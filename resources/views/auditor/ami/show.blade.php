<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900">{{ $assignment->school->name }}</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $assignment->school->scod }} | {{ $assignment->period->name }}</p>
    </div>

    <div class="mb-4 grid gap-4 md:grid-cols-4">
        <div class="rounded-2xl bg-white p-4 shadow-sm">
            <div class="text-sm text-slate-500">Total indikator</div>
            <div class="text-2xl font-semibold">{{ $stats['total_indicators'] }}</div>
        </div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">
            <div class="text-sm text-slate-500">Dinilai</div>
            <div class="text-2xl font-semibold">{{ $stats['assessed'] }}</div>
        </div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">
            <div class="text-sm text-slate-500">Belum dinilai</div>
            <div class="text-2xl font-semibold">{{ $stats['unassessed'] }}</div>
        </div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">
            <div class="text-sm text-slate-500">Temuan</div>
            <div class="text-2xl font-semibold">{{ $stats['findings'] }}</div>
        </div>
    </div>

    <div class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <div class="text-sm text-slate-500">Status audit</div>
                <div class="font-medium">{{ $assignment->audit_status ?? 'not_started' }}</div>
            </div>
            <a href="{{ route('auditor.ami.review', $assignment) }}" class="rounded-xl bg-[#00553F] px-4 py-2 text-sm font-medium text-white">Review & Finalisasi</a>
        </div>
    </div>

    <div class="space-y-3">
        @foreach ($assignment->period->standards as $standard)
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <div class="text-sm text-slate-500">{{ $standard->code }}</div>
                        <div class="font-medium">{{ $standard->name }}</div>
                        <div class="text-sm text-slate-500">{{ $standard->indicators->count() }} indikator</div>
                    </div>
                    <a href="{{ route('auditor.ami.standard', [$assignment, $standard->indicators->first()]) }}" class="rounded-xl border border-slate-200 px-3 py-2 text-sm text-[#00553F]">Buka</a>
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>
