<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900">Verifikasi Tindak Lanjut</h1>
    </div>
    <div class="mb-4 grid gap-4 md:grid-cols-4">
        <div class="rounded-2xl bg-white p-4 shadow-sm">Total: {{ $stats['total'] }}</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">Submitted: {{ $stats['submitted'] }}</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">Revisi: {{ $stats['needs_revision'] }}</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">Accepted: {{ $stats['accepted'] }}</div>
    </div>
    <div class="space-y-3">
        @foreach ($followUps as $followUp)
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <div class="text-sm text-slate-500">{{ $followUp->assignment->school->name }}</div>
                        <div class="font-medium">{{ $followUp->finding->title }}</div>
                        <div class="text-sm text-slate-500">{{ $followUp->status->value }}</div>
                    </div>
                    <a href="{{ route('auditor.follow-ups.show', $followUp) }}" class="rounded-xl border border-slate-200 px-3 py-2 text-sm text-[#00553F]">Buka</a>
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>
