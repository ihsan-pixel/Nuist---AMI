<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900">Detail Tindak Lanjut</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $followUp->finding->title }}</p>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm space-y-2">
            <div class="font-medium">Informasi Audit</div>
            <div class="text-sm text-slate-600">Periode: {{ $followUp->assignment->period->name }}</div>
            <div class="text-sm text-slate-600">Sekolah: {{ $followUp->assignment->school->name }}</div>
            <div class="text-sm text-slate-600">Standar: {{ $followUp->finding->indicator?->standard?->name }}</div>
            <div class="text-sm text-slate-600">Indikator: {{ $followUp->finding->indicator?->code }}</div>
            <div class="text-sm text-slate-600">Jenis finding: {{ $followUp->finding->type }}</div>
            <div class="text-sm text-slate-600">Judul: {{ $followUp->finding->title }}</div>
            <div class="text-sm text-slate-600">Uraian: {{ $followUp->finding->description }}</div>
            <div class="text-sm text-slate-600">Rekomendasi: {{ $followUp->finding->recommendation }}</div>
            @if ($followUp->status->value === 'needs_revision' && $followUp->verifier_note)
                <div class="rounded-xl bg-amber-50 p-3 text-sm text-amber-900">Catatan auditor: {{ $followUp->verifier_note }}</div>
            @endif
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="font-medium">Tindakan Perbaikan</div>
            <form method="POST" action="{{ route('school.ami.follow-ups.store', $followUp->finding) }}" class="mt-3 space-y-3">
                @csrf
                <textarea name="action_plan" rows="6" class="w-full rounded-xl border border-slate-200 px-3 py-2" placeholder="Tindakan yang dilakukan">{{ old('action_plan', $followUp->action_plan) }}</textarea>
                <input type="hidden" name="status" value="draft">
                <button class="rounded-xl border border-slate-200 px-4 py-2 text-sm">Simpan Draft</button>
                <button name="status" value="submitted" class="rounded-xl bg-[#00553F] px-4 py-2 text-sm font-medium text-white">Kirim Tindak Lanjut</button>
            </form>

            <div class="mt-6">
                <div class="mb-3 font-medium">Evidence Google Drive</div>
                <div class="mb-3 rounded-xl bg-slate-50 p-3 text-sm text-slate-600">
                    Pastikan link Google Drive dapat diakses oleh auditor.
                </div>
                <form method="POST" action="{{ route('school.ami.follow-ups.evidences.store', $followUp) }}" class="space-y-3">
                    @csrf
                    <input name="title" class="w-full rounded-xl border border-slate-200 px-3 py-2" placeholder="Judul bukti">
                    <input name="url" class="w-full rounded-xl border border-slate-200 px-3 py-2" placeholder="Link Google Drive">
                    <textarea name="note" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2" placeholder="Catatan"></textarea>
                    <button class="rounded-xl bg-[#00553F] px-4 py-2 text-sm font-medium text-white">Tambah Link Bukti</button>
                </form>

                <div class="mt-4 space-y-2">
                    @foreach ($followUp->evidences as $evidence)
                        <div class="rounded-xl border border-slate-200 p-3">
                            <div class="font-medium">{{ $evidence->title ?? 'Bukti' }}</div>
                            <a href="{{ $evidence->url }}" target="_blank" rel="noopener noreferrer" class="text-sm text-[#00553F]">Buka Dokumen</a>
                            <div class="text-xs text-slate-500">{{ $evidence->url }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
