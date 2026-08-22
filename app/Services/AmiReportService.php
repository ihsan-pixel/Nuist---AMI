<?php

namespace App\Services;

use App\Enums\AmiFollowUpStatus;
use App\Models\AmiFollowUp;
use App\Models\AmiFinding;
use App\Models\AmiPeriod;
use App\Models\AmiSchoolAssignment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class AmiReportService
{
    public function summary(AmiPeriod $period): array
    {
        $assignments = $this->assignments($period)->get();
        $findings = $this->findingsQuery($period)->get();
        $followUps = $this->followUpsQuery($period)->get();

        return [
            'total_schools' => $assignments->count(),
            'not_submitted' => $assignments->where('status', 'not_started')->count(),
            'submitted' => $assignments->where('status', 'submitted')->count(),
            'audit_completed' => $assignments->where('audit_status', 'completed')->count(),
            'observation' => $findings->where('type', 'observation')->count(),
            'minor' => $findings->where('type', 'minor')->count(),
            'major' => $findings->where('type', 'major')->count(),
            'follow_up_pending' => $followUps->filter(fn (AmiFollowUp $followUp) => in_array($followUp->status->value, [AmiFollowUpStatus::DRAFT->value, AmiFollowUpStatus::SUBMITTED->value, AmiFollowUpStatus::NEEDS_REVISION->value], true))->count(),
            'follow_up_accepted' => $followUps->filter(fn (AmiFollowUp $followUp) => $followUp->status->value === AmiFollowUpStatus::ACCEPTED->value)->count(),
        ];
    }

    public function schools(AmiPeriod $period, array $filters = []): LengthAwarePaginator
    {
        return $this->assignments($period)
            ->with(['school', 'auditor', 'period', 'responses', 'assessments', 'findings.followUp', 'followUps.evidences'])
            ->when($filters['district'] ?? null, fn (Builder $query, string $district) => $query->whereHas('school', fn (Builder $schoolQuery) => $schoolQuery->where('district', $district)))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $this->applyStatusFilter($query, $status))
            ->when($filters['search'] ?? null, fn (Builder $query, string $search) => $query->whereHas('school', fn (Builder $schoolQuery) => $schoolQuery->where('name', 'like', "%{$search}%")->orWhere('scod', 'like', "%{$search}%")))
            ->paginate(20)
            ->withQueryString();
    }

    public function findings(AmiPeriod $period, array $filters = []): LengthAwarePaginator
    {
        return $this->findingsQuery($period)
            ->with(['assignment.school', 'assignment.auditor', 'indicator.standard', 'auditor', 'followUp.evidences'])
            ->whereHas('assignment', fn (Builder $query) => $query->where('ami_period_id', $period->id))
            ->when($filters['district'] ?? null, fn (Builder $query, string $district) => $query->whereHas('assignment.school', fn (Builder $schoolQuery) => $schoolQuery->where('district', $district)))
            ->when($filters['type'] ?? null, fn (Builder $query, string $type) => $query->where('type', $type))
            ->when($filters['follow_up_status'] ?? null, fn (Builder $query, string $status) => $query->whereHas('followUp', fn (Builder $followUpQuery) => $followUpQuery->where('status', $status)))
            ->when($filters['search'] ?? null, fn (Builder $query, string $search) => $query->where(function (Builder $inner) use ($search) {
                $inner->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('indicator', fn (Builder $indicatorQuery) => $indicatorQuery->where('code', 'like', "%{$search}%"));
            }))
            ->paginate(20)
            ->withQueryString();
    }

    protected function assignments(AmiPeriod $period): Builder
    {
        return AmiSchoolAssignment::query()->where('ami_period_id', $period->id);
    }

    protected function findingsQuery(AmiPeriod $period): Builder
    {
        return \App\Models\AmiFinding::query()->whereHas('assignment', fn (Builder $query) => $query->where('ami_period_id', $period->id));
    }

    protected function followUpsQuery(AmiPeriod $period): Builder
    {
        return AmiFollowUp::query()->whereHas('assignment', fn (Builder $query) => $query->where('ami_period_id', $period->id));
    }

    protected function applyStatusFilter(Builder $query, string $status): void
    {
        $query->when($status === 'not_started', fn (Builder $q) => $q->where('status', 'not_started'))
            ->when($status === 'submitted', fn (Builder $q) => $q->where('status', 'submitted'))
            ->when($status === 'audit_completed', fn (Builder $q) => $q->where('audit_status', 'completed'))
            ->when($status === 'completed', fn (Builder $q) => $q->whereHas('followUps', fn (Builder $followUpQuery) => $followUpQuery->where('status', AmiFollowUpStatus::ACCEPTED->value)));
    }
}
