<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="Email Akun" />
            <x-text-input id="email" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 shadow-sm focus:border-[#00553F] focus:ring-[#00553F]" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Kata Sandi" />
            <x-text-input id="password" class="mt-2 block w-full rounded-2xl border-slate-300 px-4 py-3 shadow-sm focus:border-[#00553F] focus:ring-[#00553F]" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between gap-3">
            <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-slate-600">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-[#00553F] shadow-sm focus:ring-[#00553F]" name="remember">
                <span>Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-[#00553F] hover:text-[#004536]" href="{{ route('password.request') }}">
                    Lupa kata sandi?
                </a>
            @endif
        </div>

        <x-primary-button class="w-full justify-center rounded-2xl bg-[#00553F] px-4 py-3 text-sm font-semibold shadow-lg shadow-emerald-900/20 hover:bg-[#004536]">
            Masuk
        </x-primary-button>
    </form>
</x-guest-layout>
