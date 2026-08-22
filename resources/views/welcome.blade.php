<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="application-name" content="Nuist AMI">
        <meta name="theme-color" content="#00553F">
        <title>Nuist AMI</title>
        <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#f4f7f5] text-slate-900">
        <main class="flex min-h-screen items-center justify-center px-6">
            <section class="w-full max-w-2xl rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm">
                <div class="flex items-center gap-4">
                    <img src="{{ asset('favicon.png') }}" alt="Nuist AMI" class="h-14 w-14 rounded-2xl object-cover">
                    <div>
                        <p class="text-sm font-medium uppercase tracking-[0.2em] text-[#00553F]">Nuist AMI</p>
                        <h1 class="mt-1 text-3xl font-semibold text-slate-900">Sistem AMI NUIST</h1>
                    </div>
                </div>
                <p class="mt-5 max-w-xl text-sm leading-6 text-slate-600">
                    Aplikasi ini digunakan untuk pengelolaan periode, instrumen, penugasan, pengisian sekolah, audit, monitoring, dan laporan AMI.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('login') }}" class="inline-flex items-center rounded-xl bg-[#00553F] px-5 py-3 text-sm font-semibold text-white shadow-sm">
                        Login
                    </a>
                    <a href="{{ route('password.request') }}" class="inline-flex items-center rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700">
                        Forgot Password
                    </a>
                </div>
            </section>
        </main>
    </body>
</html>
