@props([
    'title' => '',
])

<div {{ $attributes->merge(['class' => 'sticky top-0 z-50 bg-white/90 backdrop-blur-md px-6 py-4 border-b border-slate-100']) }}>

    <div class="relative flex items-center justify-between">

        {{-- LEFT --}}
        <div class="w-10">
            {{ $left ?? '' }}
        </div>

        {{-- CENTER TITLE --}}
        <div class="absolute left-1/2 -translate-x-1/2">

            <h1 class="text-lg font-bold text-primary whitespace-nowrap">
                {{ $title }}
            </h1>

        </div>

        {{-- RIGHT --}}
        <div class="flex w-10 justify-end">
            {{ $right ?? '' }}
        </div>

    </div>

</div>