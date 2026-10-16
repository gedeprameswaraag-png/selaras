@extends('layouts.app')

@section('content')

{{-- Gaya halaman ini (memakai warna dari layout) --}}
<style>
    .mc-card { background: #fff; border: 1px solid var(--mc-line); border-radius: 1.25rem; }
    .mc-muted { color: var(--mc-muted); }
    .mc-faint { color: #8CA097; }
    .mc-divider { border-color: var(--mc-line); }
    .mc-field-label { font-size: .8125rem; font-weight: 600; color: var(--mc-muted); }
    .mc-tile { background: #F5F9F6; border: 1px solid var(--mc-line); border-radius: 1rem; }
    .mc-row { transition: background .2s; }
    .mc-row:hover { background: #F5F9F6; }

    .mc-btn {
        display: inline-flex; align-items: center; justify-content: center;
        min-height: 2.75rem; padding: .6rem 1.25rem; border-radius: .875rem;
        font-size: .875rem; font-weight: 600; transition: background .2s, color .2s;
    }
    .mc-btn-primary { background: var(--mc-ink); color: #fff; }
    .mc-btn-primary:hover { background: var(--mc-primary); }
    .mc-btn-ghost { border: 1px solid #C9D6CF; color: var(--mc-muted); background: #fff; }
    .mc-btn-ghost:hover { background: #F5F9F6; color: var(--mc-ink); }
    .mc-btn:focus-visible { outline: 3px solid var(--mc-sun); outline-offset: 2px; }

    .mc-back {
        display: inline-flex; align-items: center; gap: .5rem;
        min-height: 2.5rem; padding: .4rem 1rem .4rem .8rem;
        border-radius: 9999px; background: #fff; border: 1px solid var(--mc-line);
        color: var(--mc-ink); font-size: .875rem; font-weight: 600;
        transition: background .2s, border-color .2s, color .2s;
    }
    .mc-back:hover { background: var(--mc-soft); border-color: var(--mc-primary); color: var(--mc-primary); }
    .mc-back:focus-visible { outline: 3px solid var(--mc-sun); outline-offset: 2px; }

    .mc-badge { border-radius: 9999px; padding: .375rem .75rem; font-size: .75rem; font-weight: 500; }
    .mc-b-high { background: #FBEAE8; color: #B4443C; }
    .mc-b-med { background: #FDF3DE; color: #8A5A0B; }
    .mc-b-low { background: var(--mc-soft); color: var(--mc-primary); }

    .mc-alert-ok { background: var(--mc-soft); border: 1px solid #BFD9CE; border-radius: 1.25rem; }
    .mc-alert-bad { background: #FBF1F0; border: 1px solid #EBC9C5; border-radius: 1.25rem; }
</style>

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold">
                Smart Scheduling
            </h1>

            <p class="mt-1 text-sm mc-muted">
                Cari waktu yang tersedia untuk menyelesaikan task sebelum deadline.
            </p>
        </div>

        <a
            href="{{ route('tasks.index') }}"
            class="mc-back self-start sm:self-auto">

            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 shrink-0" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
            Kembali ke Task

        </a>

    </div>


    {{-- Task Information --}}
    <div class="mc-card">

        <div class="px-4 sm:px-6 py-5">

            <div class="flex flex-col sm:flex-row
                        sm:items-start sm:justify-between gap-4">

                <div class="min-w-0">

                    <p class="mc-field-label">
                        Task yang dianalisis
                    </p>

                    <h2 class="mt-1 text-xl sm:text-2xl font-semibold break-words">
                        {{ $task->title }}
                    </h2>

                    @if ($task->description)

                        <p class="mt-2 text-sm mc-muted break-words">
                            {{ $task->description }}
                        </p>

                    @endif

                </div>


                {{-- Priority --}}
                @if ($task->priority === 'high')

                    <span class="self-start mc-badge mc-b-high">
                        Prioritas Tinggi
                    </span>

                @elseif ($task->priority === 'medium')

                    <span class="self-start mc-badge mc-b-med">
                        Prioritas Sedang
                    </span>

                @else

                    <span class="self-start mc-badge mc-b-low">
                        Prioritas Rendah
                    </span>

                @endif

            </div>


            {{-- Task Meta --}}
            <div class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-4">

                <div class="mc-tile p-4">

                    <p class="text-xs mc-muted">
                        Deadline
                    </p>

                    <p class="mt-1 text-sm font-semibold">
                        {{ \Carbon\Carbon::parse($task->deadline)->translatedFormat('d F Y, H:i') }}
                    </p>

                </div>


                <div class="mc-tile p-4">

                    <p class="text-xs mc-muted">
                        Estimasi Pengerjaan
                    </p>

                    <p class="mt-1 text-sm font-semibold">
                        {{ $feasibility['required_minutes'] }} menit
                    </p>

                </div>


                <div class="mc-tile p-4">

                    <p class="text-xs mc-muted">
                        Waktu Tersedia
                    </p>

                    <p class="mt-1 text-sm font-semibold">
                        {{ $feasibility['available_minutes'] }} menit
                    </p>

                </div>

            </div>

        </div>

    </div>


    {{-- Success / Error --}}
    @if ($errors->any())

        <div class="mc-alert-bad p-5">

            <div class="flex items-start gap-3">

                <div class="flex h-9 w-9 shrink-0
                            items-center justify-center
                            rounded-full font-bold"
                     style="background: #F6DAD7; color: #B4443C;">

                    !

                </div>

                <div>

                    <h3 class="font-semibold" style="color: #8E342D;">
                        Jadwal tidak dapat dibuat
                    </h3>

                    <ul class="mt-1 text-sm list-disc list-inside" style="color: #B4443C;">

                        @foreach ($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            </div>

        </div>

    @endif


    {{-- Feasibility --}}
    @if ($feasibility['is_feasible'])

        <div class="mc-alert-ok p-5 sm:p-6">

            <div class="flex items-start gap-3">

                <div class="flex h-9 w-9 shrink-0
                            items-center justify-center
                            rounded-full bg-white"
                     style="color: var(--mc-primary);">

                    ✓

                </div>

                <div>

                    <h3 class="font-semibold" style="color: var(--mc-ink);">
                        Task bisa dijadwalkan
                    </h3>

                    <p class="mt-1 text-sm" style="color: var(--mc-primary);">
                        Tersedia {{ $feasibility['available_minutes'] }}
                        menit sebelum deadline, sedangkan task membutuhkan
                        {{ $feasibility['required_minutes'] }} menit.
                    </p>

                </div>

            </div>

        </div>

    @else

        <div class="mc-alert-bad p-5 sm:p-6">

            <div class="flex items-start gap-3">

                <div class="flex h-9 w-9 shrink-0
                            items-center justify-center
                            rounded-full"
                     style="background: #F6DAD7; color: #B4443C;">

                    !

                </div>

                <div>

                    <h3 class="font-semibold" style="color: #8E342D;">
                        Waktu pengerjaan tidak mencukupi
                    </h3>

                    <p class="mt-1 text-sm" style="color: #B4443C;">
                        Kamu masih kekurangan
                        {{ $feasibility['shortage_minutes'] }}
                        menit sebelum deadline.
                    </p>

                </div>

            </div>

        </div>

    @endif


    {{-- Recommended Slots --}}
    <div class="mc-card">

        <div class="px-4 sm:px-6 py-5 border-b mc-divider">

            <h2 class="text-xl font-semibold">
                Rekomendasi Waktu
            </h2>

            <p class="mt-1 text-sm mc-muted">
                Pilih salah satu waktu berikut untuk langsung menjadwalkan pengerjaan task.
            </p>

        </div>


        @if ($recommendedSlots->count() > 0)

            <div class="divide-y mc-divider">

                @foreach ($recommendedSlots as $slot)

                    <div class="mc-row p-4 sm:p-6">

                        <div class="flex flex-col lg:flex-row
                                    lg:items-center lg:justify-between
                                    gap-5">

                            <div>

                                <p class="text-sm font-semibold">

                                    {{ $slot['start']->translatedFormat('l, d F Y') }}

                                </p>

                                <p class="mt-1 text-xl font-semibold"
                                   style="font-family: 'Fraunces', Georgia, serif;">

                                    {{ $slot['start']->format('H:i') }}
                                    —
                                    {{ $slot['end']->format('H:i') }}

                                </p>

                                <p class="mt-1 text-xs mc-faint">

                                    Durasi {{ $slot['minutes'] }} menit

                                </p>

                            </div>


                            <form
                                action="{{ route('tasks.schedule', $task) }}"
                                method="POST">

                                @csrf

                                <input
                                    type="hidden"
                                    name="start_time"
                                    value="{{ $slot['start']->format('Y-m-d H:i:s') }}">

                                <input
                                    type="hidden"
                                    name="end_time"
                                    value="{{ $slot['end']->format('Y-m-d H:i:s') }}">


                                <button
                                    type="submit"
                                    class="mc-btn mc-btn-primary w-full lg:w-auto">

                                    Gunakan Slot

                                </button>

                            </form>

                        </div>

                    </div>

                @endforeach

            </div>

        @else

            <div class="px-6 py-12 text-center">

                <div class="text-4xl">
                    🕐
                </div>

                <h3 class="mt-4 text-lg font-semibold">
                    Belum ada slot yang cukup
                </h3>

                <p class="mt-2 text-sm mc-muted">
                    Sistem tidak menemukan waktu kosong yang cukup
                    untuk menyelesaikan task sekaligus sebelum deadline.
                </p>

                <p class="mt-2 text-xs mc-faint">
                    Coba periksa jadwalmu atau pertimbangkan mengurangi
                    durasi pengerjaan task.
                </p>

            </div>

        @endif

    </div>


    {{-- All Available Slots --}}
    <div class="mc-card">

        <div class="px-4 sm:px-6 py-5 border-b mc-divider">

            <h2 class="text-xl font-semibold">
                Semua Waktu Kosong
            </h2>

            <p class="mt-1 text-sm mc-muted">
                Seluruh waktu kosong yang ditemukan sistem.
            </p>

        </div>


        @if ($slots->count() > 0)

            <div class="divide-y mc-divider">

                @foreach ($slots as $slot)

                    <div class="px-4 sm:px-6 py-4">

                        <div class="flex items-center justify-between gap-4">

                            <div>

                                <p class="text-sm font-medium">

                                    {{ $slot['start']->translatedFormat('d F Y') }}

                                </p>

                                <p class="mt-1 text-xs mc-muted">

                                    {{ $slot['start']->format('H:i') }}
                                    —
                                    {{ $slot['end']->format('H:i') }}

                                </p>

                            </div>

                            <span
                                class="text-xs font-medium mc-muted shrink-0">

                                {{ $slot['minutes'] }} menit

                            </span>

                        </div>

                    </div>

                @endforeach

            </div>

        @else

            <div class="px-6 py-10 text-center">

                <p class="text-sm mc-muted">
                    Tidak ada waktu kosong sebelum deadline.
                </p>

            </div>

        @endif

    </div>

</div>

@endsection