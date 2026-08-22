<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900">{{ $assignment->school->name }}</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $assignment->school->scod }} | {{ $assignment->school->district }}</p>
    </div>

    <div class="mb-4 grid gap-4 md:grid-cols-5">
        <div class="rounded-2xl bg-white p-4 shadow-sm">Nama Sekolah: {{ $assignment->school->name }}</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">SCOD: {{ $assignment->school->scod }}</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">Kabupaten: {{ $assignment->school->district }}</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">Periode: {{ $assignment->period->name }}</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">Auditor: {{ $assignment->auditor?->name ?? '-' }}</div>
    </div>

    <div class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="font-medium">Status Akhir: {{ $label }}</div>
        <div class="mt-4 grid gap-3 md:grid-cols-5 text-sm">
            <div>Pengisian</div>
            <div>Submit</div>
            <div>Audit</div>
            <div>Tindak Lanjut</div>
            <div>Selesai</div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm space-y-2">
            <div class="font-medium">Ringkasan Pengisian</div>
            <div>Total Standar: {{ $assignment->period->standards->count() }}</div>
            <div>Total Indikator: {{ $assignment->period->standards->flatMap->indicators->count() }}</div>
            <div>Indikator Terisi: {{ $assignment->responses->count() }}</div>
            <div>Indikator Belum Terisi: {{ $assignment->period->standards->flatMap->indicators->count() - $assignment->responses->count() }}</div>
            <div>Progress: {{ $assignment->responses->count() }}/{{ $assignment->period->standards->flatMap->indicators->count() }}</div>
            <div>Tanggal Submit: {{ $assignment->submitted_at?->format('d M Y H:i') ?? '-' }}</div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm space-y-2">
            <div class="font-medium">Ringkasan Audit</div>
            <div>Status Audit: {{ $assignment->audit_status ?? '-' }}</div>
            <div>Tanggal Mulai Audit: {{ $assignment->audit_started_at?->format('d M Y H:i') ?? '-' }}</div>
            <div>Tanggal Selesai Audit: {{ $assignment->audit_completed_at?->format('d M Y H:i') ?? '-' }}</div>
            <div>Auditor: {{ $assignment->auditor?->name ?? '-' }}</div>
            <div>Jumlah Indikator Dinilai: {{ $assignment->assessments->count() }}</div>
            <div>Conform: {{ $assignment->assessments->where('status.value', 'conform')->count() }}</div>
            <div>Partially Conform: {{ $assignment->assessments->where('status.value', 'partially_conform')->count() }}</div>
            <div>Non Conform: {{ $assignment->assessments->where('status.value', 'non_conform')->count() }}</div>
        </div>
    </div>

    <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="font-medium">Ringkasan Finding</div>
        <div class="mt-2 grid gap-3 md:grid-cols-4">
            <div>Observation: {{ $assignment->findings->where('type', 'observation')->count() }}</div>
            <div>Minor: {{ $assignment->findings->where('type', 'minor')->count() }}</div>
            <div>Major: {{ $assignment->findings->where('type', 'major')->count() }}</div>
            <div>Total: {{ $assignment->findings->count() }}</div>
        </div>
        <div class="mt-4 space-y-2">
            @foreach ($assignment->findings as $finding)
                <div class="rounded-xl border border-slate-200 p-3">
                    <div class="font-medium">{{ $finding->indicator?->code }} | {{ $finding->type }} | {{ $finding->title }}</div>
                    <div class="text-sm text-slate-600">{{ $finding->description }}</div>
                    <div class="text-sm text-slate-600">Rekomendasi: {{ $finding->recommendation }}</div>
                    <div class="text-sm text-slate-600">Follow-up: {{ $finding->followUp?->status->value ?? 'belum ada' }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="font-medium">Ringkasan Tindak Lanjut</div>
        <div class="mt-2 space-y-2">
            @foreach ($assignment->followUps as $followUp)
                <div class="rounded-xl border border-slate-200 p-3">
                    <div class="font-medium">{{ $followUp->finding->title }}</div>
                    <div class="text-sm text-slate-600">Status: {{ $followUp->status->value }}</div>
                    <div class="text-sm text-slate-600">Action Plan: {{ $followUp->action_plan }}</div>
                    <div class="text-sm text-slate-600">Tanggal Submit: {{ $followUp->submitted_at?->format('d M Y H:i') ?? '-' }}</div>
                    <div class="text-sm text-slate-600">Status Verifikasi: {{ $followUp->status->value }}</div>
                    <div class="text-sm text-slate-600">Auditor: {{ $assignment->auditor?->name ?? '-' }}</div>
                    <div class="text-sm text-slate-600">Tanggal Verifikasi: {{ $followUp->verified_at?->format('d M Y H:i') ?? '-' }}</div>
                    @foreach ($followUp->evidences as $evidence)
                        <a href="{{ $evidence->url }}" target="_blank" rel="noopener noreferrer" class="text-sm text-[#00553F]">Buka Google Drive</a>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
