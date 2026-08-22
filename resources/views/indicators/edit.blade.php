<x-app-layout>
    <h1 class="mb-6 text-2xl font-semibold">Edit Indikator</h1>
    <form method="POST" action="{{ route('admin.indicators.update', $indicator) }}" class="grid gap-4 rounded-2xl bg-white p-6 shadow-sm md:grid-cols-2">
        @csrf
        @method('PUT')
        <input type="hidden" name="ami_standard_id" value="{{ $standard->id }}">
        <input value="{{ $standard->code }} - {{ $standard->name }}" disabled class="rounded-xl border border-slate-200 px-3 py-2 md:col-span-2">
        <input name="code" value="{{ old('code', $indicator->code) }}" placeholder="Kode" class="rounded-xl border border-slate-200 px-3 py-2" />
        <input name="statement" value="{{ old('statement', $indicator->statement) }}" placeholder="Pernyataan" class="rounded-xl border border-slate-200 px-3 py-2" />
        <textarea name="description" class="rounded-xl border border-slate-200 px-3 py-2 md:col-span-2">{{ old('description', $indicator->description) }}</textarea>
        <textarea name="guidance" class="rounded-xl border border-slate-200 px-3 py-2 md:col-span-2">{{ old('guidance', $indicator->guidance) }}</textarea>
        <textarea name="evidence_requirement" class="rounded-xl border border-slate-200 px-3 py-2 md:col-span-2">{{ old('evidence_requirement', $indicator->evidence_requirement) }}</textarea>
        <input name="weight" type="number" step="0.01" value="{{ old('weight', $indicator->weight) }}" class="rounded-xl border border-slate-200 px-3 py-2" />
        <input name="max_score" type="number" value="{{ old('max_score', $indicator->max_score) }}" class="rounded-xl border border-slate-200 px-3 py-2" />
        <input name="sort_order" type="number" value="{{ old('sort_order', $indicator->sort_order) }}" class="rounded-xl border border-slate-200 px-3 py-2" />
        <label class="flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2"><input type="checkbox" name="is_required" value="1" @checked(old('is_required', $indicator->is_required))> Wajib</label>
        <label class="flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $indicator->is_active))> Aktif</label>
        <button class="rounded-xl bg-[#00553F] px-4 py-2 text-white md:col-span-2">Update</button>
    </form>
</x-app-layout>
