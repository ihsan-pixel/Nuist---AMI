<x-app-layout>
    <div class="mb-6 flex items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Laporan AMI</h1>
            <p class="mt-1 text-sm text-slate-500">{{ $period->name }}</p>
        </div>
        <a href="{{ route('pengurus.ami.reports.export', request()->query()) }}" class="rounded-xl bg-[#00553F] px-4 py-2 text-sm font-medium text-white">Export Excel</a>
    </div>
    <form method="GET" class="mb-4 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-4">
        <select name="period" class="rounded-xl border-slate-300">
            @foreach ($periods as $item)
                <option value="{{ $item->id }}" @selected($period->id === $item->id)>{{ $item->name }}</option>
            @endforeach
        </select>
        <input name="search" value="{{ request('search') }}" class="rounded-xl border-slate-300" placeholder="Cari sekolah/SCOD">
        <select name="district" class="rounded-xl border-slate-300">
            <option value="">Semua Kabupaten</option>
            @foreach (['Bantul','Gunungkidul','Kulon Progo','Sleman','Kota Yogyakarta'] as $district)
                <option value="{{ $district }}" @selected(request('district') === $district)>{{ $district }}</option>
            @endforeach
        </select>
        <select name="status" class="rounded-xl border-slate-300">
            <option value="">Semua Status</option>
            <option value="not_started" @selected(request('status')==='not_started')>Belum Submit</option>
            <option value="submitted" @selected(request('status')==='submitted')>Sudah Submit</option>
            <option value="audit_completed" @selected(request('status')==='audit_completed')>Audit Selesai</option>
            <option value="completed" @selected(request('status')==='completed')>Selesai</option>
        </select>
        <button class="rounded-xl bg-[#00553F] px-4 py-2 text-white md:col-span-4">Terapkan</button>
    </form>
    <div class="mb-4 grid gap-4 md:grid-cols-4">
        <div class="rounded-2xl bg-white p-4 shadow-sm">Total Sekolah: {{ $summary['total_schools'] }}</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">Belum Submit: {{ $summary['not_submitted'] }}</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">Audit Selesai: {{ $summary['audit_completed'] }}</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">Follow-up Pending: {{ $summary['follow_up_pending'] }}</div>
    </div>
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-left"><tr><th class="px-4 py-3">SCOD</th><th class="px-4 py-3">Nama Sekolah</th><th class="px-4 py-3">Kabupaten</th><th class="px-4 py-3">Status Akhir</th><th class="px-4 py-3">Aksi</th></tr></thead>
            <tbody>
                @foreach ($schools as $assignment)
                    <tr class="border-t"><td class="px-4 py-3">{{ $assignment->school->scod }}</td><td class="px-4 py-3">{{ $assignment->school->name }}</td><td class="px-4 py-3">{{ $assignment->school->district }}</td><td class="px-4 py-3">{{ $assignment->audit_status }}</td><td class="px-4 py-3"><a href="{{ route('pengurus.ami.reports.school', $assignment) }}" class="text-[#00553F]">Lihat Laporan</a></td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $schools->links() }}</div>
</x-app-layout>
