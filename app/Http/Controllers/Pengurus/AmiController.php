<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\AmiPeriod;
use App\Models\AmiSchoolAssignment;
use App\Services\AmiMonitoringService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AmiController extends Controller
{
    public function __construct(protected AmiMonitoringService $monitoringService)
    {
    }

    public function index(Request $request): View
    {
        $periods = $this->monitoringService->periods();
        $period = $periods->firstWhere('id', $request->integer('ami_period_id'))
            ?? $periods->firstWhere('is_active', true)
            ?? $periods->first();

        abort_unless($period, 404);

        $schools = $this->monitoringService->schools($period, [
            'search' => $request->string('search')->toString(),
            'district' => $request->string('district')->toString(),
            'status' => $request->string('status')->toString(),
        ]);

        $schools->getCollection()->transform(function ($assignment) {
            $assignment->overall_status = $this->monitoringService->overallStatus($assignment);
            $assignment->overall_status_label = $this->monitoringService->labelForStatus($assignment->overall_status);
            return $assignment;
        });

        $stats = $this->monitoringService->dashboardStats($period);
        $progress = $this->monitoringService->stageProgress($period);

        return view('pengurus.ami.index', compact('periods', 'period', 'schools', 'stats', 'progress'));
    }

    public function show(Request $request, AmiSchoolAssignment $assignment): View
    {
        abort_unless($assignment->period, 404);
        $assignment->load([
            'period.standards.indicators',
            'school',
            'auditor',
            'responses.evidences',
            'assessments.indicator',
            'findings.indicator.standard',
            'followUps.evidences',
        ]);

        $status = $this->monitoringService->overallStatus($assignment);
        $label = $this->monitoringService->labelForStatus($status);
        $assignment->overall_status = $status;
        $assignment->overall_status_label = $label;

        return view('pengurus.ami.show', compact('assignment', 'status', 'label'));
    }
}
