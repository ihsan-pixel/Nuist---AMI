<?php

namespace App\Services;

use App\Enums\AmiAssessmentRating;
use App\Enums\AuditorAssessmentStatus;
use App\Models\AmiSchoolAssignment;
use Illuminate\Support\Facades\DB;

class AmiAuditService
{
    public function stats(AmiSchoolAssignment $assignment): array
    {
        $assignment->loadMissing(['period.standards.indicators', 'assessments', 'findings']);
        $indicators = $assignment->period->standards->flatMap(fn ($standard) => $standard->indicators);
        $assessments = $assignment->assessments;

        return [
            'total_indicators' => $indicators->count(),
            'assessed' => $assessments->count(),
            'unassessed' => max($indicators->count() - $assessments->count(), 0),
            'conform' => $assessments->where('status', AuditorAssessmentStatus::CONFORM)->count(),
            'partially_conform' => $assessments->where('status', AuditorAssessmentStatus::PARTIALLY_CONFORM)->count(),
            'non_conform' => $assessments->where('status', AuditorAssessmentStatus::NON_CONFORM)->count(),
            'kurang' => $assessments->where('rating', AmiAssessmentRating::KURANG)->count(),
            'cukup_baik' => $assessments->where('rating', AmiAssessmentRating::CUKUP_BAIK)->count(),
            'baik' => $assessments->where('rating', AmiAssessmentRating::BAIK)->count(),
            'sangat_baik' => $assessments->where('rating', AmiAssessmentRating::SANGAT_BAIK)->count(),
            'findings' => $assignment->findings->count(),
            'observation' => $assignment->findings->where('type', 'observation')->count(),
            'minor' => $assignment->findings->where('type', 'minor')->count(),
            'major' => $assignment->findings->where('type', 'major')->count(),
        ];
    }

    public function canComplete(AmiSchoolAssignment $assignment): bool
    {
        $assignment->loadMissing(['period.standards.indicators', 'assessments']);
        $required = $assignment->period->standards->flatMap(fn ($standard) => $standard->indicators->where('is_required', true));
        $assessedIds = $assignment->assessments->pluck('ami_indicator_id')->all();

        foreach ($required as $indicator) {
            if (! in_array($indicator->id, $assessedIds, true)) {
                return false;
            }
        }

        return true;
    }

    public function complete(AmiSchoolAssignment $assignment, int $completedBy): void
    {
        DB::transaction(function () use ($assignment, $completedBy) {
            $assignment->update([
                'audit_status' => 'completed',
                'audit_completed_at' => now(),
                'audit_completed_by' => $completedBy,
            ]);
        });
    }
}
