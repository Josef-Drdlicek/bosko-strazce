@props(['title', 'description', 'badge' => null])

<div class="relative overflow-hidden rounded-3xl bg-slate-950 px-6 py-10 text-white shadow-xl sm:px-10 sm:py-12 animate-fade-up">
    <div class="absolute inset-0 dot-grid opacity-20"></div>
    <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full bg-indigo-500/20 blur-3xl"></div>
    <div class="absolute -left-12 -bottom-16 h-48 w-48 rounded-full bg-violet-500/15 blur-3xl"></div>
    <div class="relative">
        @if($badge)
            <span class="mb-3 inline-flex items-center rounded-full bg-white/10 backdrop-blur-sm px-3 py-1.5 text-xs font-semibold text-white/90 ring-1 ring-white/20">{{ $badge }}</span>
        @endif
        <h1 class="text-3xl font-black tracking-tight sm:text-4xl">{{ $title }}</h1>
        <p class="mt-3 max-w-2xl text-slate-300 text-base leading-relaxed">{{ $description }}</p>
    </div>
</div>
