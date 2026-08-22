<x-app-layout>
    @php
        $cards = [
            ['label' => 'Status', 'value' => 'Siap dikembangkan'],
            ['label' => 'Akses', 'value' => 'Super Admin'],
            ['label' => 'Mode', 'value' => 'Terhubung ke layout utama'],
        ];
    @endphp

    <div class="space-y-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-medium uppercase tracking-[0.2em] text-[#00553F]">Admin Module</p>
                    <h1 class="mt-2 text-2xl font-semibold text-slate-900">{{ $title }}</h1>
                    <p class="mt-2 max-w-2xl text-sm text-slate-500">
                        Halaman ini disiapkan sebagai kerangka kerja modul {{ $title }} agar konsisten dengan aplikasi dan siap diisi data/flow berikutnya.
                    </p>
                </div>

                <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">
                    Terhubung ke layout AMI aktif
                </div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            @foreach ($cards as $card)
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="text-sm text-slate-500">{{ $card['label'] }}</div>
                    <div class="mt-2 text-lg font-semibold text-slate-900">{{ $card['value'] }}</div>
                </div>
            @endforeach
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-base font-semibold text-slate-900">Area kerja</h2>
                <div class="mt-4 space-y-3 text-sm text-slate-600">
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">• Tambahkan daftar data utama saat modul ini diaktifkan.</div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">• Gunakan layout, warna, dan komponen yang sama dengan halaman AMI lain.</div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">• Aksi create/edit/detail bisa ditempatkan di sini tanpa mengubah struktur dasar.</div>
                </div>
            </div>

            <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-6">
                <h2 class="text-base font-semibold text-slate-900">Status implementasi</h2>
                <div class="mt-4 space-y-3 text-sm text-slate-600">
                    <div class="rounded-2xl bg-white px-4 py-3">Route: aktif</div>
                    <div class="rounded-2xl bg-white px-4 py-3">Sidebar: aktif</div>
                    <div class="rounded-2xl bg-white px-4 py-3">Layout: konsisten</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
