<?php

namespace App\Http\Controllers\Auditor;

use App\Enums\AuditorFindingType;
use App\Http\Controllers\Controller;
use App\Http\Requests\AmiAssessmentStoreRequest;
use App\Http\Requests\AmiAssessmentUpdateRequest;
use App\Http\Requests\AmiFindingStoreRequest;
use App\Http\Requests\AmiFindingUpdateRequest;
use App\Models\AmiAssessment;
use App\Models\AmiFinding;
use App\Models\AmiIndicator;
use App\Models\AmiSchoolAssignment;
use App\Services\AmiAuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AmiController extends Controller
{
    public function __construct(protected AmiAuditService $auditService)
    {
    }

    public function index(Request $request): View
    {
        $assignments = AmiSchoolAssignment::query()
            ->with(['period', 'school', 'assessments', 'findings'])
            ->forAuditor($request->user()->id)
            ->latest('id')
            ->get();

        $stats = [
            'total' => $assignments->count(),
            'not_started' => $assignments->where('audit_status', 'not_started')->count(),
            'in_progress' => $assignments->where('audit_status', 'in_progress')->count(),
            'completed' => $assignments->where('audit_status', 'completed')->count(),
        ];

        return view('auditor.ami.index', compact('assignments', 'stats'));
    }

    public function show(Request $request, AmiSchoolAssignment $assignment): View
    {
        $this->authorizeAssignment($request, $assignment);
        $assignment->load(['period.standards.indicators', 'school', 'assessments', 'findings']);
        $stats = $this->auditService->stats($assignment);

        return view('auditor.ami.show', compact('assignment', 'stats'));
    }

    public function standard(Request $request, AmiSchoolAssignment $assignment, AmiIndicator $indicator): View
    {
        $this->authorizeAssignment($request, $assignment);
        abort_unless($indicator->standard->ami_period_id === $assignment->ami_period_id, 403);
        $assignment->load(['school', 'period', 'assessments.indicator', 'findings.indicator', 'responses.evidences']);

        $response = $assignment->responses->firstWhere('ami_indicator_id', $indicator->id);
        $assessment = $assignment->assessments->firstWhere('ami_indicator_id', $indicator->id);
        $findings = $assignment->findings->filter(fn ($finding) => $finding->ami_indicator_id === null || (int) $finding->ami_indicator_id === (int) $indicator->id);

        return view('auditor.ami.standard', compact('assignment', 'indicator', 'response', 'assessment', 'findings'));
    }

    public function storeAssessment(AmiAssessmentStoreRequest $request, AmiSchoolAssignment $assignment, AmiIndicator $indicator): RedirectResponse
    {
        $this->authorizeAssignment($request, $assignment);
        abort_unless($indicator->standard->ami_period_id === $assignment->ami_period_id, 403);
        $assessment = AmiAssessment::updateOrCreate(
            ['ami_school_assignment_id' => $assignment->id, 'ami_indicator_id' => $indicator->id],
            [
                'auditor_id' => $request->user()->id,
                'status' => $request->string('status')->toString(),
                'rating' => $request->string('rating')->toString() ?: null,
                'score' => $request->input('score'),
                'auditor_note' => $request->input('auditor_note'),
                'verification_methods' => $request->input('verification_methods', []),
                'verification_note' => $request->input('verification_note'),
                'assessed_at' => now(),
            ]
        );

        if ($assignment->audit_status === 'not_started') {
            $assignment->update(['audit_status' => 'in_progress', 'audit_started_at' => $assignment->audit_started_at ?? now()]);
        }

        return back()->with('status', 'Assessment saved.');
    }

    public function updateAssessment(AmiAssessmentUpdateRequest $request, AmiAssessment $assessment): RedirectResponse
    {
        $this->authorizeAssignment($request, $assessment->assignment);
        Gate::authorize('manage', $assessment);
        $assessment->update([
            'status' => $request->string('status')->toString(),
            'rating' => $request->string('rating')->toString() ?: null,
            'score' => $request->input('score'),
            'auditor_note' => $request->input('auditor_note'),
            'verification_methods' => $request->input('verification_methods', []),
            'verification_note' => $request->input('verification_note'),
            'assessed_at' => now(),
        ]);
        return back()->with('status', 'Assessment updated.');
    }

    public function storeFinding(AmiFindingStoreRequest $request, AmiSchoolAssignment $assignment, ?AmiIndicator $indicator = null): RedirectResponse
    {
        $this->authorizeAssignment($request, $assignment);
        if ($indicator) {
            abort_unless($indicator->standard->ami_period_id === $assignment->ami_period_id, 403);
        }
        AmiFinding::create([
            'ami_school_assignment_id' => $assignment->id,
            'ami_indicator_id' => $indicator?->id,
            'auditor_id' => $request->user()->id,
            'type' => $request->input('type'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'recommendation' => $request->input('recommendation'),
            'status' => 'open',
        ]);
        if ($assignment->audit_status === 'not_started') {
            $assignment->update(['audit_status' => 'in_progress', 'audit_started_at' => $assignment->audit_started_at ?? now()]);
        }
        return back()->with('status', 'Finding saved.');
    }

    public function updateFinding(AmiFindingUpdateRequest $request, AmiFinding $finding): RedirectResponse
    {
        $this->authorizeAssignment($request, $finding->assignment);
        Gate::authorize('manage', $finding);
        $finding->update($request->validated());
        return back()->with('status', 'Finding updated.');
    }

    public function destroyFinding(Request $request, AmiFinding $finding): RedirectResponse
    {
        $this->authorizeAssignment($request, $finding->assignment);
        Gate::authorize('manage', $finding);
        $finding->delete();
        return back()->with('status', 'Finding deleted.');
    }

    public function review(Request $request, AmiSchoolAssignment $assignment): View
    {
        $this->authorizeAssignment($request, $assignment);
        $stats = $this->auditService->stats($assignment);
        $canComplete = $this->auditService->canComplete($assignment);
        $assignment->load(['period', 'school', 'assessments', 'findings']);
        return view('auditor.ami.review', compact('assignment', 'stats', 'canComplete'));
    }

    public function complete(Request $request, AmiSchoolAssignment $assignment): RedirectResponse
    {
        $this->authorizeAssignment($request, $assignment);
        abort_if($assignment->audit_status === 'completed', 403);
        abort_unless($this->auditService->canComplete($assignment), 422);
        $this->auditService->complete($assignment, $request->user()->id);
        return redirect()->route('auditor.ami.show', $assignment)->with('status', 'Audit completed.');
    }

    protected function authorizeAssignment(Request $request, AmiSchoolAssignment $assignment): void
    {
        abort_unless($request->user()->role === 'auditor', 403);
        abort_unless((int) $assignment->auditor_id === (int) $request->user()->id, 403);
    }
}
