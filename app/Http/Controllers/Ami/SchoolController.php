<?php

namespace App\Http\Controllers\Ami;

use App\Http\Controllers\Controller;
use App\Http\Requests\SchoolStoreRequest;
use App\Http\Requests\SchoolUpdateRequest;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SchoolController extends Controller
{
    public function index(Request $request): View
    {
        $query = School::query();

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('scod', 'like', "%{$search}%");
            });
        }

        if ($district = $request->string('district')->trim()->toString()) {
            $query->where('district', $district);
        }

        if ($level = $request->string('education_level')->trim()->toString()) {
            $query->where('education_level', $level);
        }

        if ($status = $request->string('status')->trim()->toString()) {
            $query->where('status', $status);
        }

        $schools = $query->orderBy('name')->paginate(10)->withQueryString();
        $districts = School::query()->whereNotNull('district')->distinct()->orderBy('district')->pluck('district');
        $levels = School::query()->whereNotNull('education_level')->distinct()->orderBy('education_level')->pluck('education_level');

        $stats = [
            'total' => School::count(),
            'active' => School::where('status', 'active')->count(),
            'inactive' => School::where('status', 'inactive')->count(),
        ];

        return view('schools.index', compact('schools', 'stats', 'districts', 'levels'));
    }

    public function create(): View
    {
        return view('schools.create');
    }

    public function store(SchoolStoreRequest $request): RedirectResponse
    {
        School::create($request->validated());

        return redirect()->route('admin.schools.index')->with('status', 'School created.');
    }

    public function edit(School $school): View
    {
        return view('schools.edit', compact('school'));
    }

    public function update(SchoolUpdateRequest $request, School $school): RedirectResponse
    {
        $school->update($request->validated());

        return redirect()->route('admin.schools.index')->with('status', 'School updated.');
    }

    public function destroy(School $school): RedirectResponse
    {
        if ($school->users()->exists()) {
            $school->update(['status' => 'inactive']);

            return redirect()->route('admin.schools.index')->with('status', 'School has related users, marked inactive.');
        }

        $school->delete();

        return redirect()->route('admin.schools.index')->with('status', 'School deleted.');
    }
}
