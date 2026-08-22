<x-app-layout>
    <h1 class="mb-6 text-2xl font-semibold">Tambah Periode AMI</h1>
    <form method="POST" action="{{ route('admin.ami-periods.store') }}" class="grid gap-4 rounded-2xl bg-white p-6 shadow-sm md:grid-cols-2">
        @csrf
        <input name="name" value="{{ old('name') }}" placeholder="Nama" class="rounded-xl border p-3 md:col-span-2" />
        <input name="year" type="number" value="{{ old('year') }}" placeholder="Tahun" class="rounded-xl border p-3" />
        <select name="status" class="rounded-xl border p-3">
            @foreach (['draft', 'upcoming', 'active', 'review', 'completed', 'archived'] as $status)
                <option value="{{ $status }}" @selected(old('status', 'draft') === $status)>{{ $status }}</option>
            @endforeach
        </select>
        <label class="flex items-center gap-2 rounded-xl border p-3"><input type="checkbox" name="is_active" value="1" @checked(old('is_active'))> Aktif</label>
        <input name="start_date" type="date" value="{{ old('start_date') }}" class="rounded-xl border p-3" />
        <input name="end_date" type="date" value="{{ old('end_date') }}" class="rounded-xl border p-3" />
        <input name="submission_start_at" type="datetime-local" value="{{ old('submission_start_at') }}" class="rounded-xl border p-3" />
        <input name="submission_end_at" type="datetime-local" value="{{ old('submission_end_at') }}" class="rounded-xl border p-3" />
        <input name="review_start_at" type="datetime-local" value="{{ old('review_start_at') }}" class="rounded-xl border p-3" />
        <input name="review_end_at" type="datetime-local" value="{{ old('review_end_at') }}" class="rounded-xl border p-3" />
        <button class="rounded-xl bg-[#00553F] px-4 py-2 text-white md:col-span-2">Simpan</button>
    </form>
</x-app-layout>
