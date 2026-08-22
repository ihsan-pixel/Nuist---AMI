<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900">Instrumen AMI Sekolah</h1>
        <p class="mt-1 text-sm text-slate-500">Pengisian hanya untuk sekolah yang ditugaskan.</p>
    </div>

    @if (! $assignment)
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">Belum ada assignment aktif</div>
    @else
        <div class="mb-4 grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl bg-white p-4 shadow-sm">
                <div class="text-sm text-slate-500">Sekolah</div>
                <div class="font-medium">{{ $assignment->school->name }}</div>
                <div class="text-sm text-slate-500">{{ $assignment->school->scod }}</div>
            </div>
            <div class="rounded-2xl bg-white p-4 shadow-sm">
                <div class="text-sm text-slate-500">Periode</div>
                <div class="font-medium">{{ $period?->name }}</div>
                <div class="text-sm text-slate-500">Status {{ $assignment->status }}</div>
            </div>
            <div class="rounded-2xl bg-white p-4 shadow-sm">
                <div class="text-sm text-slate-500">Progress</div>
                <div class="font-semibold text-2xl">{{ $progressPercent }}%</div>
                <div class="text-sm text-slate-500">{{ $completedCount }} selesai, {{ $pendingCount }} belum selesai</div>
            </div>
        </div>

        <div class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <div class="text-sm text-slate-500">Deadline submission</div>
                    <div class="font-medium">{{ $period?->submission_end_at?->format('d M Y H:i') ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-sm text-slate-500">Total indikator wajib</div>
                    <div class="font-medium">{{ $totalRequired }}</div>
                </div>
                <div class="flex items-end justify-end">
                    <a href="{{ route('school.ami.review') }}" class="rounded-xl bg-[#00553F] px-4 py-2 text-sm font-medium text-white">Ringkasan Submit</a>
                </div>
            </div>
        </div>

        <div class="space-y-3">
            @foreach ($standards as $standard)
                @php
                    $standardResponses = $responses->filter(fn ($response) => $response->indicator->ami_standard_id === $standard->id);
                    $completed = $standardResponses->filter(fn ($response) => $response->self_score !== null && filled($response->answer))->count();
                @endphp
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <div class="text-sm text-slate-500">Komponen {{ $standard->code }}</div>
                            <div class="font-medium">{{ $standard->name }}</div>
                            <div class="mt-1 text-sm text-slate-500">Butir: {{ $standard->items->count() }} | Indikator: {{ $standard->indicators->count() }} | Selesai: {{ $completed }}</div>
                        </div>
                        <a href="{{ route('school.ami.standard', $standard) }}" class="text-sm text-[#00553F]">Buka</a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-app-layout>
