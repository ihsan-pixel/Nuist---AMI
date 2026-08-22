<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900">Ringkasan Submit</h1>
        <p class="mt-1 text-sm text-slate-500">Pastikan semua indikator wajib lengkap sebelum submit final.</p>
    </div>

    <div class="mb-4 grid gap-4 md:grid-cols-4">
        <div class="rounded-2xl bg-white p-4 shadow-sm">Wajib: {{ $required->count() }}</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">Lengkap: {{ $required->count() - $missing->count() }}</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">Belum lengkap: {{ $missing->count() }}</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">
            <form method="POST" action="{{ route('school.ami.submit') }}">
                @csrf
                <button class="rounded-xl bg-[#00553F] px-4 py-2 text-sm font-medium text-white">Submit Final</button>
            </form>
        </div>
    </div>

    <div class="space-y-3">
        @foreach ($missing as $indicator)
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                <div class="font-medium">{{ $indicator->code }}</div>
                <div class="text-sm text-slate-700">{{ $indicator->statement }}</div>
            </div>
        @endforeach

        @foreach ($assignment->responses as $response)
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="font-medium">{{ $response->indicator->code }} - {{ $response->indicator->statement }}</div>
                <div class="mt-2 space-y-2">
                    @foreach ($response->evidences as $evidence)
                        <div class="rounded-xl border border-slate-200 p-3">
                            <div class="font-medium">{{ $evidence->title ?? 'Bukti' }}</div>
                            <a href="{{ $evidence->url }}" target="_blank" rel="noopener noreferrer" class="text-sm text-[#00553F]">Buka Dokumen</a>
                            <div class="text-xs text-slate-500">{{ $evidence->url }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>
