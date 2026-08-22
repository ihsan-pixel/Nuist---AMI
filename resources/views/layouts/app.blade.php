<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Nuist AMI') }}</title>
        <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#F6F8F7] text-slate-800">
        <div class="min-h-screen lg:pl-72" x-data="{ sidebarOpen: false }">
            @include('layouts.partials.sidebar')
            <div class="flex min-h-screen flex-col">
                <header class="sticky top-0 z-20 border-b border-slate-200/80 bg-white/90 backdrop-blur">
                    <div class="flex h-16 items-center px-4 sm:px-6 lg:px-8">
                        <button class="lg:hidden rounded-lg border border-slate-200 px-3 py-2 text-sm" @click="sidebarOpen = true">Menu</button>
                        <div class="ml-auto flex items-center gap-3">
                            <div class="hidden text-right sm:block">
                                <div class="text-sm font-medium text-slate-900">{{ auth()->user()->name ?? '' }}</div>
                                <div class="text-xs text-slate-500">{{ auth()->user()->role ?? '' }}</div>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </header>
                <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                    {{ $slot }}
                </main>
                <footer class="mt-auto border-t border-slate-200 bg-white px-4 py-4 text-sm text-slate-500 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                        <span>{{ config('app.name', 'NUIST AMI') }}</span>
                        <span>Audit Mutu Internal</span>
                    </div>
                </footer>
            </div>
        </div>
    </body>
</html>
