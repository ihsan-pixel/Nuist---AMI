<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900">Komponen {{ $standard->code }}</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $standard->name }}</p>
    </div>

    <div class="space-y-4">
        @foreach ($standard->items as $item)
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="mb-3">
                    <div class="text-sm text-slate-500">Butir {{ $item->number }}</div>
                    <div class="font-medium">{{ $item->title }}</div>
                </div>
                <div class="space-y-3">
                    @foreach ($item->indicators as $indicator)
                        @php $response = $indicator->responses->first(); @endphp
                        <div class="rounded-xl border border-slate-200 p-3">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="text-sm text-slate-500">{{ $indicator->code }} | {{ $indicator->is_required ? 'wajib' : 'opsional' }}</div>
                                    <div class="font-medium">{{ $indicator->title }}</div>
                                    <div class="mt-1 text-sm text-slate-500">{{ $response ? 'Selesai' : 'Belum selesai' }}</div>
                                </div>
                                <a href="{{ route('school.ami.edit', $indicator) }}" class="rounded-xl border border-slate-200 px-3 py-2 text-sm text-[#00553F]">Isi / Lanjutkan</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>
