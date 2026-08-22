<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900">{{ $indicator->standard->code }} - {{ $indicator->standard->name }}</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $assignment->school->name }} | {{ $indicator->code }}</p>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="text-sm text-slate-500">Pernyataan</div>
                <div class="font-medium">{{ $indicator->statement }}</div>
                <div class="mt-2 text-sm text-slate-500">DKA Sekolah</div>
                <div class="text-sm text-slate-700 whitespace-pre-line">{{ $response?->answer ?? '-' }}</div>
                @if ($indicator->evidence_guidance)
                    <div class="mt-2 text-sm text-slate-500">Evidence</div>
                    <div class="text-sm text-slate-700 whitespace-pre-line">{{ $indicator->evidence_guidance }}</div>
                @endif
            </div>
            <a href="{{ route('auditor.ami.review', $assignment) }}" class="rounded-xl border border-slate-200 px-3 py-2 text-sm text-[#00553F]">Review</a>
        </div>
    </div>

    <div class="mt-4 grid gap-4 md:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="mb-3 font-medium">Assessment</div>
            <form method="POST" action="{{ route('auditor.ami.assessments.store', [$assignment, $indicator]) }}" class="space-y-3">
                @csrf
                <label class="block text-sm">
                    <span class="text-slate-600">Rating</span>
                    <select name="rating" class="mt-1 w-full rounded-xl border-slate-300">
                        <option value="kurang">Kurang</option>
                        <option value="cukup_baik">Cukup Baik</option>
                        <option value="baik">Baik</option>
                        <option value="sangat_baik">Sangat Baik</option>
                    </select>
                </label>
                <label class="block text-sm">
                    <span class="text-slate-600">Score</span>
                    <input type="number" step="0.01" name="score" class="mt-1 w-full rounded-xl border-slate-300">
                </label>
                <label class="block text-sm">
                    <span class="text-slate-600">Catatan auditor</span>
                    <textarea name="auditor_note" rows="4" class="mt-1 w-full rounded-xl border-slate-300"></textarea>
                </label>
                <button class="rounded-xl bg-[#00553F] px-4 py-2 text-sm font-medium text-white">Simpan assessment</button>
            </form>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="mb-3 font-medium">Temuan</div>
            <form method="POST" action="{{ route('auditor.ami.findings.store', [$assignment, $indicator]) }}" class="space-y-3">
                @csrf
                <label class="block text-sm">
                    <span class="text-slate-600">Tipe</span>
                    <select name="type" class="mt-1 w-full rounded-xl border-slate-300">
                        <option value="observation">Observation</option>
                        <option value="minor">Minor</option>
                        <option value="major">Major</option>
                    </select>
                </label>
                <label class="block text-sm">
                    <span class="text-slate-600">Judul</span>
                    <input name="title" class="mt-1 w-full rounded-xl border-slate-300">
                </label>
                <label class="block text-sm">
                    <span class="text-slate-600">Deskripsi</span>
                    <textarea name="description" rows="4" class="mt-1 w-full rounded-xl border-slate-300"></textarea>
                </label>
                <label class="block text-sm">
                    <span class="text-slate-600">Rekomendasi</span>
                    <textarea name="recommendation" rows="3" class="mt-1 w-full rounded-xl border-slate-300"></textarea>
                </label>
                <button class="rounded-xl bg-[#00553F] px-4 py-2 text-sm font-medium text-white">Simpan temuan</button>
            </form>
        </div>
    </div>

    <div class="mt-4 space-y-3">
        <div class="text-lg font-medium">Responses sekolah terkait indikator ini</div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="text-sm text-slate-500">Response sekolah ada di modul sekolah, auditor hanya membaca untuk referensi.</div>
            <div class="mt-3 text-sm text-slate-600">DKA: {{ $response?->answer ?? '-' }}</div>
        </div>
        <div class="text-lg font-medium">Temuan terkait</div>
        @foreach ($findings as $finding)
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="font-medium">{{ $finding->title }}</div>
                <div class="text-sm text-slate-500">{{ $finding->type }} | {{ $finding->status }}</div>
                <div class="mt-2 text-sm">{{ $finding->description }}</div>
            </div>
        @endforeach
    </div>
</x-app-layout>
