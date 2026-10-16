@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- Gaya khusus halaman ini (memakai warna dari layout) --}}
<style>
    .mc-card {
        background: #fff;
        border: 1px solid var(--mc-line);
        border-radius: 1.25rem;
    }

    .mc-muted {
        color: var(--mc-muted);
    }

    .mc-faint {
        color: #8CA097;
    }

    .mc-link-soft {
        color: var(--mc-primary);
        font-weight: 600;
    }

    .mc-link-soft:hover {
        text-decoration: underline;
    }

    .mc-row {
        transition: background .2s;
    }

    .mc-row:hover {
        background: #F5F9F6;
    }

    /* Timeline */
    .mc-dot-task {
        background: var(--mc-sun);
        box-shadow: 0 0 0 4px #FDF3DE;
    }

    .mc-dot-sch {
        background: var(--mc-primary);
        box-shadow: 0 0 0 4px var(--mc-soft);
    }

    .mc-badge-task {
        background: #FDF3DE;
        color: #8A5A0B;
    }

    .mc-badge-sch {
        background: var(--mc-soft);
        color: var(--mc-primary);
    }

    /* Form */
    .mc-input {
        width: 100%;
        border: 1px solid #C9D6CF;
        border-radius: .875rem;
        padding: .75rem .9rem;
        background: #fff;
        color: var(--mc-ink);
        font-size: 1rem;
        min-height: 2.75rem;
    }

    .mc-input:focus {
        outline: none;
        border-color: var(--mc-primary);
        box-shadow: 0 0 0 3px var(--mc-soft);
    }

    .mc-label {
        display: block;
        font-size: .875rem;
        font-weight: 600;
        margin-bottom: .4rem;
    }

    .mc-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 2.75rem;
        padding: .6rem 1.1rem;
        border-radius: .875rem;
        font-size: .875rem;
        font-weight: 600;
        transition: background .2s, color .2s;
    }

    .mc-btn-primary {
        background: var(--mc-ink);
        color: #fff;
    }

    .mc-btn-primary:hover {
        background: var(--mc-primary);
    }

    .mc-btn-ghost {
        border: 1px solid #C9D6CF;
        color: var(--mc-muted);
        background: #fff;
    }

    .mc-btn-ghost:hover {
        background: #F5F9F6;
        color: var(--mc-ink);
    }

    .mc-btn:focus-visible,
    .mc-row:focus-visible {
        outline: 3px solid var(--mc-sun);
        outline-offset: 2px;
    }
</style>

