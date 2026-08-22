<?php

namespace App\Http\Controllers\Ami;

use App\Http\Controllers\Controller;
use App\Http\Requests\AmiPeriodStoreRequest;
use App\Http\Requests\AmiPeriodUpdateRequest;
use App\Models\AmiPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AmiPeriodController extends Controller
{
    public function index(): View
    {
        $periods = AmiPeriod::query()->latest()->paginate(10);

        return view('ami-periods.index', compact('periods'));
    }

    public function create(): View
    {
        return view('ami-periods.create');
    }

    public function store(AmiPeriodStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['created_by'] = $request->user()->id;
        foreach (['submission_start_at', 'submission_end_at', 'review_start_at', 'review_end_at'] as $field) {
            $data[$field] = AmiPeriod::normalizeDateTimeInput($data[$field] ?? null);
        }
        if ($data['is_active']) {
            DB::transaction(function () use ($data) {
                AmiPeriod::query()->update(['is_active' => false]);
                AmiPeriod::create($data);
            });
        } else {
            AmiPeriod::create($data);
        }

        return redirect()->route('admin.ami-periods.index')->with('status', 'AMI period created.');
    }

    public function edit(AmiPeriod $amiPeriod): View
    {
        return view('ami-periods.edit', ['period' => $amiPeriod]);
    }

    public function update(AmiPeriodUpdateRequest $request, AmiPeriod $amiPeriod): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        foreach (['submission_start_at', 'submission_end_at', 'review_start_at', 'review_end_at'] as $field) {
            $data[$field] = AmiPeriod::normalizeDateTimeInput($data[$field] ?? null);
        }

        DB::transaction(function () use ($amiPeriod, $data) {
            if ($data['is_active']) {
                AmiPeriod::query()->whereKeyNot($amiPeriod->id)->update(['is_active' => false]);
            }

            $amiPeriod->update($data);
        });

        return redirect()->route('admin.ami-periods.index')->with('status', 'AMI period updated.');
    }

    public function activate(AmiPeriod $amiPeriod): RedirectResponse
    {
        DB::transaction(function () use ($amiPeriod) {
            AmiPeriod::query()->update(['is_active' => false]);
            $amiPeriod->update([
                'is_active' => true,
                'status' => 'active',
            ]);
        });

        return redirect()->route('admin.ami-periods.index')->with('status', 'AMI period activated.');
    }
}
