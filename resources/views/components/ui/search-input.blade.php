<div class="relative">

    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
        🔍
    </div>

    <input
        type="text"
        placeholder="Cari..."
        {{ $attributes->merge([
            'class' =>
                'w-full rounded-2xl border border-gray-200 bg-white py-3 pl-10 pr-4 text-sm focus:border-primary focus:outline-none'
        ]) }}
    >

</div>