<div class="space-y-6">

    {{-- GREETING --}}
    <div>
        <h1 class="text-2xl sm:text-3xl font-semibold" style="color: var(--mc-ink);">
            Halo, {{ auth()->user()->name }} 👋
        </h1>

        <p class="mc-muted mt-1">
            {{ now()->translatedFormat('l, d F Y') }}
        </p>
    </div>


    {{-- SUMMARY --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">

        {{-- Workload (kartu paling menonjol) --}}
        <div class="col-span-2 md:col-span-1 p-5 rounded-[1.25rem]"
             style="background: var(--mc-ink); color: #fff;">

            <p class="text-sm" style="color: var(--mc-side-text);">
                Workload Hari Ini
            </p>

            <h2 class="text-2xl sm:text-3xl font-semibold mt-2 break-words">
                {{ $workloadStatus }}
            </h2>

        </div>


        {{-- Task --}}
        <div class="mc-card p-5">

            <p class="text-sm mc-muted">
                Waktu Task
            </p>

            <h2 class="text-2xl sm:text-3xl font-semibold mt-2">
                {{ round($todayWorkMinutes / 60, 1) }} jam
            </h2>

        </div>


        {{-- Schedule --}}
        <div class="mc-card p-5">

            <p class="text-sm mc-muted">
                Jadwal
            </p>

            <h2 class="text-2xl sm:text-3xl font-semibold mt-2">
                {{ $todaySchedules->count() }}
            </h2>

            <p class="text-sm mc-muted">
                aktivitas
            </p>

        </div>


        {{-- Check-in --}}
        <div class="mc-card p-5 col-span-2 md:col-span-1">

            <p class="text-sm mc-muted">
                Check-in
            </p>

            <h2 class="text-2xl sm:text-3xl font-semibold mt-2">

                @if($todayCheckin)

                    {{ $todayCheckin->energy_level }}/5

                @else

                    -

                @endif

            </h2>

        </div>

    </div>


    {{-- MAIN GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">


        {{-- AGENDA HARI INI --}}
        <div class="lg:col-span-2 mc-card p-4 sm:p-6">

            {{-- Header --}}
            <div class="flex flex-wrap items-start justify-between gap-x-4 gap-y-2 mb-6">

                <div>

                    <h2 class="text-xl font-semibold">
                        Agenda Hari Ini
                    </h2>

                    <p class="text-sm mc-muted mt-1">
                        Jadwal dan task yang perlu kamu perhatikan hari ini.
                    </p>

                </div>


                <a
                    href="{{ route('calendar') }}"
                    class="text-sm mc-link-soft">

                    Buka Calendar →

                </a>

            </div>


            {{-- Timeline --}}
            @forelse($todayAgenda as $agenda)

                <a
                    href="{{ route('calendar') }}"
                    class="group block">

                    <div
                        class="mc-row flex gap-3 sm:gap-4
                               border-b border-slate-100
                               py-4
                               last:border-b-0
                               -mx-2 px-2 sm:-mx-3 sm:px-3
                               rounded-xl">


                        {{-- TIME --}}
                        <div class="w-12 sm:w-20 shrink-0 text-right">

                            <p class="text-sm font-semibold">

                                {{ $agenda['start']->format('H:i') }}

                            </p>

                            <p class="text-xs mc-faint mt-1">

                                {{ $agenda['end']->format('H:i') }}

                            </p>

                        </div>


                        {{-- TIMELINE LINE --}}
                        <div class="relative flex flex-col items-center pt-1">

                            @if($agenda['type'] === 'task')

                                <div class="mc-dot-task w-3 h-3 rounded-full">

                                </div>

                            @else

                                <div class="mc-dot-sch w-3 h-3 rounded-full">

                                </div>

                            @endif

                            <div
                                class="w-px flex-1
                                       mt-2"
                                style="background: var(--mc-line);">
                            </div>

                        </div>


                        {{-- CONTENT --}}
                        <div class="flex-1 min-w-0">

                            <div class="flex items-start justify-between gap-3">

                                <div class="min-w-0">

                                    <h3
                                        class="font-semibold break-words
                                               group-hover:text-[var(--mc-primary)]"
                                        style="font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;">

                                        @if($agenda['type'] === 'task')

                                            📝 {{ $agenda['title'] }}

                                        @else

                                            {{ $agenda['title'] }}

                                        @endif

                                    </h3>


                                    <p class="text-xs mc-muted mt-1">

                                        @if($agenda['type'] === 'task')

                                            Task · Deadline

                                        @else

                                            {{ match($agenda['category']) {
                                                'academic' => 'Akademik',
                                                'organization' => 'Organisasi',
                                                'personal' => 'Pribadi',
                                                'rest' => 'Istirahat',
                                                'other' => 'Lainnya',
                                                default => 'Lainnya'
                                            } }}

                                        @endif

                                    </p>

                                </div>


                                {{-- Badge --}}
                                @if($agenda['type'] === 'task')

                                    <span
                                        class="mc-badge-task shrink-0
                                               text-xs font-medium
                                               px-2.5 py-1
                                               rounded-full">

                                        Task

                                    </span>

                                @else

                                    <span
                                        class="mc-badge-sch shrink-0
                                               text-xs font-medium
                                               px-2.5 py-1
                                               rounded-full">

                                        Jadwal

                                    </span>

                                @endif

                            </div>


                            {{-- Description --}}
                            @if($agenda['description'])

                                <p
                                    class="text-sm mc-muted
                                           mt-2 line-clamp-1">

                                    {{ $agenda['description'] }}

                                </p>

                            @endif


                            {{-- Priority --}}
                            @if($agenda['type'] === 'task')

                                <p class="text-xs mc-faint mt-2">

                                    Estimasi
                                    {{ $agenda['start']->diffInMinutes($agenda['end']) }}
                                    menit

                                    @if($agenda['priority'] === 'high')

                                        · Prioritas tinggi

                                    @elseif($agenda['priority'] === 'medium')

                                        · Prioritas sedang

                                    @endif

                                </p>

                            @endif

                        </div>

                    </div>

                </a>

            @empty

                {{-- EMPTY STATE --}}
                <div
                    class="rounded-2xl
                           border border-dashed
                           py-12 px-4
                           text-center"
                    style="border-color: #C9D6CF;">

                    <div
                        class="mx-auto mb-3
                               w-12 h-12
                               rounded-full
                               flex items-center justify-center
                               text-xl"
                        style="background: var(--mc-soft);">

                        📅

                    </div>

                    <h3 class="font-semibold text-lg">
                        Hari ini masih kosong
                    </h3>

                    <p class="text-sm mc-muted mt-1">
                        Belum ada jadwal atau task yang perlu dikerjakan hari ini.
                    </p>

                    <a
                        href="{{ route('calendar') }}"
                        class="inline-block mt-4
                               text-sm mc-link-soft">

                        Atur jadwal →

                    </a>

                </div>

            @endforelse

        </div>


        {{-- CHECK-IN --}}
        <div class="mc-card p-4 sm:p-6 self-start">

            <h2 class="text-xl font-semibold">
                How are you today?
            </h2>

            <p class="text-sm mc-muted mt-1 mb-5">
                Check-in singkat untuk membantu sistem
                menyesuaikan rekomendasi.
            </p>


            {{-- STATUS CHECK-IN --}}
            @if($todayCheckin)

                <div
                    class="mb-5 rounded-xl
                           px-4 py-3"
                    style="background: var(--mc-soft);">

                    <p class="text-sm font-semibold" style="color: var(--mc-primary);">
                        ✓ Kamu sudah mengisi check-in hari ini.
                    </p>

                    <p class="text-xs mt-1" style="color: var(--mc-primary);">
                        Kamu masih bisa mengubah jawabanmu.
                    </p>

                </div>

            @endif


            <form
                id="checkin-form"
                action="{{ route('checkin.store') }}"
                method="POST">

                @csrf


                {{-- ENERGY --}}
                <label class="mc-label">
                    Energy Level
                </label>

                <select
                    name="energy_level"
                    class="mc-input mb-4">

                    <option
                        value="1"
                        {{ old('energy_level', $todayCheckin->energy_level ?? '') == 1 ? 'selected' : '' }}>
                        1 - Sangat rendah
                    </option>

                    <option
                        value="2"
                        {{ old('energy_level', $todayCheckin->energy_level ?? '') == 2 ? 'selected' : '' }}>
                        2 - Rendah
                    </option>

                    <option
                        value="3"
                        {{ old('energy_level', $todayCheckin->energy_level ?? '') == 3 ? 'selected' : '' }}>
                        3 - Normal
                    </option>

                    <option
                        value="4"
                        {{ old('energy_level', $todayCheckin->energy_level ?? '') == 4 ? 'selected' : '' }}>
                        4 - Baik
                    </option>

                    <option
                        value="5"
                        {{ old('energy_level', $todayCheckin->energy_level ?? '') == 5 ? 'selected' : '' }}>
                        5 - Sangat baik
                    </option>

                </select>


                {{-- WORKLOAD --}}
                <label class="mc-label">
                    Beban hari ini
                </label>

                <select
                    name="workload_level"
                    class="mc-input mb-4">

                    <option
                        value="light"
                        {{ old('workload_level', $todayCheckin->workload_level ?? '') === 'light' ? 'selected' : '' }}>
                        Ringan
                    </option>

                    <option
                        value="normal"
                        {{ old('workload_level', $todayCheckin->workload_level ?? '') === 'normal' ? 'selected' : '' }}>
                        Normal
                    </option>

                    <option
                        value="heavy"
                        {{ old('workload_level', $todayCheckin->workload_level ?? '') === 'heavy' ? 'selected' : '' }}>
                        Berat
                    </option>

                </select>


                {{-- NOTE --}}
                <label class="mc-label">
                    Catatan
                </label>

                <textarea
                    name="note"
                    rows="3"
                    class="mc-input mb-4"
                    placeholder="Opsional...">{{ old('note', $todayCheckin->note ?? '') }}</textarea>


                <button
                    type="submit"
                    class="mc-btn mc-btn-primary w-full">

                    @if($todayCheckin)

                        Ubah Check-in

                    @else

                        Simpan Check-in

                    @endif

                </button>

            </form>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- REKOMENDASI --}}
    {{-- ========================================================= --}}

    @if($recommendations->count() > 0)

        <div class="mc-card p-4 sm:p-6">

            <div class="mb-5">

                <h2 class="text-xl font-semibold">
                    Rekomendasi Hari Ini
                </h2>

                <p class="text-sm mc-muted mt-1">
                    Saran berdasarkan jadwal, task, dan check-in kamu.
                </p>

            </div>


            <div class="space-y-3">

                @foreach($recommendations as $recommendation)

                    <div
                        class="mc-row rounded-2xl
                               border
                               p-4"
                        style="border-color: var(--mc-line);">

                        <div class="flex items-start gap-3 sm:gap-4">

                            {{-- ICON --}}
                            <div
                                class="w-10 h-10
                                       shrink-0
                                       rounded-full
                                       flex items-center justify-center
                                       text-lg"
                                style="background: var(--mc-soft);">

                                @if($recommendation->type === 'recovery')

                                    🌿

                                @elseif($recommendation->type === 'workload')

                                    ⚠️

                                @elseif($recommendation->type === 'deadline')

                                    📝

                                @else

                                    💡

                                @endif

                            </div>


                            {{-- CONTENT --}}
                            <div class="flex-1 min-w-0">

                                <h3 class="font-semibold break-words"
                                    style="font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;">
                                    {{ $recommendation->title }}
                                </h3>

                                <p class="text-sm mt-1 leading-6" style="color: #3F5A54;">
                                    {{ $recommendation->message }}
                                </p>


                                @if($recommendation->type === 'recovery')

                                    <div
                                        class="mt-3
                                               rounded-xl
                                               px-3 py-2"
                                        style="background: var(--mc-soft);">

                                        <p class="text-xs" style="color: var(--mc-primary);">
                                            🌱 Prioritaskan waktu istirahat sebelum melanjutkan aktivitas berikutnya.
                                        </p>

                                    </div>

                                @endif


                                {{-- ACTION --}}
                                <div class="flex flex-wrap gap-2 mt-4">

                                    <form
                                        method="POST"
                                        action="{{ route('recommendations.accept', $recommendation) }}">

                                        @csrf

                                        <button
                                            type="submit"
                                            class="mc-btn mc-btn-primary">

                                            Mengerti

                                        </button>

                                    </form>


                                    <form
                                        method="POST"
                                        action="{{ route('recommendations.reject', $recommendation) }}">

                                        @csrf

                                        <button
                                            type="submit"
                                            class="mc-btn mc-btn-ghost">

                                            Abaikan

                                        </button>

                                    </form>

                                </div>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        </div>

    @endif


    {{-- UPCOMING TASK --}}
    <div class="mc-card p-4 sm:p-6">

        <div class="flex flex-wrap items-start justify-between gap-x-4 gap-y-2 mb-5">

            <div class="max-w-xl">

                <h2 class="text-xl font-semibold">
                    Deadline Terdekat
                </h2>

                <p class="text-sm mc-muted mt-1">
                    Jangan hanya melihat deadline.
                    Perhatikan juga estimasi waktu pengerjaan.
                </p>

            </div>

            <a
                href="{{ route('tasks.index') }}"
                class="text-sm mc-link-soft">

                Kelola Task

            </a>

        </div>


        <div class="space-y-3">

            @forelse($upcomingTasks as $task)

                <a
                    href="{{ route('tasks.show', $task) }}"
                    class="mc-row flex items-center
                           justify-between gap-4
                           border
                           rounded-2xl p-4"
                    style="border-color: var(--mc-line);">

                    <div class="min-w-0">

                        <h3 class="font-semibold break-words"
                            style="font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;">
                            {{ $task->title }}
                        </h3>

                        <p class="text-sm mc-muted">

                            Deadline:
                            {{ $task->deadline->format('d M Y H:i') }}

                        </p>

                    </div>


                    <div class="text-right shrink-0">

                        <p class="font-semibold">
                            {{ round($task->estimated_duration / 60, 1) }}
                            jam
                        </p>

                        <p class="text-xs mc-muted">
                            estimasi pengerjaan
                        </p>

                    </div>

                </a>

            @empty

                <p class="mc-muted">
                    Tidak ada deadline terdekat.
                </p>

            @endforelse

        </div>

    </div>

</div>


{{-- CHECK-IN CONFIRMATION MODAL --}}
@if($todayCheckin)

<div
    id="checkin-modal"
    class="hidden fixed inset-0 z-[200]
           items-center justify-center
           px-4"
    style="background: rgba(22,48,43,.55);">

    <div
        class="w-full max-w-md
               rounded-3xl bg-white
               shadow-xl p-6">

        <div
            class="w-12 h-12
                   rounded-full
                   flex items-center justify-center
                   text-xl mb-4"
            style="background: #FDF3DE; color: #8A5A0B;">

            ?

        </div>

        <h2 class="text-xl font-semibold">
            Check-in hari ini sudah diisi
        </h2>

        <p class="text-sm mc-muted mt-2 leading-relaxed">
            Kamu sudah mengisi kondisi kamu hari ini.
            Apakah kamu ingin mengubah jawaban tersebut?
        </p>

        <div class="flex gap-3 mt-6">

            <button
                type="button"
                id="cancel-checkin"
                class="mc-btn mc-btn-ghost flex-1">

                Batal

            </button>

            <button
                type="button"
                id="confirm-checkin"
                class="mc-btn mc-btn-primary flex-1">

                Ya, Ubah

            </button>

        </div>

    </div>

</div>


<script>

    document.addEventListener('DOMContentLoaded', function () {

        const form =
            document.getElementById('checkin-form');

        const modal =
            document.getElementById('checkin-modal');

        const cancelButton =
            document.getElementById('cancel-checkin');

        const confirmButton =
            document.getElementById('confirm-checkin');


        if (!form || !modal) {
            return;
        }


        form.addEventListener('submit', function (event) {

            event.preventDefault();

            modal.classList.remove('hidden');
            modal.classList.add('flex');

        });


        cancelButton.addEventListener('click', function () {

            modal.classList.add('hidden');
            modal.classList.remove('flex');

        });


        confirmButton.addEventListener('click', function () {

            form.submit();

        });

    });

</script>

@endif

@endsection