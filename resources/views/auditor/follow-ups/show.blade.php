<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900">Detail Verifikasi</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $followUp->assignment->school->name }}</p>
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm space-y-2">
            <div class="font-medium">Ringkasan</div>
            <div class="text-sm text-slate-600">Periode: {{ $followUp->assignment->period->name }}</div>
            <div class="text-sm text-slate-600">Finding: {{ $followUp->finding->title }}</div>
            <div class="text-sm text-slate-600">Action plan: {{ $followUp->action_plan }}</div>
            <div class="text-sm text-slate-600">Status: {{ $followUp->status->value }}</div>
            @if ($followUp->verifier_note)
                <div class="rounded-xl bg-slate-50 p-3 text-sm text-slate-700">Catatan: {{ $followUp->verifier_note }}</div>
            @endif
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="font-medium">Evidence</div>
            <div class="mt-3 space-y-2">
                @foreach ($followUp->evidences as $evidence)
                    <div class="rounded-xl border border-slate-200 p-3">
                        <div class="font-medium">{{ $evidence->title ?? 'Bukti' }}</div>
                        <a href="{{ $evidence->url }}" target="_blank" rel="noopener noreferrer" class="text-sm text-[#00553F]">Buka Dokumen</a>
                        <div class="text-xs text-slate-500">{{ $evidence->url }}</div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 flex gap-3">
                <form method="POST" action="{{ route('auditor.follow-ups.accept', $followUp) }}">
                    @csrf
                    <button class="rounded-xl bg-[#00553F] px-4 py-2 text-sm font-medium text-white">Accept</button>
                </form>
                <form method="POST" action="{{ route('auditor.follow-ups.revision', $followUp) }}" class="flex-1">
                    @csrf
                    <textarea name="verifier_note" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2" placeholder="Catatan revisi"></textarea>
                    <button class="mt-2 rounded-xl border border-slate-200 px-4 py-2 text-sm">Needs Revision</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
