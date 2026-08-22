<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900">Tindak Lanjut AMI</h1>
        <p class="mt-1 text-sm text-slate-500">Hanya finding dari audit yang sudah selesai.</p>
    </div>

    @if (! $assignment)
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">Belum ada audit completed yang bisa ditindaklanjuti.</div>
    @else
        <div class="mb-4 grid gap-4 md:grid-cols-5">
            <div class="rounded-2xl bg-white p-4 shadow-sm">Total: {{ $stats['total'] }}</div>
            <div class="rounded-2xl bg-white p-4 shadow-sm">Belum dikerjakan: {{ $stats['pending'] }}</div>
            <div class="rounded-2xl bg-white p-4 shadow-sm">Draft: {{ $stats['draft'] }}</div>
            <div class="rounded-2xl bg-white p-4 shadow-sm">Menunggu: {{ $stats['submitted'] }}</div>
            <div class="rounded-2xl bg-white p-4 shadow-sm">Selesai: {{ $stats['accepted'] }}</div>
        </div>

        <div class="space-y-3">
            @foreach ($followUps as $followUp)
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-sm text-slate-500">{{ $followUp->finding->indicator?->code }} | {{ $followUp->finding->indicator?->standard?->name }}</div>
                            <div class="font-medium">{{ $followUp->finding->title }}</div>
                            <div class="text-sm text-slate-500">{{ $followUp->finding->type }} | {{ $followUp->status->value }}</div>
                        </div>
                        <a href="{{ route('school.ami.follow-ups.show', $followUp) }}" class="rounded-xl border border-slate-200 px-3 py-2 text-sm text-[#00553F]">Lihat</a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-app-layout>
