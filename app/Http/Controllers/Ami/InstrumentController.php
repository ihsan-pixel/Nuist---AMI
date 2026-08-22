<?php

namespace App\Http\Controllers\Ami;

use App\Http\Controllers\Controller;
use App\Models\AmiPeriod;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstrumentController extends Controller
{
    public function index(Request $request): View
    {
        $periods = AmiPeriod::query()->orderByDesc('is_active')->orderByDesc('year')->get();
        $selectedPeriodId = $request->integer('ami_period_id') ?: $periods->first()?->id;
        $selectedPeriod = $selectedPeriodId
            ? AmiPeriod::query()->with(['standards.items.indicators' => fn ($q) => $q->orderBy('sort_order')])->find($selectedPeriodId)
            : null;

        return view('instruments.index', compact('periods', 'selectedPeriod'));
    }
}
