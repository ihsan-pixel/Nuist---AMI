<x-app-layout>
    <h1 class="mb-6 text-2xl font-semibold">Edit Periode AMI</h1>
    <form method="POST" action="{{ route('admin.ami-periods.update', $period) }}" class="grid gap-4 rounded-2xl bg-white p-6 shadow-sm md:grid-cols-2">
        @csrf
        @method('PUT')
        <input name="name" value="{{ old('name', $period->name) }}" class="rounded-xl border p-3 md:col-span-2" />
        <input name="year" type="number" value="{{ old('year', $period->year) }}" class="rounded-xl border p-3" />
        <select name="status" class="rounded-xl border p-3">
            @foreach (['draft', 'upcoming', 'active', 'review', 'completed', 'archived'] as $status)
                <option value="{{ $status }}" @selected(old('status', $period->status) === $status)>{{ $status }}</option>
            @endforeach
        </select>
        <label class="flex items-center gap-2 rounded-xl border p-3"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $period->is_active))> Aktif</label>
        <input name="start_date" type="date" value="{{ old('start_date', optional($period->start_date)->format('Y-m-d')) }}" class="rounded-xl border p-3" />
        <input name="end_date" type="date" value="{{ old('end_date', optional($period->end_date)->format('Y-m-d')) }}" class="rounded-xl border p-3" />
        <input name="submission_start_at" type="datetime-local" value="{{ old('submission_start_at', optional($period->submission_start_at)->format('Y-m-d\TH:i')) }}" class="rounded-xl border p-3" />
        <input name="submission_end_at" type="datetime-local" value="{{ old('submission_end_at', optional($period->submission_end_at)->format('Y-m-d\TH:i')) }}" class="rounded-xl border p-3" />
        <input name="review_start_at" type="datetime-local" value="{{ old('review_start_at', optional($period->review_start_at)->format('Y-m-d\TH:i')) }}" class="rounded-xl border p-3" />
        <input name="review_end_at" type="datetime-local" value="{{ old('review_end_at', optional($period->review_end_at)->format('Y-m-d\TH:i')) }}" class="rounded-xl border p-3" />
        <button class="rounded-xl bg-[#00553F] px-4 py-2 text-white md:col-span-2">Update</button>
    </form>
</x-app-layout>
