@props([
    'label' => '',
    'name' => '',
    'placeholder' => 'Pilih opsi',
    'options' => [], // Format: ['value' => 'Label']
])

<div class="space-y-2" 
     x-data="{ 
        open: false, 
        selected: '{{ old($name) }}',
        label: '{{ $placeholder }}',
        init() {
            if(this.selected) {
                const options = {{ json_encode($options) }};
                this.label = options[this.selected] || '{{ $placeholder }}';
            }
        }
     }" 
     @click.outside="open = false">
    
    @if ($label)
        <label class="mb-1.5 block text-sm font-semibold text-gray-700 ml-1">
            {{ $label }}
        </label>
    @endif

    <div class="relative">
        {{-- Hidden Input untuk Form Submission --}}
        <input type="hidden" name="{{ $name }}" x-model="selected">

        {{-- Trigger Button (Didesain mirip input) --}}
        <button 
            type="button"
            @click="open = !open"
            class="flex h-14 w-full items-center justify-between rounded-2xl border bg-white/50 px-5 text-left text-sm font-medium transition-all focus:outline-none focus:ring-4 {{ $name && $errors->has($name) ? '!border-red-500 focus:ring-red-500/20 text-red-900' : 'border-gray-200 text-gray-700 focus:border-primary focus:ring-primary/10' }}"
            :class="open ? '{{ $name && $errors->has($name) ? '!border-red-500 ring-4 ring-red-500/20' : 'border-primary ring-4 ring-primary/10' }}' : ''">
            
            <span :class="selected ? 'text-gray-900' : 'text-gray-400'" x-text="label"></span>
            
            <svg xmlns="http://www.w3.org/2000/svg" 
                 class="h-5 w-5 text-gray-400 transition-transform duration-200" 
                 :class="open ? 'rotate-180' : ''"
                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        {{-- Dropdown Menu (Premium Glassmorphism) --}}
        <div 
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-[-10px]"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-[-10px]"
            class="absolute z-50 mt-2 w-full overflow-hidden rounded-2xl border border-white/40 bg-white/80 p-2 shadow-2xl backdrop-blur-xl"
            x-cloak>
            
            @foreach($options as $value => $text)
                <div 
                    @click="selected = '{{ $value }}'; label = '{{ $text }}'; open = false"
                    class="cursor-pointer rounded-xl px-4 py-3 text-base transition-colors hover:bg-primary/10 hover:text-primary font-medium"
                    :class="selected === '{{ $value }}' ? 'bg-primary text-white hover:bg-primary hover:text-white' : 'text-gray-700'">
                    {{ $text }}
                </div>
            @endforeach
        </div>
    </div>

    @if($name)
        @error($name)
            <p class="mt-1 flex items-center gap-1 text-sm font-semibold text-red-500 ml-1">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                {{ $message }}
            </p>
        @enderror
    @endif
</div>