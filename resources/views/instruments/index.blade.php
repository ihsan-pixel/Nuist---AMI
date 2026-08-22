<x-app-layout>
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Instrumen AMI IA2024</h1>
            <p class="mt-1 text-sm text-slate-500">Komponen → Butir → Indikator.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.standards.create', ['ami_period_id' => $selectedPeriod?->id]) }}" class="rounded-xl bg-[#00553F] px-4 py-2 text-sm font-medium text-white">Tambah Komponen</a>
        </div>
    </div>

    <form method="GET" class="mb-4 max-w-sm">
        <select name="ami_period_id" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2" onchange="this.form.submit()">
            @foreach ($periods as $period)
                <option value="{{ $period->id }}" @selected($selectedPeriod?->id === $period->id)>{{ $period->name }} ({{ $period->year }})</option>
            @endforeach
        </select>
    </form>

    <div x-data="{ openComponent: null, openItem: null }" class="space-y-4">
        @foreach ($selectedPeriod?->standards ?? [] as $standard)
            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">
                <button type="button" class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left" @click="openComponent = openComponent === {{ $standard->id }} ? null : {{ $standard->id }}; openItem = null">
                    <div>
                        <div class="text-xs uppercase tracking-wide text-slate-500">Komponen {{ $standard->sort_order }}</div>
                        <div class="text-lg font-semibold text-slate-900">{{ $standard->name }}</div>
                    </div>
                    <div class="text-sm text-slate-500">{{ $standard->items->count() }} butir</div>
                </button>

                <div x-show="openComponent === {{ $standard->id }}" x-cloak class="border-t border-slate-100 p-5">
                    <div class="space-y-3">
                        @foreach ($standard->items as $item)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50">
                                <button type="button" class="flex w-full items-center justify-between gap-4 px-4 py-3 text-left" @click="openItem = openItem === {{ $item->id }} ? null : {{ $item->id }}">
                                    <div>
                                        <div class="text-sm font-semibold text-slate-900">Butir {{ $item->number }} - {{ $item->title }}</div>
                                        <div class="text-xs text-slate-500">{{ $item->code }}</div>
                                    </div>
                                    <div class="text-sm text-slate-500">{{ $item->indicators->count() }} indikator</div>
                                </button>

                                <div x-show="openItem === {{ $item->id }}" x-cloak class="border-t border-slate-200 bg-white px-4 py-4">
                                    <div class="space-y-2">
                                        @foreach ($item->indicators->sortBy('sort_order') as $indicator)
                                            <div class="rounded-xl border border-slate-200 px-4 py-3">
                                                <div class="text-xs uppercase tracking-wide text-slate-500">{{ $indicator->code }}</div>
                                                <div class="font-medium text-slate-900">{{ $indicator->title ?? $indicator->statement }}</div>
                                                <div class="mt-1 text-sm text-slate-500">
                                                    {{ $indicator->operational_definition ?: 'Belum diisi definisi operasional.' }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>
