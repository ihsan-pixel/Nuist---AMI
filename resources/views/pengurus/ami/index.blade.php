<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900">Monitoring AMI</h1>
        <p class="mt-1 text-sm text-slate-500">Dashboard read-only untuk pengurus.</p>
    </div>

    <form method="GET" class="mb-4 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-4">
        <select name="ami_period_id" class="rounded-xl border-slate-300">
            @foreach ($periods as $item)
                <option value="{{ $item->id }}" @selected($period->id === $item->id)>{{ $item->name }}</option>
            @endforeach
        </select>
        <input name="search" value="{{ request('search') }}" placeholder="Cari nama atau SCOD" class="rounded-xl border-slate-300" />
        <select name="district" class="rounded-xl border-slate-300">
            <option value="">Semua Kabupaten</option>
            @foreach (['Bantul','Gunungkidul','Kulon Progo','Sleman','Kota Yogyakarta'] as $district)
                <option value="{{ $district }}" @selected(request('district') === $district)>{{ $district }}</option>
            @endforeach
        </select>
        <select name="status" class="rounded-xl border-slate-300">
            @foreach ([
                '' => 'Semua',
                'not_started' => 'Belum Mulai',
                'filling' => 'Sedang Mengisi',
                'submitted' => 'Sudah Submit',
                'audit_in_progress' => 'Sedang Diaudit',
                'audit_completed' => 'Audit Selesai',
                'follow_up_required' => 'Perlu Tindak Lanjut',
                'completed' => 'Selesai',
            ] as $key => $label)
                <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="rounded-xl bg-[#00553F] px-4 py-2 text-white md:col-span-4">Terapkan</button>
    </form>

    <div class="mb-4 grid gap-4 md:grid-cols-4 xl:grid-cols-8">
        <div class="rounded-2xl bg-white p-4 shadow-sm">Total Sekolah: {{ $stats['total_schools'] }}</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">Belum Mengisi: {{ $stats['not_started'] }}</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">Sedang Mengisi: {{ $stats['in_progress'] }}</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">Sudah Submit: {{ $stats['submitted'] }}</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">Sedang Diaudit: {{ $stats['audit_in_progress'] }}</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">Audit Selesai: {{ $stats['audit_completed'] }}</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">Tindak Lanjut Pending: {{ $stats['follow_up_pending'] }}</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">Tindak Lanjut Selesai: {{ $stats['follow_up_done'] }}</div>
    </div>

    <div class="mb-4 grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl bg-white p-4 shadow-sm">Pengisian Sekolah: {{ $progress['school'] }}%</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">Audit: {{ $progress['audit'] }}%</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">Tindak Lanjut: {{ $progress['follow_up'] }}%</div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-left">
                <tr>
                    <th class="px-4 py-3">SCOD</th>
                    <th class="px-4 py-3">Nama Sekolah</th>
                    <th class="px-4 py-3">Kabupaten</th>
                    <th class="px-4 py-3">Periode</th>
                    <th class="px-4 py-3">Status Pengisian</th>
                    <th class="px-4 py-3">Progress</th>
                    <th class="px-4 py-3">Auditor</th>
                    <th class="px-4 py-3">Status Audit</th>
                    <th class="px-4 py-3">Jumlah Temuan</th>
                    <th class="px-4 py-3">Follow-up</th>
                    <th class="px-4 py-3">Status Akhir</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($schools as $assignment)
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-3">{{ $assignment->school->scod }}</td>
                        <td class="px-4 py-3">{{ $assignment->school->name }}</td>
                        <td class="px-4 py-3">{{ $assignment->school->district }}</td>
                        <td class="px-4 py-3">{{ $assignment->period->name }}</td>
                        <td class="px-4 py-3">{{ ucfirst($assignment->status) }}</td>
                        <td class="px-4 py-3">{{ $assignment->responses->count() }}/{{ $assignment->period->standards->flatMap->indicators->count() }}</td>
                        <td class="px-4 py-3">{{ $assignment->auditor?->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $assignment->audit_status ?? 'not_started' }}</td>
                        <td class="px-4 py-3">{{ $assignment->findings->count() }}</td>
                        <td class="px-4 py-3">{{ $assignment->followUps->where('status.value', 'accepted')->count() }}/{{ $assignment->followUps->count() }}</td>
                        <td class="px-4 py-3">{{ $assignment->overall_status_label ?? '-' }}</td>
                        <td class="px-4 py-3"><a href="{{ route('pengurus.ami.show', $assignment) }}" class="text-[#00553F]">Lihat Detail</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $schools->links() }}</div>
</x-app-layout>
