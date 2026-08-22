<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900">Audit AMI</h1>
        <p class="mt-1 text-sm text-slate-500">Assignment yang ditugaskan kepada Anda.</p>
    </div>
    <div class="mb-4 grid gap-4 md:grid-cols-4">
        <div class="rounded-2xl bg-white p-4 shadow-sm">Total: {{ $stats['total'] }}</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">Belum mulai: {{ $stats['not_started'] }}</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">Sedang diperiksa: {{ $stats['in_progress'] }}</div>
        <div class="rounded-2xl bg-white p-4 shadow-sm">Selesai: {{ $stats['completed'] }}</div>
    </div>
    <div class="space-y-3">
        @foreach ($assignments as $assignment)
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <div class="font-medium">{{ $assignment->school->name }}</div>
                        <div class="text-sm text-slate-500">{{ $assignment->school->scod }} | {{ $assignment->school->district }} | {{ $assignment->period->name }}</div>
                        <div class="text-sm text-slate-500">Submission: {{ $assignment->status }} | Audit: {{ $assignment->audit_status ?? 'not_started' }} | Submit: {{ $assignment->submitted_at?->format('d M Y H:i') ?? '-' }}</div>
                    </div>
                    <a href="{{ route('auditor.ami.show', $assignment) }}" class="rounded-xl bg-[#00553F] px-4 py-2 text-sm font-medium text-white">Periksa</a>
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>
