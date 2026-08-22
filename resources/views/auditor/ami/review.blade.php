<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900">Review Audit</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $assignment->school->name }} | {{ $assignment->period->name }}</p>
    </div>

    <div class="mb-4 grid gap-4 md:grid-cols-4">
        <div class="rounded-2xl bg-white p-4 shadow-sm">Total: {{ $stats['total_indicators'] }}</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">Dinilai: {{ $stats['assessed'] }}</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">Rating Baik: {{ $stats['baik'] }}</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">Temuan: {{ $stats['findings'] }}</div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="mb-3 font-medium">Kelayakan finalisasi</div>
        <div class="text-sm text-slate-600">{{ $canComplete ? 'Siap finalisasi' : 'Masih ada indikator wajib yang belum dinilai' }}</div>
        <form method="POST" action="{{ route('auditor.ami.complete', $assignment) }}" class="mt-4">
            @csrf
            <button @disabled(! $canComplete) class="rounded-xl bg-[#00553F] px-4 py-2 text-sm font-medium text-white disabled:cursor-not-allowed disabled:opacity-50">Finalisasi audit</button>
        </form>
    </div>
</x-app-layout>
