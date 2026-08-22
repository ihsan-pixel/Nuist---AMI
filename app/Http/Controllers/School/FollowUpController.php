<?php

namespace App\Http\Controllers\School;

use App\Enums\AmiFollowUpStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\AmiFollowUpEvidenceStoreRequest;
use App\Http\Requests\AmiFollowUpStoreRequest;
use App\Models\AmiFinding;
use App\Models\AmiFollowUp;
use App\Models\AmiFollowUpEvidence;
use App\Models\AmiSchoolAssignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class FollowUpController extends Controller
{
    public function index(Request $request): View
    {
        $assignment = $this->currentCompletedAssignment($request);
        $followUps = $assignment
            ? $assignment->followUps()->with(['finding.indicator.standard', 'evidences'])->get()
            : collect();

        $stats = [
            'total' => $followUps->count(),
            'draft' => $followUps->where('status.value', AmiFollowUpStatus::DRAFT->value)->count(),
            'submitted' => $followUps->where('status.value', AmiFollowUpStatus::SUBMITTED->value)->count(),
            'needs_revision' => $followUps->where('status.value', AmiFollowUpStatus::NEEDS_REVISION->value)->count(),
            'accepted' => $followUps->where('status.value', AmiFollowUpStatus::ACCEPTED->value)->count(),
            'pending' => $followUps->where('status.value', AmiFollowUpStatus::DRAFT->value)->count() + $followUps->where('status.value', AmiFollowUpStatus::NEEDS_REVISION->value)->count(),
        ];

        return view('school.ami.follow-ups.index', compact('assignment', 'followUps', 'stats'));
    }

    public function show(Request $request, AmiFollowUp $followUp): View
    {
        Gate::authorize('view', $followUp);
        $followUp->load(['finding.indicator.standard', 'assignment.period', 'evidences']);
        return view('school.ami.follow-ups.show', compact('followUp'));
    }

    public function store(AmiFollowUpStoreRequest $request, AmiFinding $finding): RedirectResponse
    {
        $followUp = $this->followUpForSchool($request, $finding);
        Gate::authorize('manage', $followUp);

        $followUp->fill($request->validated());
        $followUp->save();

        if ($request->validated('status') === AmiFollowUpStatus::SUBMITTED->value) {
            $this->submitFollowUp($followUp);
            return back()->with('status', 'Tindak lanjut terkirim.');
        }

        return back()->with('status', 'Draft tersimpan.');
    }

    public function evidenceStore(AmiFollowUpEvidenceStoreRequest $request, AmiFollowUp $followUp): RedirectResponse
    {
        Gate::authorize('manage', $followUp);

        AmiFollowUpEvidence::create([
            'ami_follow_up_id' => $followUp->id,
            'title' => $request->validated('title'),
            'url' => $request->validated('url'),
            'note' => $request->validated('note'),
        ]);

        return back();
    }

    public function evidenceDestroy(Request $request, AmiFollowUpEvidence $evidence): RedirectResponse
    {
        Gate::authorize('manage', $evidence->followUp);
        $evidence->delete();
        return back();
    }

    public function submit(Request $request, AmiFollowUp $followUp): RedirectResponse
    {
        Gate::authorize('view', $followUp);
        $this->submitFollowUp($followUp);
        return back()->with('status', 'Tindak lanjut terkirim.');
    }

    protected function submitFollowUp(AmiFollowUp $followUp): void
    {
        abort_if($followUp->assignment->audit_status !== 'completed', 422);
        abort_if(blank($followUp->action_plan), 422);
        if ($followUp->finding->requiresFollowUp()) {
            abort_if($followUp->evidences()->count() < 1, 422);
        }
        DB::transaction(function () use ($followUp) {
            $followUp->update([
                'status' => AmiFollowUpStatus::SUBMITTED->value,
                'submitted_at' => now(),
            ]);
        });
    }

    protected function currentCompletedAssignment(Request $request)
    {
        return AmiSchoolAssignment::query()
            ->where('school_id', $request->user()?->school_id)
            ->where('audit_status', 'completed')
            ->latest('id')
            ->first();
    }

    protected function followUpForSchool(Request $request, AmiFinding $finding): AmiFollowUp
    {
        $assignment = $this->currentCompletedAssignment($request);
        abort_unless($assignment && (int) $finding->ami_school_assignment_id === (int) $assignment->id, 403);
        abort_unless($finding->requiresFollowUp(), 422);

        return AmiFollowUp::firstOrNew([
            'ami_finding_id' => $finding->id,
        ], [
            'ami_school_assignment_id' => $assignment->id,
            'school_id' => $assignment->school_id,
            'status' => AmiFollowUpStatus::DRAFT->value,
        ]);
    }
}
