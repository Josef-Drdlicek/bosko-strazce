<!DOCTYPE html>
<html lang="cs" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Bosko Strážce' }} — Bosko Strážce</title>
    <meta name="description" content="{{ $metaDescription ?? 'Antikorupční monitorovací platforma pro město Boskovice' }}">
    <link rel="canonical" href="{{ url()->current() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-50 text-gray-900 antialiased">

<nav class="bg-white border-b border-gray-200 sticky top-0 z-40">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center gap-8">
                <a href="{{ route('dashboard') }}" class="text-lg font-bold text-gray-900 tracking-tight">
                    Bosko Strážce
                </a>
                <div class="hidden md:flex items-center gap-1">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-nav-link>
                    <x-nav-link :href="route('documents.index')" :active="request()->routeIs('documents.*')">
                        Dokumenty
                    </x-nav-link>
                    <x-nav-link :href="route('contracts.index')" :active="request()->routeIs('contracts.*')">
                        Smlouvy
                    </x-nav-link>
                    <x-nav-link :href="route('entities.index')" :active="request()->routeIs('entities.*')">
                        Subjekty
                    </x-nav-link>
                </div>
            </div>

            <form action="{{ route('search') }}" method="GET" class="hidden sm:block">
                <div class="relative">
                    <input
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Hledat..."
                        class="w-64 rounded-lg border border-gray-300 bg-gray-50 px-4 py-2 text-sm placeholder:text-gray-400 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500"
                    >
                </div>
            </form>
        </div>
    </div>
</nav>

<main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
    {{ $slot }}
</main>

<footer class="border-t border-gray-200 bg-white mt-16">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <p class="text-center text-sm text-gray-500">
            Bosko Strážce &mdash; Antikorupční monitorovací platforma pro město Boskovice.
            Data z veřejných zdrojů.
        </p>
    </div>
</footer>

</body>
</html>
