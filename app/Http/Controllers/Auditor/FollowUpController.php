<?php

namespace App\Http\Controllers\Auditor;

use App\Enums\AmiFollowUpStatus;
use App\Http\Controllers\Controller;
use App\Models\AmiFollowUp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class FollowUpController extends Controller
{
    public function index(Request $request): View
    {
        $followUps = AmiFollowUp::query()
            ->with(['finding.indicator.standard', 'assignment.school', 'evidences'])
            ->whereHas('assignment', fn ($query) => $query->where('auditor_id', $request->user()->id))
            ->latest('id')
            ->get();

        $stats = [
            'total' => $followUps->count(),
            'submitted' => $followUps->where('status.value', AmiFollowUpStatus::SUBMITTED->value)->count(),
            'needs_revision' => $followUps->where('status.value', AmiFollowUpStatus::NEEDS_REVISION->value)->count(),
            'accepted' => $followUps->where('status.value', AmiFollowUpStatus::ACCEPTED->value)->count(),
        ];

        return view('auditor.follow-ups.index', compact('followUps', 'stats'));
    }

    public function show(Request $request, AmiFollowUp $followUp): View
    {
        Gate::authorize('view', $followUp);
        $followUp->load(['finding.indicator.standard', 'assignment.period', 'assignment.school', 'evidences', 'verifier']);
        return view('auditor.follow-ups.show', compact('followUp'));
    }

    public function accept(Request $request, AmiFollowUp $followUp): RedirectResponse
    {
        Gate::authorize('verify', $followUp);
        $this->verify($followUp, AmiFollowUpStatus::ACCEPTED->value, null, $request->user()->id);
        return back()->with('status', 'Tindak lanjut diterima.');
    }

    public function revision(Request $request, AmiFollowUp $followUp): RedirectResponse
    {
        Gate::authorize('verify', $followUp);
        $request->validate(['verifier_note' => ['required', 'string']]);
        $this->verify($followUp, AmiFollowUpStatus::NEEDS_REVISION->value, $request->string('verifier_note')->toString(), $request->user()->id);
        return back()->with('status', 'Tindak lanjut dikembalikan untuk revisi.');
    }

    protected function verify(AmiFollowUp $followUp, string $status, ?string $note, int $userId): void
    {
        DB::transaction(function () use ($followUp, $status, $note, $userId) {
            $followUp->update([
                'status' => $status,
                'verified_at' => now(),
                'verified_by' => $userId,
                'verifier_note' => $note,
            ]);
        });
    }
}
