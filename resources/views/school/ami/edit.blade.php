<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900">{{ $indicator->code }}</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $indicator->standard->name }}</p>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-4 grid gap-3 md:grid-cols-2">
            <div>
                <div class="text-sm text-slate-500">Komponen</div>
                <div class="font-medium">{{ $indicator->standard->name }}</div>
            </div>
            <div>
                <div class="text-sm text-slate-500">Butir</div>
                <div class="font-medium">{{ $indicator->item?->title ?? '-' }}</div>
            </div>
        </div>
        <div class="mb-4">
            <div class="text-sm text-slate-500">Pernyataan Indikator</div>
            <div class="font-medium">{{ $indicator->statement }}</div>
        </div>
        @if ($indicator->operational_definition)
            <div class="mb-4 rounded-xl bg-slate-50 p-3 text-sm text-slate-700">
                <div class="font-medium text-slate-900">DKA / Deskripsi Kinerja</div>
                <div class="mt-1 whitespace-pre-line">{{ $indicator->operational_definition }}</div>
            </div>
        @endif
        @if ($indicator->guidance)
            <div class="mb-4 text-sm text-slate-500">{{ $indicator->guidance }}</div>
        @endif
        @if ($indicator->evidence_requirement)
            <div class="mb-4 text-sm text-slate-500">{{ $indicator->evidence_requirement }}</div>
        @endif
        <div class="mb-4 rounded-xl bg-slate-50 p-3 text-sm text-slate-600">
            Pastikan link Google Drive dapat diakses oleh auditor. Dokumen yang memerlukan permintaan akses dapat menghambat proses audit.
        </div>

        <form method="POST" action="{{ route('school.ami.update', $indicator) }}" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" value="draft">
            <div>
                <label class="mb-1 block text-sm font-medium">Self Score</label>
                <select name="self_score" class="w-full rounded-xl border border-slate-200 px-3 py-2">
                    <option value="">Pilih</option>
                    <option value="1" @selected(old('self_score', $response->self_score) == 1)>1 - Belum Terpenuhi</option>
                    <option value="2" @selected(old('self_score', $response->self_score) == 2)>2 - Terpenuhi Sebagian</option>
                    <option value="3" @selected(old('self_score', $response->self_score) == 3)>3 - Terpenuhi</option>
                    <option value="4" @selected(old('self_score', $response->self_score) == 4)>4 - Terpenuhi Sangat Baik</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">DKA / Deskripsi Kinerja</label>
                <textarea name="answer" class="w-full rounded-xl border border-slate-200 px-3 py-2" rows="4">{{ old('answer', $response->answer) }}</textarea>
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Catatan</label>
                <textarea name="note" class="w-full rounded-xl border border-slate-200 px-3 py-2" rows="3">{{ old('note', $response->note) }}</textarea>
            </div>
            <div class="flex gap-3">
                <button name="save_action" value="draft" class="rounded-xl border border-slate-200 px-4 py-2 text-sm">Simpan Draft</button>
                <button name="save_action" value="next" class="rounded-xl bg-[#00553F] px-4 py-2 text-sm font-medium text-white">Simpan & Berikutnya</button>
                <a href="{{ route('school.ami.standard', $indicator->standard) }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm">Sebelumnya</a>
            </div>
        </form>

        <div class="mt-8">
            <div class="mb-3 text-sm font-medium">Bukti Google Drive</div>
            <form method="POST" action="{{ route('school.ami.evidences.store', $response) }}" class="grid gap-3 md:grid-cols-2">
                @csrf
                <input name="title" placeholder="Judul dokumen" class="rounded-xl border border-slate-200 px-3 py-2" />
                <input name="url" placeholder="Link Google Drive" class="rounded-xl border border-slate-200 px-3 py-2 md:col-span-2" />
                <input name="description" placeholder="Catatan" class="rounded-xl border border-slate-200 px-3 py-2 md:col-span-2" />
                <input name="sort_order" type="number" value="0" class="rounded-xl border border-slate-200 px-3 py-2" />
                <button class="rounded-xl bg-[#00553F] px-4 py-2 text-white md:col-span-2">Tambah Bukti</button>
            </form>

            <div class="mt-4 space-y-3">
                @foreach ($response->evidences as $evidence)
                    <div class="rounded-xl border border-slate-200 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <div class="font-medium">{{ $evidence->title ?? 'Bukti' }}</div>
                                <a href="{{ $evidence->url }}" target="_blank" rel="noopener noreferrer" class="text-sm text-[#00553F]">Buka Dokumen</a>
                                <div class="text-xs text-slate-500">{{ $evidence->url }}</div>
                            </div>
                            <form method="POST" action="{{ route('school.ami.evidences.destroy', $evidence) }}">
                                @csrf
                                @method('DELETE')
                                <button class="text-sm text-red-600">Hapus</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
