<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\AmiPeriod;
use App\Services\AmiMonitoringService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FindingsController extends Controller
{
    public function __construct(protected AmiMonitoringService $monitoringService)
    {
    }

    public function index(Request $request): View
    {
        $period = AmiPeriod::query()->where('is_active', true)->first() ?? AmiPeriod::query()->latest('id')->firstOrFail();
        $findings = $this->monitoringService->findings($period, [
            'district' => $request->string('district')->toString(),
            'type' => $request->string('type')->toString(),
            'follow_up_status' => $request->string('follow_up_status')->toString(),
            'search' => $request->string('search')->toString(),
        ]);

        return view('pengurus.findings.index', compact('period', 'findings'));
    }
}
