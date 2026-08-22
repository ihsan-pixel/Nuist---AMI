<x-app-layout>
    @php
        $role = $role ?? 'user';
        $roleLabel = match ($role) {
            'super_admin' => 'Super Admin',
            'pengurus' => 'Pengurus',
            'auditor' => 'Auditor',
            'sekolah' => 'Sekolah',
            default => 'Pengguna',
        };
    @endphp

    <div class="mb-6 rounded-3xl border border-slate-200 bg-white px-6 py-6 shadow-sm">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div class="max-w-3xl">
                <div class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">
                    Dashboard {{ $roleLabel }}
                </div>
                <h1 class="mt-3 text-3xl font-semibold text-slate-900">Selamat datang, {{ $name ?? 'Pengguna' }}</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">
                    {{ $role === 'sekolah'
                        ? 'Gunakan dashboard ini untuk memantau progres pengisian AMI, melihat indikator yang belum lengkap, dan menyiapkan bukti dengan rapi.'
                        : 'Gunakan dashboard ini untuk memantau pekerjaan inti, status periode, dan fokus tindakan sesuai peran Anda di sistem AMI.' }}
                </p>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 lg:w-[360px]">
                <div class="rounded-2xl bg-slate-50 p-4">
                    <div class="text-xs font-medium uppercase tracking-wide text-slate-500">Periode aktif</div>
                    <div class="mt-2 text-sm font-semibold text-slate-900">{{ $activePeriod?->name ?? 'Belum ada' }}</div>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4">
                    <div class="text-xs font-medium uppercase tracking-wide text-slate-500">Status</div>
                    <div class="mt-2 text-sm font-semibold text-slate-900">{{ $activePeriod?->status ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach (($quickStats ?? []) as $stat)
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="text-sm font-medium text-slate-500">{{ $stat['label'] }}</div>
                <div class="mt-3 text-2xl font-semibold text-slate-900">{{ $stat['value'] }}</div>
            </div>
        @endforeach
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-slate-900">Informasi Periode</h2>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">{{ $activePeriod?->year ?? '-' }}</span>
            </div>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div class="rounded-2xl bg-slate-50 p-4">
                    <div class="text-sm text-slate-500">Rentang periode</div>
                    <div class="mt-1 font-medium text-slate-900">
                        {{ $activePeriod?->start_date?->format('d M Y') ?? '-' }} - {{ $activePeriod?->end_date?->format('d M Y') ?? '-' }}
                    </div>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4">
                    <div class="text-sm text-slate-500">Submisi</div>
                    <div class="mt-1 font-medium text-slate-900">
                        {{ $activePeriod?->submission_start_at?->format('d M Y') ?? '-' }} - {{ $activePeriod?->submission_end_at?->format('d M Y') ?? '-' }}
                    </div>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4">
                    <div class="text-sm text-slate-500">Review</div>
                    <div class="mt-1 font-medium text-slate-900">
                        {{ $activePeriod?->review_start_at?->format('d M Y') ?? '-' }} - {{ $activePeriod?->review_end_at?->format('d M Y') ?? '-' }}
                    </div>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4">
                    <div class="text-sm text-slate-500">Ruang kerja</div>
                    <div class="mt-1 font-medium text-slate-900">
                        {{ $roleLabel }}
                    </div>
                </div>
            </div>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-900">Fokus Utama</h2>
            <div class="mt-4 space-y-3">
                @forelse (($highlights ?? []) as $highlight)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                        {{ $highlight }}
                    </div>
                @empty
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-500">
                        Tidak ada data ringkasan.
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
