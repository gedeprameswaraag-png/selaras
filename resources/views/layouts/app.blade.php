<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">

    <title>{{ $title ?? 'Selaras' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Font: Fraunces (judul) + Instrument Sans (isi) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    {{--
        FILOSOFI DESAIN: "Pelan-pelan"
        Hijau pinus (tenang), kabut sage (lega), satu titik kuning matahari (penanda "kamu di sini").
        Sudut lembut, ruang lega, satu-satunya gerak: lingkaran napas di sidebar.
        Semua gaya ada di blok ini. Tidak ada perubahan pada route, form, ID, atau logika.
    --}}
    <style>
        :root {
            --mc-bg: #EEF3EF;
            --mc-ink: #16302B;
            --mc-muted: #5F7570;
            --mc-line: #DAE4DE;
            --mc-primary: #2E6B5E;
            --mc-soft: #DCEBE4;
            --mc-sun: #F0B75A;
            --mc-side-text: #B9CFC6;
        }

        body {
            background: var(--mc-bg);
            color: var(--mc-ink);
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        h1,
        h2,
        h3 {
            font-family: 'Fraunces', Georgia, serif;
        }

        /* ---------- Sidebar ---------- */
        .mc-sidebar {
            background: var(--mc-ink);
            color: var(--mc-side-text);
            border-right: 0;
            width: 18rem;
            max-width: 85vw;
            overflow: hidden;
            transform: translateX(-100%);
            transition: transform .3s ease;
        }

        .mc-sidebar.mc-open {
            transform: none;
        }

        @media (min-width: 1024px) {
            .mc-sidebar {
                transform: none;
            }

            .mc-main {
                margin-left: 18rem;
            }
        }

        .mc-sidebar .mc-logo {
            border-bottom: 1px solid rgba(255, 255, 255, .1);
        }

        .mc-nav-label {
            color: var(--mc-side-text);
            opacity: .7;
        }

        .mc-link {
            color: var(--mc-side-text);
            border-radius: 1rem;
        }

        .mc-link:hover {
            background: rgba(255, 255, 255, .08);
            color: #fff;
        }

        .mc-link.mc-active {
            background: var(--mc-bg);
            color: var(--mc-ink);
        }

        .mc-link.mc-active::after {
            content: "";
            margin-left: auto;
            width: .5rem;
            height: .5rem;
            border-radius: 9999px;
            background: var(--mc-sun);
        }

        .mc-side-foot {
            border-top: 1px solid rgba(255, 255, 255, .1);
        }

        /* ---------- Lingkaran napas ---------- */
        .mc-breath {
            width: 2.5rem;
            height: 2.5rem;
            flex-shrink: 0;
            border-radius: 9999px;
            background: radial-gradient(circle at 35% 35%, #F7D08F, var(--mc-sun));
            animation: mc-breathe 8s ease-in-out infinite;
        }

        @keyframes mc-breathe {

            0%,
            100% {
                transform: scale(.72);
                opacity: .75;
            }

            50% {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* ---------- Header ---------- */
        .mc-header {
            background: var(--mc-bg);
            border-bottom: 1px solid var(--mc-line);
        }

        .mc-avatar {
            background: var(--mc-primary);
            color: #fff;
        }

        /* ---------- Navigasi bawah (HP & tablet) ---------- */
        .mc-bottom {
            background: #fff;
            border-top: 1px solid var(--mc-line);
            padding-bottom: env(safe-area-inset-bottom, 0px);
        }

        .mc-tab {
            color: var(--mc-muted);
        }

        .mc-tab .mc-pill {
            width: 3.25rem;
            height: 1.85rem;
            border-radius: 9999px;
        }

        .mc-tab.mc-active {
            color: var(--mc-primary);
            font-weight: 600;
        }

        .mc-tab.mc-active .mc-pill {
            background: var(--mc-soft);
        }

        /* ---------- Toast ---------- */
        .mc-toast {
            border-color: var(--mc-line);
            border-left: 4px solid var(--mc-primary);
        }

        @media (prefers-reduced-motion: reduce) {
            .mc-breath {
                animation: none;
            }

            .mc-sidebar {
                transition: none;
            }
        }
    </style>

</head>


<body>

    @php
        // Sapaan sesuai jam (mengikuti timezone di config/app.php)
        $mcHour = now()->hour;

        if ($mcHour >= 4 && $mcHour < 11) {
            $mcGreeting = 'Selamat pagi';
            $mcReminder = 'Semangat menjalani hari.';
        } elseif ($mcHour >= 11 && $mcHour < 18) {
            $mcGreeting = 'Selamat siang';
            $mcReminder = 'Jangan lupa istirahat.';
        } else {
            $mcGreeting = 'Selamat malam';
            $mcReminder = 'Kamu sudah melakukan yang terbaik.';
        }

        // [route, pola aktif, label, path ikon]
        $mcMenu = [
            ['dashboard', 'dashboard', 'Dashboard', ['M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4v-6h4v6h4a1 1 0 001-1V10']],
            ['calendar', 'calendar*', 'Calendar', ['M6.75 3v2.25M17.25 3v2.25M3.75 9.75h16.5M5.25 5.25h13.5A1.5 1.5 0 0120.25 6.75v12A1.5 1.5 0 0118.75 20.25H5.25a1.5 1.5 0 01-1.5-1.5v-12a1.5 1.5 0 011.5-1.5z']],
            ['tasks.index', 'tasks.*', 'Tasks', ['M9 11.25l2 2 4-4m5.25 2.25a8.25 8.25 0 11-16.5 0 8.25 8.25 0 0116.5 0z']],
            ['insights', 'insights', 'Insights', ['M3 3v18h18', 'M7 16l4-5 3 3 5-7']],
            ['settings', 'settings', 'Settings', ['M10.5 6h9m-9 6h9m-9 6h9M4.5 6h.008v.008H4.5V6zm0 6h.008v.008H4.5V12zm0 6h.008v.008H4.5V18z']],
        ];
    @endphp


    {{-- OVERLAY (HP & tablet) --}}
    <div id="mc-overlay" class="hidden fixed inset-0 z-40 lg:hidden" style="background: rgba(22,48,43,.6);"></div>


    {{-- SIDEBAR --}}
    <aside id="mc-sidebar" class="mc-sidebar fixed top-0 left-0 z-50 h-screen flex flex-col">

        {{-- LOGO --}}
        <div class="mc-logo h-20 px-6 shrink-0 flex items-center justify-between">

            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 min-w-0">

                {{-- Ikon daun --}}
                <div class="w-10 h-10 shrink-0 rounded-full flex items-center justify-center"
                     style="background: var(--mc-sun); color: var(--mc-ink);">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-6 h-6" aria-hidden="true">
                        <path fill="currentColor"
                              d="M20 4C11 4 5 8.5 5 15.5c0 1.1.2 2 .6 2.9C6.6 14.2 9.6 10.6 14 8.5 10.8 11 8.4 14.4 7.6 19.3c.9.4 1.9.6 2.9.6C17 19.9 20 13 20 4z" />
                    </svg>
                </div>

                <div class="min-w-0">
                    <h1 class="font-bold text-xl leading-none text-white">Selaras</h1>
                    <p class="text-[11px] mt-1 leading-tight" style="color: var(--mc-side-text);">
                        Rencanakan Harimu, Jaga Dirimu.
                    </p>
                </div>

            </a>

            {{-- Tutup (HP & tablet) --}}
            <button type="button" id="mc-close"
                    class="lg:hidden w-11 h-11 rounded-full flex items-center justify-center text-white shrink-0"
                    aria-label="Tutup menu">
                ✕
            </button>

        </div>


        {{-- NAVIGATION (tanpa scroll) --}}
        <nav class="flex-1 min-h-0 px-4 py-5">

            <p class="mc-nav-label px-4 mb-3 text-[11px] font-semibold uppercase tracking-wider">
                Menu
            </p>

            @foreach ($mcMenu as [$mcRoute, $mcMatch, $mcLabel, $mcPaths])
                <a href="{{ route($mcRoute) }}"
                   class="mc-link flex items-center gap-3 px-4 py-2.5 mb-1 text-sm font-medium transition {{ request()->routeIs($mcMatch) ? 'mc-active' : '' }}">

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.8" stroke="currentColor" class="w-5 h-5 shrink-0">
                        @foreach ($mcPaths as $mcD)
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $mcD }}" />
                        @endforeach
                    </svg>

                    <span>{{ $mcLabel }}</span>

                </a>
            @endforeach

        </nav>


        {{-- SAPAAN HARIAN + LINGKARAN NAPAS --}}
        <div class="mx-4 mb-3 shrink-0 rounded-2xl p-4 flex items-center gap-4"
             style="background: rgba(255,255,255,.06);">
            <div class="mc-breath" aria-hidden="true"></div>
            <div class="min-w-0">
                <p class="text-xs" style="color: var(--mc-side-text);">{{ $mcGreeting }}</p>
                <p class="text-sm leading-snug text-white mt-0.5">{{ $mcReminder }}</p>
            </div>
        </div>


        {{-- LOGOUT --}}
        <div class="mc-side-foot shrink-0 p-4">

            <form method="POST" action="{{ route('logout') }}">

                @csrf

                <button type="submit"
                        class="mc-link w-full flex items-center gap-3 px-4 py-2.5 text-sm font-medium transition">

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.8" stroke="currentColor" class="w-5 h-5 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3-3H9m0 0l3-3m-3 3l3 3" />
                    </svg>

                    <span>Logout</span>

                </button>

            </form>

        </div>

    </aside>


    {{-- MAIN AREA --}}
    <div class="mc-main min-h-screen">


        {{-- TOP BAR --}}
        <header class="mc-header h-20 flex items-center gap-3 px-4 sm:px-6 lg:px-8 sticky top-0 z-30">

            {{-- Tombol menu (HP & tablet) --}}
            <button type="button" id="mc-open"
                    class="lg:hidden -ml-2 w-11 h-11 rounded-full flex items-center justify-center shrink-0"
                    aria-label="Buka menu">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.8" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h10" />
                </svg>
            </button>

            {{-- Judul + tanggal --}}
            <div class="min-w-0 flex-1">

                <h2 class="text-xl sm:text-2xl font-semibold truncate" style="color: var(--mc-ink);">
                    {{ $title ?? 'Dashboard' }}
                </h2>

                <p class="text-sm mt-0.5 truncate" style="color: var(--mc-muted);">
                    {{ now()->format('l, d F Y') }}
                </p>

            </div>

            {{-- User (kanan) --}}
            <div class="flex items-center gap-3 shrink-0 min-w-0">

                <div class="hidden sm:block min-w-0 text-right">

                    <p class="text-sm font-semibold truncate max-w-[12rem]" style="color: var(--mc-ink);">
                        {{ auth()->user()->name }}
                    </p>

                    <p class="text-xs truncate max-w-[12rem]" style="color: var(--mc-muted);">
                        {{ auth()->user()->email }}
                    </p>

                </div>

                <div class="mc-avatar w-10 h-10 shrink-0 rounded-full flex items-center justify-center font-semibold"
                     title="{{ auth()->user()->name }}">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>

            </div>

        </header>


        {{-- PAGE CONTENT --}}
        <main class="p-4 sm:p-6 lg:p-8 pb-28 lg:pb-8">

            {{-- GLOBAL SUCCESS TOAST --}}
            @if (session('success'))

            <div id="success-toast"
                 class="mc-toast fixed top-24 right-4 left-4 sm:left-auto sm:right-8 z-[100]
                        sm:w-full sm:max-w-sm rounded-2xl border bg-white shadow-xl px-5 py-4">

                <div class="flex items-start gap-3">

                    {{-- Icon --}}
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full font-bold"
                         style="background: var(--mc-soft); color: var(--mc-primary);">
                        ✓
                    </div>

                    {{-- Message --}}
                    <div class="flex-1 min-w-0">

                        <p class="text-sm font-semibold" style="color: var(--mc-ink);">
                            Berhasil
                        </p>

                        <p class="mt-1 text-sm" style="color: var(--mc-muted);">
                            {{ session('success') }}
                        </p>

                    </div>

                    {{-- Close --}}
                    <button type="button" id="close-success-toast"
                            class="text-slate-400 hover:text-slate-700 transition">
                        ✕
                    </button>

                </div>

            </div>

            @endif


            @yield('content')

        </main>

    </div>


    {{-- NAVIGASI BAWAH (HP & tablet) --}}
    <nav class="mc-bottom fixed bottom-0 inset-x-0 z-30 lg:hidden">

        <div class="grid grid-cols-5 max-w-xl mx-auto">

            @foreach ($mcMenu as [$mcRoute, $mcMatch, $mcLabel, $mcPaths])
                <a href="{{ route($mcRoute) }}"
                   class="mc-tab flex flex-col items-center gap-1 pt-2 pb-2 text-[11px] font-medium {{ request()->routeIs($mcMatch) ? 'mc-active' : '' }}">

                    <span class="mc-pill flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                            @foreach ($mcPaths as $mcD)
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $mcD }}" />
                            @endforeach
                        </svg>
                    </span>

                    <span>{{ $mcLabel }}</span>
                </a>
            @endforeach

        </div>

    </nav>


    {{-- SUCCESS TOAST SCRIPT --}}
    @if (session('success'))

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const toast = document.getElementById('success-toast');
            const closeButton = document.getElementById('close-success-toast');

            if (!toast) {
                return;
            }

            // Tutup menggunakan tombol X
            if (closeButton) {
                closeButton.addEventListener('click', function() {
                    toast.remove();
                });
            }

            // Hilang otomatis setelah 4 detik
            setTimeout(function() {
                if (toast) {
                    toast.remove();
                }
            }, 4000);

        });
    </script>

    @endif


    {{-- MENU HP & TABLET (JavaScript biasa, tidak memakai Alpine) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            var sidebar = document.getElementById('mc-sidebar');
            var overlay = document.getElementById('mc-overlay');
            var openBtn = document.getElementById('mc-open');
            var closeBtn = document.getElementById('mc-close');

            if (!sidebar || !overlay) {
                return;
            }

            function openMenu() {
                sidebar.classList.add('mc-open');
                overlay.classList.remove('hidden');
                document.documentElement.style.overflow = 'hidden';
            }

            function closeMenu() {
                sidebar.classList.remove('mc-open');
                overlay.classList.add('hidden');
                document.documentElement.style.overflow = '';
            }

            if (openBtn) openBtn.addEventListener('click', openMenu);
            if (closeBtn) closeBtn.addEventListener('click', closeMenu);
            overlay.addEventListener('click', closeMenu);

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeMenu();
            });

        });
    </script>


    {{-- ALPINE JS --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>


</body>

</html>