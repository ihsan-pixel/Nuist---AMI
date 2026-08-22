<?php

namespace App\Services;

use App\Enums\AmiFollowUpStatus;
use App\Models\AmiFollowUp;
use App\Models\AmiPeriod;
use App\Models\AmiSchoolAssignment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class AmiMonitoringService
{
    public function periods()
    {
        return AmiPeriod::query()->orderByDesc('is_active')->orderByDesc('year')->get();
    }

    public function dashboardStats(AmiPeriod $period): array
    {
        $assignments = $this->baseAssignmentsQuery($period)->get();
        $followUps = $this->followUpsQuery($period)->get();

        return [
            'total_schools' => $assignments->count(),
            'not_started' => $assignments->where('status', 'not_started')->count(),
            'in_progress' => $assignments->where('status', 'in_progress')->count(),
            'submitted' => $assignments->where('status', 'submitted')->count(),
            'audit_in_progress' => $assignments->where('audit_status', 'in_progress')->count(),
            'audit_completed' => $assignments->where('audit_status', 'completed')->count(),
            'follow_up_pending' => $followUps->filter(fn (AmiFollowUp $followUp) => in_array($followUp->status->value, [AmiFollowUpStatus::DRAFT->value, AmiFollowUpStatus::SUBMITTED->value, AmiFollowUpStatus::NEEDS_REVISION->value], true))->count(),
            'follow_up_done' => $followUps->filter(fn (AmiFollowUp $followUp) => $followUp->status->value === AmiFollowUpStatus::ACCEPTED->value)->count(),
        ];
    }

    public function stageProgress(AmiPeriod $period): array
    {
        $assignments = $this->baseAssignmentsQuery($period)->get();
        $total = max($assignments->count(), 1);

        $schoolProgress = (int) round($assignments->filter(fn ($assignment) => in_array($assignment->status, ['in_progress', 'submitted', 'completed'], true))->count() / $total * 100);
        $auditProgress = (int) round($assignments->filter(fn ($assignment) => in_array($assignment->audit_status, ['in_progress', 'completed'], true))->count() / $total * 100);
        $followUpProgress = (int) round($this->followUpsQuery($period)->whereIn('status', [AmiFollowUpStatus::ACCEPTED->value])->count() / max($this->followUpsQuery($period)->count(), 1) * 100);

        return [
            'school' => $schoolProgress,
            'audit' => $auditProgress,
            'follow_up' => $followUpProgress,
        ];
    }

    public function schools(AmiPeriod $period, array $filters = []): LengthAwarePaginator
    {
        return $this->baseAssignmentsQuery($period)
            ->with(['school', 'auditor', 'period', 'followUps.evidences', 'findings.followUp'])
            ->when($filters['search'] ?? null, function (Builder $query, string $search) {
                $query->whereHas('school', fn (Builder $schoolQuery) => $schoolQuery
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('scod', 'like', "%{$search}%"));
            })
            ->when($filters['district'] ?? null, fn (Builder $query, string $district) => $query->whereHas('school', fn (Builder $schoolQuery) => $schoolQuery->where('district', $district)))
            ->when($filters['status'] ?? null, function (Builder $query, string $status) {
                $query->when($status === 'not_started', fn (Builder $q) => $q->where('status', 'not_started'))
                    ->when($status === 'filling', fn (Builder $q) => $q->where('status', 'in_progress'))
                    ->when($status === 'submitted', fn (Builder $q) => $q->where('status', 'submitted'))
                    ->when($status === 'audit_in_progress', fn (Builder $q) => $q->where('audit_status', 'in_progress'))
                    ->when($status === 'audit_completed', fn (Builder $q) => $q->where('audit_status', 'completed'))
                    ->when($status === 'follow_up_required', fn (Builder $q) => $q->whereHas('findings', fn (Builder $findingQuery) => $findingQuery->whereHas('followUp', fn (Builder $followUpQuery) => $followUpQuery->whereIn('status', [AmiFollowUpStatus::DRAFT->value, AmiFollowUpStatus::SUBMITTED->value, AmiFollowUpStatus::NEEDS_REVISION->value]))))
                    ->when($status === 'completed', fn (Builder $q) => $q->whereHas('followUps', fn (Builder $followUpQuery) => $followUpQuery->where('status', AmiFollowUpStatus::ACCEPTED->value)));
            })
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();
    }

    public function findings(AmiPeriod $period, array $filters = []): LengthAwarePaginator
    {
        return \App\Models\AmiFinding::query()
            ->with(['assignment.school', 'indicator.standard', 'auditor', 'followUp'])
            ->whereHas('assignment', fn (Builder $query) => $query->where('ami_period_id', $period->id))
            ->when($filters['district'] ?? null, fn (Builder $query, string $district) => $query->whereHas('assignment.school', fn (Builder $schoolQuery) => $schoolQuery->where('district', $district)))
            ->when($filters['type'] ?? null, fn (Builder $query, string $type) => $query->where('type', $type))
            ->when($filters['follow_up_status'] ?? null, fn (Builder $query, string $status) => $query->whereHas('followUp', fn (Builder $followUpQuery) => $followUpQuery->where('status', $status)))
            ->when($filters['search'] ?? null, function (Builder $query, string $search) {
                $query->where(function (Builder $inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('indicator', fn (Builder $indicatorQuery) => $indicatorQuery->where('code', 'like', "%{$search}%"));
                });
            })
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();
    }

    public function overallStatus(AmiSchoolAssignment $assignment): string
    {
        if ($assignment->audit_status === 'completed' && $this->isFollowUpCompleted($assignment)) {
            return 'completed';
        }

        if ($assignment->audit_status === 'completed') {
            return $assignment->followUps->contains(fn (AmiFollowUp $followUp) => in_array($followUp->status->value, [AmiFollowUpStatus::DRAFT->value, AmiFollowUpStatus::SUBMITTED->value, AmiFollowUpStatus::NEEDS_REVISION->value], true))
                ? 'follow_up_in_progress'
                : 'follow_up_required';
        }

        if ($assignment->audit_status === 'in_progress') {
            return 'audit_in_progress';
        }

        if ($assignment->status === 'submitted') {
            return 'submitted';
        }

        if ($assignment->status === 'in_progress') {
            return 'filling';
        }

        return 'not_started';
    }

    public function labelForStatus(string $status): string
    {
        return match ($status) {
            'not_started' => 'Belum Mulai',
            'filling' => 'Sedang Mengisi',
            'submitted' => 'Menunggu Audit',
            'audit_in_progress' => 'Sedang Diaudit',
            'audit_completed' => 'Audit Selesai',
            'follow_up_required' => 'Perlu Tindak Lanjut',
            'follow_up_in_progress' => 'Tindak Lanjut Berjalan',
            'completed' => 'Selesai',
            default => 'Belum Mulai',
        };
    }

    protected function baseAssignmentsQuery(AmiPeriod $period): Builder
    {
        return AmiSchoolAssignment::query()->where('ami_period_id', $period->id);
    }

    protected function followUpsQuery(AmiPeriod $period): Builder
    {
        return AmiFollowUp::query()->whereHas('assignment', fn (Builder $query) => $query->where('ami_period_id', $period->id));
    }

    protected function isFollowUpCompleted(AmiSchoolAssignment $assignment): bool
    {
        $followUps = $assignment->followUps;
        if ($followUps->isEmpty()) {
            return false;
        }

        return $followUps->every(fn (AmiFollowUp $followUp) => $followUp->status->value === AmiFollowUpStatus::ACCEPTED->value || ! $followUp->finding->requiresFollowUp());
    }
}
