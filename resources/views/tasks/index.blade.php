@extends('layouts.app')

@section('content')

{{-- Gaya halaman ini (memakai warna dari layout) --}}
<style>
    .mc-card { background: #fff; border: 1px solid var(--mc-line); border-radius: 1.25rem; }
    .mc-muted { color: var(--mc-muted); }
    .mc-faint { color: #8CA097; }
    .mc-sans { font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif; }
    .mc-divider { border-color: var(--mc-line); }

    .mc-btn {
        display: inline-flex; align-items: center; justify-content: center;
        min-height: 2.75rem; padding: .6rem 1.25rem; border-radius: .875rem;
        font-size: .875rem; font-weight: 600; transition: background .2s, color .2s;
    }
    .mc-btn-sm { min-height: 2.5rem; padding: .45rem .9rem; font-size: .8125rem; border-radius: .75rem; }
    .mc-btn-primary { background: var(--mc-ink); color: #fff; }
    .mc-btn-primary:hover { background: var(--mc-primary); }
    .mc-btn-done { background: var(--mc-primary); color: #fff; }
    .mc-btn-done:hover { background: var(--mc-ink); }
    .mc-btn-ghost { border: 1px solid #C9D6CF; color: var(--mc-muted); background: #fff; }
    .mc-btn-ghost:hover { background: #F5F9F6; color: var(--mc-ink); }
    .mc-btn-danger { border: 1px solid #EBC9C5; color: #B4443C; background: #fff; }
    .mc-btn-danger:hover { background: #FBF1F0; }
    .mc-btn:focus-visible { outline: 3px solid var(--mc-sun); outline-offset: 2px; }

    /* Tab */
    .mc-tab-on { background: var(--mc-ink); color: #fff; }
    .mc-tab-off { color: var(--mc-muted); }
    .mc-tab-off:hover { background: var(--mc-soft); color: var(--mc-ink); }
    .mc-count-on { color: var(--mc-side-text); }
    .mc-count-off { color: #8CA097; }

    /* Badge */
    .mc-badge { border-radius: 9999px; padding: .25rem .65rem; font-size: .75rem; font-weight: 500; }
    .mc-b-high { background: #FBEAE8; color: #B4443C; }
    .mc-b-med { background: #FDF3DE; color: #8A5A0B; }
    .mc-b-low { background: var(--mc-soft); color: var(--mc-primary); }
    .mc-b-progress { background: #E6EEF4; color: #3F5F7A; }
    .mc-b-todo { background: #EEF3EF; color: var(--mc-muted); }

    .mc-row { transition: background .2s; }
    .mc-row:hover { background: #F5F9F6; }
</style>

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

        <div>
            <h1 class="text-2xl sm:text-3xl font-semibold">
                Tasks & Deadline
            </h1>

            <p class="mt-1 text-sm mc-muted">
                Kelola tugas dan deadline agar pekerjaanmu tidak menumpuk.
            </p>
        </div>

        <a
            href="{{ route('tasks.create') }}"
            class="mc-btn mc-btn-primary gap-2">

            <span class="text-lg leading-none">+</span>
            Tambah Task

        </a>

    </div>


    {{-- Success Message --}}
    @if (session('success'))

        <div class="rounded-2xl px-5 py-4" style="background: var(--mc-soft);">

            <p class="text-sm font-medium" style="color: var(--mc-primary);">
                {{ session('success') }}
            </p>

        </div>

    @endif


    {{-- Task Tabs --}}
    <div
        x-data="{ activeTab: 'ongoing' }"
        class="space-y-5"
    >

        {{-- Tab Navigation --}}
        <div class="mc-card p-2">

            <div class="grid grid-cols-2 gap-2">

                {{-- Ongoing --}}
                <button
                    type="button"
                    @click="activeTab = 'ongoing'"
                    :class="activeTab === 'ongoing'
                        ? 'mc-tab-on'
                        : 'mc-tab-off'"
                    class="rounded-xl px-3 sm:px-5 py-3 text-sm font-semibold transition">

                    Ongoing

                    <span
                        class="ml-1"
                        :class="activeTab === 'ongoing'
                            ? 'mc-count-on'
                            : 'mc-count-off'">

                        ({{ $tasks->where('status', '!=', 'completed')->count() }})

                    </span>

                </button>


                {{-- Complete --}}
                <button
                    type="button"
                    @click="activeTab = 'complete'"
                    :class="activeTab === 'complete'
                        ? 'mc-tab-on'
                        : 'mc-tab-off'"
                    class="rounded-xl px-3 sm:px-5 py-3 text-sm font-semibold transition">

                    Complete

                    <span
                        class="ml-1"
                        :class="activeTab === 'complete'
                            ? 'mc-count-on'
                            : 'mc-count-off'">

                        ({{ $tasks->where('status', 'completed')->count() }})

                    </span>

                </button>

            </div>

        </div>


        {{-- ================================================= --}}
        {{-- ONGOING --}}
        {{-- ================================================= --}}

        <div
            x-show="activeTab === 'ongoing'"
            x-transition
            class="mc-card"
        >

            <div class="px-4 sm:px-6 py-5 border-b mc-divider">

                <h2 class="text-xl font-semibold">
                    Ongoing Tasks
                </h2>

                <p class="mt-1 text-sm mc-muted">
                    Task yang masih perlu kamu kerjakan.
                </p>

            </div>


            @if ($tasks->where('status', '!=', 'completed')->count() > 0)

                <div class="divide-y mc-divider">

                    @foreach ($tasks->where('status', '!=', 'completed') as $task)

                        <div class="mc-row p-4 sm:p-6">

                            <div class="flex flex-col lg:flex-row
                                        lg:items-center lg:justify-between
                                        gap-5">

                                {{-- Task Information --}}
                                <div class="min-w-0">

                                    <div class="flex flex-wrap items-center gap-2">

                                        <h3 class="text-base font-semibold mc-sans break-words">
                                            {{ $task->title }}
                                        </h3>


                                        {{-- Priority --}}
                                        @if ($task->priority === 'high')

                                            <span class="mc-badge mc-b-high">
                                                Tinggi
                                            </span>

                                        @elseif ($task->priority === 'medium')

                                            <span class="mc-badge mc-b-med">
                                                Sedang
                                            </span>

                                        @else

                                            <span class="mc-badge mc-b-low">
                                                Rendah
                                            </span>

                                        @endif


                                        {{-- Status --}}
                                        @if ($task->status === 'in_progress')

                                            <span class="mc-badge mc-b-progress">
                                                Sedang Dikerjakan
                                            </span>

                                        @else

                                            <span class="mc-badge mc-b-todo">
                                                Belum Dikerjakan
                                            </span>

                                        @endif

                                    </div>


                                    {{-- Description --}}
                                    @if ($task->description)

                                        <p class="mt-2 text-sm mc-muted line-clamp-2">
                                            {{ $task->description }}
                                        </p>

                                    @endif


                                    {{-- Meta --}}
                                    <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2
                                                text-xs mc-muted">

                                        <span>
                                            📅
                                            {{ \Carbon\Carbon::parse($task->deadline)->translatedFormat('d F Y, H:i') }}
                                        </span>

                                        <span>
                                            ⏱
                                            {{ $task->estimated_duration }} menit
                                        </span>

                                        <span>

                                            @if ($task->category === 'academic')
                                                🎓 Akademik
                                            @elseif ($task->category === 'organization')
                                                👥 Organisasi
                                            @elseif ($task->category === 'personal')
                                                👤 Pribadi
                                            @else
                                                📌 Lainnya
                                            @endif

                                        </span>

                                    </div>

                                </div>


                                {{-- Actions --}}
                                <div class="flex flex-wrap items-center gap-2 shrink-0">

                                    {{-- Smart Scheduling --}}
                                    <a
                                        href="{{ route('tasks.smart-schedule', $task) }}"
                                        @if ($task->status === 'in_progress')
                                            onclick="return confirm('Jadwal task ini sudah diatur. Apakah kamu mau mengganti jadwal?')"
                                        @endif
                                        class="mc-btn mc-btn-sm mc-btn-primary">

                                        @if ($task->status === 'in_progress')
                                            Ganti Jadwal
                                        @else
                                            Atur Jadwal
                                        @endif

                                    </a>


                                    {{-- Complete --}}
                                    <form
                                        action="{{ route('tasks.complete', $task) }}"
                                        method="POST">

                                        @csrf

                                        <button
                                            type="submit"
                                            class="mc-btn mc-btn-sm mc-btn-done">

                                            ✓ Selesai

                                        </button>

                                    </form>


                                    {{-- Detail --}}
                                    <a
                                        href="{{ route('tasks.show', $task) }}"
                                        class="mc-btn mc-btn-sm mc-btn-ghost">

                                        Detail

                                    </a>


                                    {{-- Edit --}}
                                    <a
                                        href="{{ route('tasks.edit', $task) }}"
                                        class="mc-btn mc-btn-sm mc-btn-ghost">

                                        Edit

                                    </a>


                                    {{-- Delete --}}
                                    <form
                                        action="{{ route('tasks.destroy', $task) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus task ini?')">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="mc-btn mc-btn-sm mc-btn-danger">

                                            Hapus

                                        </button>

                                    </form>

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                <div class="px-6 py-16 text-center">

                    <div class="text-4xl">
                        🎉
                    </div>

                    <h3 class="mt-4 text-lg font-semibold">
                        Tidak ada task ongoing
                    </h3>

                    <p class="mt-2 text-sm mc-muted">
                        Semua task sudah selesai.
                    </p>

                </div>

            @endif

        </div>


        {{-- ================================================= --}}
        {{-- COMPLETE --}}
        {{-- ================================================= --}}

        <div
            x-show="activeTab === 'complete'"
            x-transition
            class="mc-card"
        >

            <div class="px-4 sm:px-6 py-5 border-b mc-divider">

                <h2 class="text-xl font-semibold">
                    Completed Tasks
                </h2>

                <p class="mt-1 text-sm mc-muted">
                    Task yang sudah kamu selesaikan.
                </p>

            </div>


            @if ($tasks->where('status', 'completed')->count() > 0)

                <div class="divide-y mc-divider">

                    @foreach ($tasks->where('status', 'completed') as $task)

                        <div class="p-4 sm:p-6" style="background: #F8FAF9;">

                            <div class="flex flex-col lg:flex-row
                                        lg:items-center lg:justify-between
                                        gap-5">

                                {{-- Task Information --}}
                                <div class="min-w-0">

                                    <div class="flex flex-wrap items-center gap-2">

                                        <h3 class="text-base font-semibold mc-sans
                                                   mc-muted line-through break-words">

                                            {{ $task->title }}

                                        </h3>

                                        <span class="mc-badge mc-b-low">

                                            ✓ Selesai

                                        </span>

                                    </div>


                                    @if ($task->description)

                                        <p class="mt-2 text-sm mc-faint line-clamp-2">
                                            {{ $task->description }}
                                        </p>

                                    @endif


                                    <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2
                                                text-xs mc-faint">

                                        <span>
                                            📅
                                            {{ \Carbon\Carbon::parse($task->deadline)->translatedFormat('d F Y, H:i') }}
                                        </span>

                                        <span>
                                            ⏱
                                            {{ $task->estimated_duration }} menit
                                        </span>

                                        <span>

                                            @if ($task->category === 'academic')
                                                🎓 Akademik
                                            @elseif ($task->category === 'organization')
                                                👥 Organisasi
                                            @elseif ($task->category === 'personal')
                                                👤 Pribadi
                                            @else
                                                📌 Lainnya
                                            @endif

                                        </span>

                                    </div>

                                </div>


                                {{-- Completed Actions --}}
                                <div class="flex flex-wrap items-center gap-2 shrink-0">

                                    {{-- Reopen --}}
                                    <form
                                        action="{{ route('tasks.reopen', $task) }}"
                                        method="POST">

                                        @csrf

                                        <button
                                            type="submit"
                                            class="mc-btn mc-btn-sm mc-btn-ghost">

                                            Buka Kembali

                                        </button>

                                    </form>


                                    {{-- Detail --}}
                                    <a
                                        href="{{ route('tasks.show', $task) }}"
                                        class="mc-btn mc-btn-sm mc-btn-ghost">

                                        Detail

                                    </a>


                                    {{-- Delete --}}
                                    <form
                                        action="{{ route('tasks.destroy', $task) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus task ini?')">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="mc-btn mc-btn-sm mc-btn-danger">

                                            Hapus

                                        </button>

                                    </form>

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

            @else

                <div class="px-6 py-16 text-center">

                    <div class="text-4xl">
                        📋
                    </div>

                    <h3 class="mt-4 text-lg font-semibold">
                        Belum ada task selesai
                    </h3>

                    <p class="mt-2 text-sm mc-muted">
                        Task yang sudah selesai akan muncul di sini.
                    </p>

                </div>

            @endif

        </div>

    </div>

</div>

@endsection