<x-app-layout>
    <h1 class="mb-6 text-2xl font-semibold">Tambah Indikator</h1>
    <form method="POST" action="{{ route('admin.indicators.store') }}" class="grid gap-4 rounded-2xl bg-white p-6 shadow-sm md:grid-cols-2">
        @csrf
        <input type="hidden" name="ami_standard_id" value="{{ $standard->id }}">
        <input value="{{ $standard->code }} - {{ $standard->name }}" disabled class="rounded-xl border border-slate-200 px-3 py-2 md:col-span-2">
        <input name="code" value="{{ old('code') }}" placeholder="Kode" class="rounded-xl border border-slate-200 px-3 py-2" />
        <input name="statement" value="{{ old('statement') }}" placeholder="Pernyataan" class="rounded-xl border border-slate-200 px-3 py-2" />
        <textarea name="description" placeholder="Deskripsi" class="rounded-xl border border-slate-200 px-3 py-2 md:col-span-2">{{ old('description') }}</textarea>
        <textarea name="guidance" placeholder="Panduan" class="rounded-xl border border-slate-200 px-3 py-2 md:col-span-2">{{ old('guidance') }}</textarea>
        <textarea name="evidence_requirement" placeholder="Kebutuhan bukti" class="rounded-xl border border-slate-200 px-3 py-2 md:col-span-2">{{ old('evidence_requirement') }}</textarea>
        <input name="weight" type="number" step="0.01" value="{{ old('weight') }}" placeholder="Bobot" class="rounded-xl border border-slate-200 px-3 py-2" />
        <input name="max_score" type="number" value="{{ old('max_score', 4) }}" placeholder="Max Score" class="rounded-xl border border-slate-200 px-3 py-2" />
        <input name="sort_order" type="number" value="{{ old('sort_order', 0) }}" placeholder="Urutan" class="rounded-xl border border-slate-200 px-3 py-2" />
        <label class="flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2"><input type="checkbox" name="is_required" value="1" @checked(old('is_required', true))> Wajib</label>
        <label class="flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))> Aktif</label>
        <button class="rounded-xl bg-[#00553F] px-4 py-2 text-white md:col-span-2">Simpan</button>
    </form>
</x-app-layout>
