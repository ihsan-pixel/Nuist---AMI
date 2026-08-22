<x-app-layout>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Pengaturan Aplikasi</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola identitas aplikasi, logo, dukungan, dan informasi dasar lain.</p>
        </div>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <form id="app-setting-form" method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
                @csrf

                <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
                    <div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label for="app_name" class="block text-sm font-medium text-slate-700">Nama aplikasi</label>
                                <input id="app_name" name="app_name" value="{{ old('app_name', $settings['app_name']->value ?? config('app.name')) }}" class="mt-2 block w-full rounded-2xl border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-[#00553F] focus:ring-[#00553F]">
                                @error('app_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="app_version" class="block text-sm font-medium text-slate-700">Versi aplikasi</label>
                                <input id="app_version" name="app_version" value="{{ old('app_version', $settings['app_version']->value ?? '1.0.0') }}" class="mt-2 block w-full rounded-2xl border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-[#00553F] focus:ring-[#00553F]">
                                @error('app_version') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label for="app_tagline" class="block text-sm font-medium text-slate-700">Tagline</label>
                                <input id="app_tagline" name="app_tagline" value="{{ old('app_tagline', $settings['app_tagline']->value ?? 'Audit Mutu Internal') }}" class="mt-2 block w-full rounded-2xl border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-[#00553F] focus:ring-[#00553F]">
                                @error('app_tagline') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label for="app_organization" class="block text-sm font-medium text-slate-700">Nama organisasi</label>
                                <input id="app_organization" name="app_organization" value="{{ old('app_organization', $settings['app_organization']->value ?? 'NUIST') }}" class="mt-2 block w-full rounded-2xl border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-[#00553F] focus:ring-[#00553F]">
                                @error('app_organization') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="app_support_email" class="block text-sm font-medium text-slate-700">Email dukungan</label>
                                <input id="app_support_email" name="app_support_email" type="email" value="{{ old('app_support_email', $settings['app_support_email']->value ?? '') }}" class="mt-2 block w-full rounded-2xl border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-[#00553F] focus:ring-[#00553F]">
                                @error('app_support_email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="app_website" class="block text-sm font-medium text-slate-700">Website</label>
                                <input id="app_website" name="app_website" type="url" value="{{ old('app_website', $settings['app_website']->value ?? '') }}" class="mt-2 block w-full rounded-2xl border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-[#00553F] focus:ring-[#00553F]">
                                @error('app_website') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label for="app_copyright" class="block text-sm font-medium text-slate-700">Copyright</label>
                                <input id="app_copyright" name="app_copyright" value="{{ old('app_copyright', $settings['app_copyright']->value ?? '© '.date('Y').' NUIST') }}" class="mt-2 block w-full rounded-2xl border border-slate-300 px-3 py-2.5 text-sm shadow-sm focus:border-[#00553F] focus:ring-[#00553F]">
                                @error('app_copyright') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="mt-6 space-y-4">
                            <div>
                                <label for="logo" class="block text-sm font-medium text-slate-700">Logo aplikasi</label>
                                <input id="logo" name="logo" type="file" accept="image/*" class="mt-2 block w-full rounded-2xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-[#00553F] focus:ring-[#00553F]">
                                @error('logo')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" class="inline-flex items-center rounded-2xl bg-[#00553F] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#004536]">
                                Simpan Pengaturan
                            </button>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-6">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Ringkasan</h3>

                        @if (\App\Http\Controllers\Ami\AppSettingController::logoUrl())
                            <div class="mt-4 flex items-center gap-4">
                                <img src="{{ \App\Http\Controllers\Ami\AppSettingController::logoUrl() }}" alt="Logo aplikasi" class="h-16 w-16 rounded-2xl object-cover ring-1 ring-slate-200">
                                <div>
                                    <p class="text-sm font-medium text-slate-900">Logo saat ini aktif</p>
                                    <p class="text-sm text-slate-500 break-all">{{ \App\Http\Controllers\Ami\AppSettingController::logoPath() }}</p>
                                </div>
                            </div>
                        @elseif ($settings['app_logo']->value ?? null)
                            <div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                                Logo tersimpan di database, tetapi file tidak ditemukan di storage.
                            </div>
                        @else
                            <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-6 text-sm text-slate-500">
                                Belum ada logo aplikasi.
                            </div>
                        @endif

                        <div class="mt-6 grid gap-3 text-sm">
                            <div class="rounded-2xl bg-white p-4">
                                <div class="text-slate-500">Nama</div>
                                <div class="font-medium text-slate-900">{{ $settings['app_name']->value ?? config('app.name') }}</div>
                            </div>
                            <div class="rounded-2xl bg-white p-4">
                                <div class="text-slate-500">Versi</div>
                                <div class="font-medium text-slate-900">{{ $settings['app_version']->value ?? '1.0.0' }}</div>
                            </div>
                            <div class="rounded-2xl bg-white p-4">
                                <div class="text-slate-500">Dukungan</div>
                                <div class="font-medium text-slate-900">{{ $settings['app_support_email']->value ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
