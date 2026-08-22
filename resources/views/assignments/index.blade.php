<x-app-layout>
    <div class="mb-6 flex items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Penugasan AMI</h1>
            <p class="mt-1 text-sm text-slate-500">Tetapkan sekolah untuk periode AMI.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.assignments.index') }}" class="mb-4 max-w-sm">
        <select name="ami_period_id" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2" onchange="this.form.submit()">
            @foreach ($periods as $period)
                <option value="{{ $period->id }}" @selected($selectedPeriod?->id === $period->id)>{{ $period->name }} ({{ $period->year }})</option>
            @endforeach
        </select>
    </form>

    @if ($selectedPeriod)
        <div class="mb-4 grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl bg-white p-4 shadow-sm">Sekolah: {{ $assignments->count() }}</div>
            <div class="rounded-2xl bg-white p-4 shadow-sm">Standar: {{ $selectedPeriod->standards->count() }}</div>
            <div class="rounded-2xl bg-white p-4 shadow-sm">Indikator: {{ $selectedPeriod->standards->sum(fn ($standard) => $standard->indicators->count()) }}</div>
        </div>

        <form method="POST" action="{{ route('admin.assignments.store') }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            @csrf
            <input type="hidden" name="ami_period_id" value="{{ $selectedPeriod->id }}">
            <div class="mb-4 grid gap-3 md:grid-cols-2">
                @foreach ($schools as $school)
                    @php
                        $assignment = $assignments->firstWhere('school_id', $school->id);
                    @endphp
                    <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-3">
                        <input type="checkbox" name="school_ids[]" value="{{ $school->id }}" @checked(! $assignment) {{ $assignment ? 'disabled' : '' }}>
                        <div class="text-sm">
                            <div class="font-medium">{{ $school->name }}</div>
                            <div class="text-slate-500">{{ $school->scod }} | {{ $school->education_level }} | {{ $school->district }}</div>
                            <div class="mt-1 text-xs {{ $assignment ? 'text-amber-600' : 'text-slate-500' }}">
                                {{ $assignment ? 'Sudah ditugaskan' : 'Belum ditugaskan' }}
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>
            <button class="rounded-xl bg-[#00553F] px-4 py-2 text-sm font-medium text-white">Tetapkan Sekolah</button>
        </form>

        <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="mb-3 text-sm font-medium text-slate-900">Detail Assignment</div>
            <div class="space-y-3">
                @foreach ($assignments as $assignment)
                    <div class="rounded-xl border border-slate-200 p-4">
                        <div class="font-medium">{{ $assignment->school->name }}</div>
                        <div class="text-sm text-slate-500">
                            Periode {{ $selectedPeriod->name }} | Standar {{ $selectedPeriod->standards->count() }} | Indikator {{ $selectedPeriod->standards->sum(fn ($standard) => $standard->indicators->count()) }} | Status {{ $assignment->status }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</x-app-layout>
