@props([
    'label' => '',
    'name' => '',
])

<div class="space-y-2">
    @if ($label)
        <label class="text-sm font-medium text-gray-700">
            {{ $label }}
        </label>
    @endif

    <input @if ($name) name="{{ $name }}" @endif
        @if ($name && request()->isMethod('post') && !str_contains($attributes->get('type', ''), 'password')) value="{{ old($name) }}" @endif
        {{ $attributes->merge([
            'class' =>
                'w-full rounded-xl border px-4 py-3 text-sm focus:outline-none transition-colors ' .
                ($name && $errors->has($name)
                    ? '!border-red-500 focus:!border-red-500 focus:ring-4 focus:!ring-red-500/20'
                    : 'border-gray-200 focus:border-primary focus:ring-primary/20'),
        ]) }}>

    @if ($name)
        @error($name)
            <p class="mt-1 flex items-center gap-1 text-sm font-semibold text-red-500">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ $message }}
            </p>
        @enderror
    @endif
</div>
