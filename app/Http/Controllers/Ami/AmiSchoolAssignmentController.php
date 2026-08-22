<?php

namespace App\Http\Controllers\Ami;

use App\Http\Controllers\Controller;
use App\Http\Requests\AmiSchoolAssignmentStoreRequest;
use App\Models\AmiIndicator;
use App\Models\AmiPeriod;
use App\Models\AmiSchoolAssignment;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AmiSchoolAssignmentController extends Controller
{
    public function index(Request $request): View
    {
        $periods = AmiPeriod::query()->orderByDesc('is_active')->orderByDesc('year')->get();
        $selectedPeriod = $request->integer('ami_period_id')
            ? AmiPeriod::query()->with(['assignments.school', 'standards.indicators'])->find($request->integer('ami_period_id'))
            : $periods->first();

        $schools = School::query()->orderBy('name')->get();
        $assignments = $selectedPeriod?->assignments()->with('school')->get() ?? collect();

        return view('assignments.index', compact('periods', 'selectedPeriod', 'schools', 'assignments'));
    }

    public function store(AmiSchoolAssignmentStoreRequest $request): RedirectResponse
    {
        $period = AmiPeriod::findOrFail($request->integer('ami_period_id'));
        $schoolIds = collect($request->validated('school_ids'))->unique()->values();

        DB::transaction(function () use ($period, $schoolIds) {
            foreach ($schoolIds as $schoolId) {
                AmiSchoolAssignment::firstOrCreate(
                    ['ami_period_id' => $period->id, 'school_id' => $schoolId],
                    ['status' => 'not_started']
                );
            }
        });

        return redirect()->route('admin.assignments.index', ['ami_period_id' => $period->id]);
    }
}
