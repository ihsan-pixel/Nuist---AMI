<x-app-layout>
    <div class="mb-6 flex items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Satuan Pendidikan</h1>
            <p class="mt-1 text-sm text-slate-500">Master sekolah untuk AMI.</p>
        </div>
        <a href="{{ route('admin.schools.create') }}" class="rounded-xl bg-[#00553F] px-4 py-2 text-sm font-medium text-white">Tambah</a>
    </div>
    <form method="GET" action="{{ route('admin.schools.index') }}" class="mb-4 grid gap-3 rounded-2xl border bg-white p-4 shadow-sm md:grid-cols-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / SCOD" class="rounded-xl border-slate-200 px-3 py-2">
        <select name="district" class="rounded-xl border-slate-200 px-3 py-2">
            <option value="">Kabupaten</option>
            @foreach ($districts as $district)
                <option value="{{ $district }}" @selected(request('district') === $district)>{{ $district }}</option>
            @endforeach
        </select>
        <select name="education_level" class="rounded-xl border-slate-200 px-3 py-2">
            <option value="">Jenjang</option>
            @foreach ($levels as $level)
                <option value="{{ $level }}" @selected(request('education_level') === $level)>{{ $level }}</option>
            @endforeach
        </select>
        <select name="status" class="rounded-xl border-slate-200 px-3 py-2">
            <option value="">Status</option>
            <option value="active" @selected(request('status') === 'active')>active</option>
            <option value="inactive" @selected(request('status') === 'inactive')>inactive</option>
        </select>
        <div class="md:col-span-4 flex gap-2">
            <button class="rounded-xl bg-[#00553F] px-4 py-2 text-sm font-medium text-white">Filter</button>
            <a href="{{ route('admin.schools.index') }}" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700">Reset</a>
        </div>
    </form>
    <div class="mb-4 grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl bg-white p-4 shadow-sm">Total: {{ $stats['total'] }}</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">Aktif: {{ $stats['active'] }}</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">Nonaktif: {{ $stats['inactive'] }}</div>
    </div>
    <div class="rounded-2xl border bg-white p-4 shadow-sm">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-slate-500"><th>SCOD</th><th>Nama Satuan Pendidikan</th><th>Jenjang</th><th>Kabupaten</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                @foreach ($schools as $school)
                    <tr class="border-t">
                        <td class="py-3">{{ $school->scod }}</td>
                        <td>{{ $school->name }}</td>
                        <td>{{ $school->education_level }}</td>
                        <td>{{ $school->district }}</td>
                        <td>{{ $school->status }}</td>
                        <td class="space-x-2">
                            <a href="{{ route('admin.schools.edit', $school) }}">Edit</a>
                            <form class="inline" method="POST" action="{{ route('admin.schools.destroy', $school) }}">
                                @csrf @method('DELETE')
                                <button type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">{{ $schools->links() }}</div>
    </div>
</x-app-layout>
