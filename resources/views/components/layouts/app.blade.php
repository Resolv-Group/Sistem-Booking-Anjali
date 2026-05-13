<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'Sistem Anjali')
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>

<body class="bg-gradient-to-br from-teal-50 via-white to-gray-100 text-gray-800 font-['manrope', 'sans-serif'] antialiased overflow-x-hidden"
      x-data="{ pageLoaded: false, navigating: false }"
      x-init="setTimeout(() => pageLoaded = true, 50)"
      @start-navigation.window="navigating = true">

    {{-- Global Page Transition Wrapper --}}
    <div :class="pageLoaded && !navigating ? 'opacity-100' : 'opacity-0'"
         class="transition-opacity duration-500 ease-out min-h-screen">
        @yield('content')
    </div>

    {{-- Full Screen Loader Overlay (Shows on Navigation) --}}
    <div x-show="navigating" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-[10000] flex flex-col items-center justify-center bg-white/70 backdrop-blur-md"
         x-cloak>
        <div class="relative flex h-20 w-20 items-center justify-center rounded-3xl bg-white shadow-2xl">
            <div class="absolute h-full w-full animate-ping rounded-3xl border-2 border-primary/40"></div>
            {{-- House/Anjali Logo Icon --}}
            <svg class="h-10 w-10 text-primary animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
            </svg>
        </div>
        <p class="mt-5 text-sm font-extrabold tracking-[0.2em] text-primary uppercase animate-pulse">Memuat</p>
    </div>

    {{-- Navigation Interceptor Script --}}
    <script>
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (!link) return;
            
            // Bypass for new tabs, downloads, or non-http links
            if (link.target === '_blank' || link.hasAttribute('download')) return;
            if (!link.href || link.href.startsWith('javascript:') || link.href.startsWith('mailto:') || link.href.startsWith('tel:')) return;
            
            // Bypass for cross-origin or anchor links on the same page
            try {
                const url = new URL(link.href, window.location.origin);
                if (url.origin !== window.location.origin) return;
                if (url.pathname === window.location.pathname && url.hash) return;
            } catch (err) {
                return; // Invalid URL
            }

            e.preventDefault();
            window.dispatchEvent(new Event('start-navigation'));
            
            setTimeout(() => {
                window.location.href = link.href;
            }, 300); // 300ms gives enough time for the loader to fade in beautifully
        });

        // Trigger loader on form submission
        document.addEventListener('submit', function(e) {
            window.dispatchEvent(new Event('start-navigation'));
        });

        // Handle browser back/forward cache (bfcache)
        window.addEventListener('pageshow', function(e) {
            if (e.persisted) {
                window.location.reload();
            }
        });
    </script>
</body>
</html>