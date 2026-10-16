<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Selaras') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --mc-ink: #16302B;
            --mc-primary: #2F6B5A;
            --mc-soft: #E3F0EA;
            --mc-line: #D9E5DE;
            --mc-muted: #5B7268;
            --mc-sun: #E8A83A;
            --mc-side-text: #B9CFC5;
            --mc-bg: #F5F9F6;
        }

        body {
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            background: var(--mc-bg);
            color: var(--mc-ink);
        }

        .mc-brand-panel {
            background: var(--mc-ink);
            color: #fff;
        }

        .mc-card {
            background: #fff;
            border: 1px solid var(--mc-line);
            border-radius: 1.5rem;
            box-shadow: 0 18px 40px -24px rgba(22, 48, 43, .25);
        }

        .mc-muted { color: var(--mc-muted); }
        .mc-faint { color: #8CA097; }

        .mc-eyebrow {
            display: inline-block;
            font-size: .75rem;
            font-weight: 600;
            padding: .25rem .7rem;
            border-radius: 9999px;
            background: var(--mc-soft);
            color: var(--mc-primary);
            margin-bottom: .75rem;
        }

        .mc-input {
            display: block;
            width: 100%;
            border: 1px solid #C9D6CF;
            border-radius: .875rem;
            padding: .75rem .9rem;
            background: #fff;
            color: var(--mc-ink);
            font-size: 1rem;
            min-height: 2.75rem;
        }

        .mc-input::placeholder { color: #9DB0A6; }

        .mc-input:focus {
            outline: none;
            border-color: var(--mc-primary);
            box-shadow: 0 0 0 3px var(--mc-soft);
        }

        .mc-label {
            display: block;
            font-size: .875rem;
            font-weight: 600;
            margin-bottom: .5rem;
            color: var(--mc-ink);
        }

        .mc-link-soft { color: var(--mc-primary); font-weight: 600; }
        .mc-link-soft:hover { text-decoration: underline; }

        .mc-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 2.9rem;
            padding: .6rem 1.1rem;
            border-radius: .875rem;
            font-size: .875rem;
            font-weight: 600;
            background: var(--mc-ink);
            color: #fff;
            transition: background .2s;
        }

        .mc-btn:hover { background: var(--mc-primary); }

        .mc-check {
            border-radius: .375rem;
            border: 1px solid #C9D6CF;
            color: var(--mc-primary);
        }

        .mc-check:focus {
            box-shadow: 0 0 0 3px var(--mc-soft);
            border-color: var(--mc-primary);
        }

        .mc-btn:focus-visible,
        .mc-link-soft:focus-visible {
            outline: 3px solid var(--mc-sun);
            outline-offset: 2px;
        }
    </style>
</head>

<body class="antialiased">

    {{-- Logo daun --}}
    @php
        $logo = '<svg viewBox="0 0 32 32" class="w-10 h-10 shrink-0" aria-hidden="true">
            <rect width="32" height="32" rx="10" fill="#2F6B5A"/>
            <path d="M9.5 22.5C9 14 14 9 22.5 9.5 23 18 18 23 9.5 22.5Z" fill="#fff"/>
            <path d="M9.5 22.5 17 15" stroke="#2F6B5A" stroke-width="1.6" stroke-linecap="round"/>
        </svg>';
    @endphp

    <div class="min-h-screen grid lg:grid-cols-2">

        {{-- PANEL BRAND (desktop) --}}
        <aside class="mc-brand-panel hidden lg:flex flex-col justify-between p-12 xl:p-16">

            <a href="/" class="flex items-center gap-3">
                {!! $logo !!}
                <span class="text-xl font-semibold">Selaras</span>
            </a>

            <div class="max-w-md">
                <h2 class="text-4xl font-semibold leading-tight">
                    Rencanakan Harimu,<br>Jaga Dirimu.
                </h2>

                <p class="mt-4 leading-7" style="color: var(--mc-side-text);">
                    Jadwal, task, dan kondisimu dalam satu tempat.
                </p>
            </div>

            <div></div>

        </aside>


        {{-- AREA FORM --}}
        <main class="flex flex-col items-center justify-center px-4 py-10 sm:px-8">

            {{-- Brand (mobile) --}}
            <a href="/" class="lg:hidden flex flex-col items-center text-center mb-8">
                <span class="flex items-center gap-3">
                    {!! $logo !!}
                    <span class="text-xl font-semibold">Selaras</span>
                </span>
                <span class="text-sm mc-muted mt-2">Rencanakan Harimu, Jaga Dirimu.</span>
            </a>

            <div class="mc-card w-full max-w-md p-6 sm:p-8">
                {{ $slot }}
            </div>

        </main>

    </div>

</body>

</html>