<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Nuist AMI') }}</title>
        <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(0,85,63,0.10),_transparent_30%),linear-gradient(180deg,_#f7faf9_0%,_#eef4f1_100%)] flex items-center justify-center p-4 sm:p-6">
            <div class="w-full max-w-[1180px] rounded-[2.25rem] bg-white shadow-[0_30px_90px_rgba(15,23,42,0.12),0_0_0_1px_rgba(255,255,255,0.7)] overflow-hidden grid lg:grid-cols-[1.08fr_0.92fr]">
                <div class="hidden lg:flex flex-col justify-between p-12 text-white bg-[linear-gradient(160deg,#00553F_0%,#0b6b50_52%,#073d31_100%)]">
                    <div>
                        <div style="background-color:#0b3f2f;color:#ffffff;border-radius:9999px;padding:0.5rem 1rem;display:inline-flex;align-items:center;gap:0.75rem;font-size:0.75rem;font-weight:600;letter-spacing:0.22em;text-transform:uppercase;box-shadow:0 2px 8px rgba(0,0,0,0.18);">
                            <span class="h-2 w-2 rounded-full bg-emerald-300"></span>
                            Sistem AMI
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-center p-6 sm:p-10">
                    <div class="w-full max-w-md rounded-[2rem] border border-white/70 bg-white/95 p-8 shadow-[0_24px_80px_rgba(15,23,42,0.12)] backdrop-blur">
                        <div class="mb-8 text-center">
                            <img src="{{ asset('favicon.png') }}" alt="Nuist AMI" class="mx-auto mb-4 h-16 w-16 object-contain">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#00553F]">Nuist AMI LP Ma'arif NU PWNU DIY</p>
                            <h2 class="mt-2 text-2xl font-semibold text-slate-900">Masuk ke Sistem</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                Gunakan akun resmi yang telah disiapkan oleh administrator.
                            </p>
                        </div>

                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
