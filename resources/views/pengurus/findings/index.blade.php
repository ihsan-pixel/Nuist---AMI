<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900">Monitoring Findings</h1>
        <p class="mt-1 text-sm text-slate-500">Periode: {{ $period->name }}</p>
    </div>
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-left">
                <tr>
                    <th class="px-4 py-3">Sekolah</th>
                    <th class="px-4 py-3">SCOD</th>
                    <th class="px-4 py-3">Kabupaten</th>
                    <th class="px-4 py-3">Standar</th>
                    <th class="px-4 py-3">Indikator</th>
                    <th class="px-4 py-3">Jenis</th>
                    <th class="px-4 py-3">Finding</th>
                    <th class="px-4 py-3">Rekomendasi</th>
                    <th class="px-4 py-3">Follow-up Status</th>
                    <th class="px-4 py-3">Auditor</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($findings as $finding)
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-3">{{ $finding->assignment->school->name }}</td>
                        <td class="px-4 py-3">{{ $finding->assignment->school->scod }}</td>
                        <td class="px-4 py-3">{{ $finding->assignment->school->district }}</td>
                        <td class="px-4 py-3">{{ $finding->indicator->standard->name }}</td>
                        <td class="px-4 py-3">{{ $finding->indicator->code }}</td>
                        <td class="px-4 py-3">{{ $finding->type }}</td>
                        <td class="px-4 py-3">{{ $finding->title }}</td>
                        <td class="px-4 py-3">{{ $finding->recommendation }}</td>
                        <td class="px-4 py-3">{{ $finding->followUp?->status->value ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $finding->auditor?->name ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $findings->links() }}</div>
</x-app-layout>
