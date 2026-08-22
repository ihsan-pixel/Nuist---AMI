<x-app-layout>
    <div class="mb-6 flex items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Periode AMI</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola periode pelaksanaan AMI.</p>
        </div>
        <a href="{{ route('admin.ami-periods.create') }}" class="rounded-xl bg-[#00553F] px-4 py-2 text-sm font-medium text-white">Tambah</a>
    </div>
    <div class="rounded-2xl border bg-white p-4 shadow-sm">
        @foreach ($periods as $period)
            <div class="flex items-center justify-between border-b py-3 last:border-b-0">
                <div>
                    <div class="font-medium">{{ $period->name }} ({{ $period->year }})</div>
                    <div class="text-sm text-slate-500">{{ $period->status }} @if($period->is_active) - aktif @endif</div>
                </div>
                <div class="space-x-3">
                    <a href="{{ route('admin.ami-periods.edit', $period) }}">Edit</a>
                    <form class="inline" method="POST" action="{{ route('admin.ami-periods.activate', $period) }}">
                        @csrf
                        <button type="submit">Aktifkan</button>
                    </form>
                </div>
            </div>
        @endforeach
        <div class="mt-4">{{ $periods->links() }}</div>
    </div>
</x-app-layout>
