<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900">{{ $assignment->school->name }}</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $assignment->school->scod }} | {{ $assignment->period->name }}</p>
    </div>
    <div class="mb-4 grid gap-4 md:grid-cols-6">
        <div class="rounded-2xl bg-white p-4 shadow-sm">Sekolah: {{ $assignment->school->name }}</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">SCOD: {{ $assignment->school->scod }}</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">Kabupaten: {{ $assignment->school->district }}</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">Periode: {{ $assignment->period->name }}</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">Auditor: {{ $assignment->auditor?->name ?? '-' }}</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">Status akhir: {{ $assignment->status === 'submitted' ? 'Sudah Submit' : 'Selesai' }}</div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="font-medium">Identitas</div>
        <div>Submit: {{ $assignment->submitted_at?->format('d M Y H:i') ?? '-' }}</div>
        <div>Audit selesai: {{ $assignment->audit_completed_at?->format('d M Y H:i') ?? '-' }}</div>
    </div>

    <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="font-medium">Ringkasan</div>
        <div>Total standar: {{ $summary['total_standards'] }}</div>
        <div>Total indikator: {{ $summary['total_indicators'] }}</div>
        <div>Indikator dinilai: {{ $summary['assessed_indicators'] }}</div>
        <div>Kurang: {{ $summary['kurang'] }}</div>
        <div>Cukup Baik: {{ $summary['cukup_baik'] }}</div>
        <div>Baik: {{ $summary['baik'] }}</div>
        <div>Sangat Baik: {{ $summary['sangat_baik'] }}</div>
    </div>

    <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="font-medium">Hasil per Standar</div>
        <div class="mt-3 space-y-2">
            @foreach ($standardRows as $row)
                <div class="rounded-xl border border-slate-200 p-3">
                    <div class="font-medium">{{ $row['code'] }} - {{ $row['name'] }}</div>
                    <div class="text-sm text-slate-600">Indikator: {{ $row['indicators'] }} | Kurang: {{ $row['kurang'] }} | Cukup Baik: {{ $row['cukup_baik'] }} | Baik: {{ $row['baik'] }} | Sangat Baik: {{ $row['sangat_baik'] }} | Finding: {{ $row['findings'] }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="font-medium">Temuan</div>
        <div class="mt-3 space-y-2">
            @foreach ($assignment->findings as $finding)
                <div class="rounded-xl border border-slate-200 p-3">
                    <div class="font-medium">{{ $finding->type }} | {{ $finding->indicator?->code }} | {{ $finding->title }}</div>
                    <div class="text-sm text-slate-600">{{ $finding->description }}</div>
                    <div class="text-sm text-slate-600">Rekomendasi: {{ $finding->recommendation }}</div>
                    <div class="text-sm text-slate-600">Follow-up: {{ $finding->followUp?->status->value ?? '-' }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="font-medium">Tindak Lanjut</div>
        <div class="mt-3 space-y-2">
            @foreach ($assignment->followUps as $followUp)
                <div class="rounded-xl border border-slate-200 p-3">
                    <div class="font-medium">{{ $followUp->finding->title }}</div>
                    <div class="text-sm text-slate-600">Jenis: {{ $followUp->finding->type }}</div>
                    <div class="text-sm text-slate-600">Action plan: {{ $followUp->action_plan }}</div>
                    <div class="text-sm text-slate-600">Status: {{ $followUp->status->value }}</div>
                    <div class="text-sm text-slate-600">Submitted at: {{ $followUp->submitted_at?->format('d M Y H:i') ?? '-' }}</div>
                    <div class="text-sm text-slate-600">Verifier note: {{ $followUp->verifier_note ?? '-' }}</div>
                    <div class="text-sm text-slate-600">Verified at: {{ $followUp->verified_at?->format('d M Y H:i') ?? '-' }}</div>
                    <div class="text-sm text-slate-600">Verifier: {{ $followUp->verifier?->name ?? '-' }}</div>
                    @foreach ($followUp->evidences as $evidence)
                        <div class="mt-2 rounded-lg bg-slate-50 p-3">
                            <div class="font-medium">{{ $evidence->title ?? 'Bukti' }}</div>
                            <div class="text-sm text-slate-600">{{ $evidence->note }}</div>
                            <a href="{{ $evidence->url }}" target="_blank" rel="noopener noreferrer" class="text-sm text-[#00553F]">Buka Google Drive</a>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
