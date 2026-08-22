<?php

namespace App\Http\Controllers\Ami;

use App\Http\Controllers\Controller;
use App\Http\Requests\AmiStandardStoreRequest;
use App\Http\Requests\AmiStandardUpdateRequest;
use App\Models\AmiPeriod;
use App\Models\AmiStandard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AmiStandardController extends Controller
{
    public function create(Request $request): View
    {
        $periods = AmiPeriod::query()->orderByDesc('is_active')->orderByDesc('year')->get();
        return view('standards.create', ['periods' => $periods, 'selectedPeriodId' => $request->integer('ami_period_id')]);
    }

    public function store(AmiStandardStoreRequest $request): RedirectResponse
    {
        AmiStandard::create([
            ...$request->validated(),
            'is_active' => (bool) ($request->boolean('is_active')),
        ]);

        return redirect()->route('admin.instruments.index', ['ami_period_id' => $request->ami_period_id]);
    }

    public function edit(AmiStandard $standard): View
    {
        $periods = AmiPeriod::query()->orderByDesc('is_active')->orderByDesc('year')->get();
        return view('standards.edit', compact('standard', 'periods'));
    }

    public function update(AmiStandardUpdateRequest $request, AmiStandard $standard): RedirectResponse
    {
        $standard->update([
            ...$request->validated(),
            'is_active' => (bool) $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.instruments.index', ['ami_period_id' => $request->ami_period_id]);
    }
}
