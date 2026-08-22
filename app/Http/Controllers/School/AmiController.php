<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Http\Requests\AmiEvidenceStoreRequest;
use App\Http\Requests\AmiEvidenceUpdateRequest;
use App\Http\Requests\AmiResponseStoreRequest;
use App\Models\AmiEvidence;
use App\Models\AmiIndicator;
use App\Models\AmiPeriod;
use App\Models\AmiResponse;
use App\Models\AmiSchoolAssignment;
use App\Models\AmiStandard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AmiController extends Controller
{
    public function index(Request $request): View
    {
        $assignment = $this->currentAssignment($request);
        $period = $assignment?->period()->with(['standards.items.indicators.responses.evidences'])->first();
        $standards = $period?->standards ?? collect();
        $responses = $assignment?->responses()->with('indicator')->get() ?? collect();

        $requiredIndicators = $standards->flatMap(fn ($standard) => $standard->indicators->where('is_required', true));
        $completedCount = $requiredIndicators->filter(function ($indicator) use ($responses) {
            $response = $responses->firstWhere('ami_indicator_id', $indicator->id);
            return $response && $response->self_score !== null && filled($response->answer);
        })->count();
        $totalRequired = $requiredIndicators->count();
        $percent = $totalRequired > 0 ? (int) round(($completedCount / $totalRequired) * 100) : 0;

        return view('school.ami.index', [
            'assignment' => $assignment,
            'period' => $period,
            'standards' => $standards,
            'responses' => $responses,
            'totalRequired' => $totalRequired,
            'completedCount' => $completedCount,
            'pendingCount' => max($totalRequired - $completedCount, 0),
            'progressPercent' => $percent,
        ]);
    }

    public function standard(Request $request, AmiStandard $standard): View|RedirectResponse
    {
        $assignment = $this->assignmentForStandard($request, $standard) ?? $this->currentAssignment($request);
        abort_unless($assignment, 403);

        if ((int) $standard->ami_period_id !== (int) $assignment->ami_period_id) {
            $standard = $assignment->period->standards()->with(['items.indicators' => fn ($query) => $query->orderBy('sort_order')])->first();
            if (! $standard) {
                return redirect()->route('school.ami.index')->with('status', 'Standar yang dibuka tidak sesuai dengan assignment aktif.');
            }
        } else {
            $standard->load(['items.indicators' => fn ($query) => $query->orderBy('sort_order')]);
        }

        $standard->items->each(function ($item) use ($assignment) {
            $item->indicators->each(function (AmiIndicator $indicator) use ($assignment) {
                $indicator->setRelation('responses', $indicator->responses()->where('ami_school_assignment_id', $assignment->id)->get());
            });
        });
        $standard->indicators->each(function (AmiIndicator $indicator) use ($assignment) {
            $indicator->setRelation('responses', $indicator->responses()->where('ami_school_assignment_id', $assignment->id)->get());
        });

        return view('school.ami.standard', compact('assignment', 'standard'));
    }

    public function edit(Request $request, AmiIndicator $indicator): View|RedirectResponse
    {
        $assignment = $this->currentAssignment($request);
        if (! $assignment) {
            return redirect()->route('school.ami.index')->with('status', 'Indikator tidak tersedia untuk assignment sekolah Anda.');
        }

        $indicator->loadMissing('standard', 'item');

        $response = AmiResponse::firstOrCreate([
            'ami_school_assignment_id' => $assignment->id,
            'ami_indicator_id' => $indicator->id,
        ]);
        $response->loadMissing('evidences');

        return view('school.ami.edit', [
            'assignment' => $assignment,
            'indicator' => $indicator->load('standard'),
            'response' => $response,
        ]);
    }

    public function update(AmiResponseStoreRequest $request, AmiIndicator $indicator): RedirectResponse
    {
        $assignment = $this->currentAssignment($request);
        if (! $assignment) {
            return redirect()->route('school.ami.index')->with('status', 'Indikator tidak tersedia untuk assignment sekolah Anda.');
        }

        $indicator->loadMissing('standard');

        if ($assignment->status === 'submitted') {
            return redirect()->route('school.ami.edit', $indicator)->with('status', 'Assignment sudah submitted dan tidak bisa diedit.');
        }

        $response = AmiResponse::updateOrCreate(
            [
                'ami_school_assignment_id' => $assignment->id,
                'ami_indicator_id' => $indicator->id,
            ],
            [
                'self_score' => $request->validated('self_score'),
                'answer' => $request->validated('answer'),
                'note' => $request->validated('note'),
                'status' => $request->validated('status'),
                'saved_at' => now(),
            ]
        );

        if ($assignment->status === 'not_started') {
            $assignment->update([
                'status' => 'in_progress',
                'started_at' => $assignment->started_at ?? now(),
            ]);
        }

        return $request->input('save_action') === 'next'
            ? redirect()->route('school.ami.standard', $indicator->standard)
            : redirect()->route('school.ami.edit', $indicator);
    }

    public function evidenceStore(AmiEvidenceStoreRequest $request, AmiResponse $response): RedirectResponse
    {
        $assignment = $this->currentAssignment($request);
        abort_unless($assignment && $response->ami_school_assignment_id === $assignment->id, 403);
        abort_if($assignment->status === 'submitted', 403);

        AmiEvidence::create([
            'ami_response_id' => $response->id,
            'title' => $request->validated('title'),
            'url' => $request->validated('url'),
            'description' => $request->validated('description'),
            'sort_order' => $request->integer('sort_order', 0),
            'created_by' => $request->user()->id,
        ]);

        return back();
    }

    public function evidenceUpdate(AmiEvidenceUpdateRequest $request, AmiEvidence $evidence): RedirectResponse
    {
        $assignment = $this->currentAssignment($request);
        abort_unless($assignment && $evidence->response->ami_school_assignment_id === $assignment->id, 403);
        abort_if($assignment->status === 'submitted', 403);

        $evidence->update([
            'title' => $request->validated('title'),
            'url' => $request->validated('url'),
            'description' => $request->validated('description'),
            'sort_order' => $request->integer('sort_order', 0),
        ]);

        return back();
    }

    public function evidenceDestroy(Request $request, AmiEvidence $evidence): RedirectResponse
    {
        $assignment = $this->currentAssignment($request);
        abort_unless($assignment && $evidence->response->ami_school_assignment_id === $assignment->id, 403);
        abort_if($assignment->status === 'submitted', 403);

        $evidence->delete();

        return back();
    }

    public function review(Request $request): View
    {
        $assignment = $this->currentAssignment($request);
        abort_unless($assignment, 403);

        $assignment->load(['period.standards.items.indicators', 'responses.evidences']);
        $required = $assignment->period->standards->flatMap(fn ($standard) => $standard->indicators->where('is_required', true));
        $responses = $assignment->responses->keyBy('ami_indicator_id');
        $missing = $required->filter(fn ($indicator) => ! ($responses[$indicator->id] ?? null) || blank($responses[$indicator->id]->self_score) || blank($responses[$indicator->id]->answer));

        return view('school.ami.review', [
            'assignment' => $assignment,
            'required' => $required,
            'responses' => $responses,
            'missing' => $missing,
        ]);
    }

    public function submit(Request $request): RedirectResponse
    {
        $assignment = $this->currentAssignment($request);
        abort_unless($assignment, 403);
        abort_if($assignment->status === 'submitted', 403);

        $assignment->load(['period.standards.items.indicators', 'responses']);
        $required = $assignment->period->standards->flatMap(fn ($standard) => $standard->indicators->where('is_required', true));
        $responses = $assignment->responses->keyBy('ami_indicator_id');
        $missing = $required->filter(fn ($indicator) => ! ($responses[$indicator->id] ?? null) || blank($responses[$indicator->id]->self_score) || blank($responses[$indicator->id]->answer));

        if ($missing->isNotEmpty()) {
            return back()->withErrors(['submit' => 'Masih ada indikator wajib yang belum lengkap.']);
        }

        DB::transaction(function () use ($assignment, $responses) {
            $assignment->update([
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            $responses->each(function (AmiResponse $response) {
                $response->update(['status' => 'submitted']);
            });
        });

        return redirect()->route('school.ami.index');
    }

    protected function currentAssignment(Request $request): ?AmiSchoolAssignment
    {
        $user = $request->user();

        return AmiSchoolAssignment::query()
            ->with(['period.standards.indicators', 'responses.evidences'])
            ->where('school_id', $user?->school_id)
            ->orderByRaw("case when status = 'in_progress' then 0 when status = 'revision' then 1 when status = 'not_started' then 2 else 3 end")
            ->latest('id')
            ->first();
    }

    protected function assignmentForIndicator(Request $request, AmiIndicator $indicator): ?AmiSchoolAssignment
    {
        $user = $request->user();

        $indicator->loadMissing('standard');

        return AmiSchoolAssignment::query()
            ->with(['period.standards.indicators', 'responses.evidences'])
            ->where('school_id', $user?->school_id)
            ->where('ami_period_id', $indicator->standard->ami_period_id)
            ->orderByRaw("case when status = 'in_progress' then 0 when status = 'revision' then 1 when status = 'not_started' then 2 else 3 end")
            ->latest('id')
            ->first();
    }

    protected function assignmentForStandard(Request $request, AmiStandard $standard): ?AmiSchoolAssignment
    {
        $user = $request->user();

        return AmiSchoolAssignment::query()
            ->with(['period.standards.indicators', 'responses.evidences'])
            ->where('school_id', $user?->school_id)
            ->where('ami_period_id', $standard->ami_period_id)
            ->orderByRaw("case when status = 'in_progress' then 0 when status = 'revision' then 1 when status = 'not_started' then 2 else 3 end")
            ->latest('id')
            ->first();
    }
}
