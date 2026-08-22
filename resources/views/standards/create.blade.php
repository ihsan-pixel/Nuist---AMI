<x-app-layout>
    <h1 class="mb-6 text-2xl font-semibold">Tambah Standar</h1>
    <form method="POST" action="{{ route('admin.standards.store') }}" class="grid gap-4 rounded-2xl bg-white p-6 shadow-sm md:grid-cols-2">
        @csrf
        <select name="ami_period_id" class="rounded-xl border border-slate-200 px-3 py-2 md:col-span-2">
            @foreach ($periods as $period)
                <option value="{{ $period->id }}" @selected((int) old('ami_period_id', $selectedPeriodId) === $period->id)>{{ $period->name }}</option>
            @endforeach
        </select>
        <input name="code" value="{{ old('code') }}" placeholder="Kode" class="rounded-xl border border-slate-200 px-3 py-2" />
        <input name="name" value="{{ old('name') }}" placeholder="Nama" class="rounded-xl border border-slate-200 px-3 py-2" />
        <textarea name="description" placeholder="Deskripsi" class="rounded-xl border border-slate-200 px-3 py-2 md:col-span-2">{{ old('description') }}</textarea>
        <input name="sort_order" type="number" value="{{ old('sort_order', 0) }}" placeholder="Urutan" class="rounded-xl border border-slate-200 px-3 py-2" />
        <input name="weight" type="number" step="0.01" value="{{ old('weight') }}" placeholder="Bobot" class="rounded-xl border border-slate-200 px-3 py-2" />
        <label class="flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))> Aktif</label>
        <button class="rounded-xl bg-[#00553F] px-4 py-2 text-white md:col-span-2">Simpan</button>
    </form>
</x-app-layout>
