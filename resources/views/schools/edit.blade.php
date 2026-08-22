<x-app-layout>
    <h1 class="mb-6 text-2xl font-semibold">Edit Sekolah</h1>
    <form method="POST" action="{{ route('admin.schools.update', $school) }}" class="grid gap-4 rounded-2xl bg-white p-6 shadow-sm md:grid-cols-2">
        @csrf
        @method('PUT')
        <input name="scod" value="{{ $school->scod }}" class="rounded-xl border p-3" />
        <input name="name" value="{{ $school->name }}" class="rounded-xl border p-3" />
        <input name="education_level" value="{{ $school->education_level }}" class="rounded-xl border p-3" />
        <input name="district" value="{{ $school->district }}" class="rounded-xl border p-3" />
        <input name="email" value="{{ $school->email }}" class="rounded-xl border p-3" />
        <input name="phone" value="{{ $school->phone }}" class="rounded-xl border p-3" />
        <textarea name="address" class="rounded-xl border p-3 md:col-span-2">{{ $school->address }}</textarea>
        <select name="status" class="rounded-xl border p-3 md:col-span-2">
            <option value="active" @selected($school->status === 'active')>active</option>
            <option value="inactive" @selected($school->status === 'inactive')>inactive</option>
        </select>
        <button class="rounded-xl bg-[#00553F] px-4 py-2 text-white md:col-span-2">Update</button>
    </form>
</x-app-layout>
