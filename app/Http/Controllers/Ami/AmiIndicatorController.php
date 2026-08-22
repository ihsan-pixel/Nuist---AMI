<?php

namespace App\Http\Controllers\Ami;

use App\Http\Controllers\Controller;
use App\Http\Requests\AmiIndicatorStoreRequest;
use App\Http\Requests\AmiIndicatorUpdateRequest;
use App\Models\AmiIndicator;
use App\Models\AmiStandard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AmiIndicatorController extends Controller
{
    public function create(Request $request): View
    {
        $standard = AmiStandard::query()->findOrFail($request->integer('ami_standard_id'));

        return view('indicators.create', compact('standard'));
    }

    public function store(AmiIndicatorStoreRequest $request): RedirectResponse
    {
        AmiIndicator::create([
            ...$request->validated(),
            'max_score' => $request->integer('max_score', 4),
            'is_required' => $request->boolean('is_required', true),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.instruments.index', ['ami_period_id' => AmiStandard::findOrFail($request->ami_standard_id)->ami_period_id]);
    }

    public function edit(AmiIndicator $indicator): View
    {
        $standard = $indicator->standard;
        return view('indicators.edit', compact('indicator', 'standard'));
    }

    public function update(AmiIndicatorUpdateRequest $request, AmiIndicator $indicator): RedirectResponse
    {
        $indicator->update([
            ...$request->validated(),
            'max_score' => $request->integer('max_score', 4),
            'is_required' => $request->boolean('is_required', true),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.instruments.index', ['ami_period_id' => AmiStandard::findOrFail($request->ami_standard_id)->ami_period_id]);
    }
}
