@extends('layouts.app')

@section('content')

{{-- Gaya halaman ini (memakai warna dari layout) --}}
<style>
    .mc-card { background: #fff; border: 1px solid var(--mc-line); border-radius: 1.25rem; }
    .mc-muted { color: var(--mc-muted); }
    .mc-field-label { font-size: .8125rem; font-weight: 600; color: var(--mc-muted); }
    .mc-tile { background: #F5F9F6; border: 1px solid var(--mc-line); border-radius: 1rem; }

    .mc-back {
        display: inline-flex; align-items: center; gap: .5rem;
        min-height: 2.5rem; padding: .4rem 1rem .4rem .8rem;
        border-radius: 9999px; background: #fff; border: 1px solid var(--mc-line);
        color: var(--mc-ink); font-size: .875rem; font-weight: 600;
        transition: background .2s, border-color .2s, color .2s;
    }
    .mc-back:hover { background: var(--mc-soft); border-color: var(--mc-primary); color: var(--mc-primary); }
    .mc-back:focus-visible { outline: 3px solid var(--mc-sun); outline-offset: 2px; }

    .mc-btn {
        display: inline-flex; align-items: center; justify-content: center;
        min-height: 2.75rem; padding: .6rem 1.25rem; border-radius: .875rem;
        font-size: .875rem; font-weight: 600; transition: background .2s, color .2s;
    }
    .mc-btn-primary { background: var(--mc-ink); color: #fff; }
    .mc-btn-primary:hover { background: var(--mc-primary); }
    .mc-btn-danger { border: 1px solid #EBC9C5; color: #B4443C; background: #fff; }
    .mc-btn-danger:hover { background: #FBF1F0; }
    .mc-btn:focus-visible, .mc-back:focus-visible { outline: 3px solid var(--mc-sun); outline-offset: 2px; }

    .mc-badge { border-radius: 9999px; padding: .375rem .75rem; font-size: .75rem; font-weight: 500; }
    .mc-b-high { background: #FBEAE8; color: #B4443C; }
    .mc-b-med { background: #FDF3DE; color: #8A5A0B; }
    .mc-b-low { background: var(--mc-soft); color: var(--mc-primary); }
    .mc-b-progress { background: #E6EEF4; color: #3F5F7A; }
    .mc-b-todo { background: #EEF3EF; color: var(--mc-muted); }
</style>

<div class="max-w-3xl mx-auto space-y-6">

    {{-- Header --}}
    <div>
        <a
            href="{{ route('tasks.index') }}"
            class="mc-back">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 shrink-0" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>
            Kembali ke Tasks
        </a>

        <div class="mt-3 flex flex-col sm:flex-row sm:items-start
                    sm:justify-between gap-4">

            <div class="min-w-0">
                <h1 class="text-2xl sm:text-3xl font-semibold break-words">
                    {{ $task->title }}
                </h1>

                <p class="mt-1 text-sm mc-muted">
                    Detail task
                </p>
            </div>

            <a
                href="{{ route('tasks.edit', $task) }}"
                class="mc-btn mc-btn-primary shrink-0">

                Edit Task

            </a>

        </div>
    </div>


    {{-- Detail Card --}}
    <div class="mc-card">

        <div class="p-4 sm:p-6 space-y-6">

            {{-- Status & Priority --}}
            <div class="flex flex-wrap gap-2">

                @if ($task->status === 'completed')

                    <span class="mc-badge mc-b-low">
                        Selesai
                    </span>

                @elseif ($task->status === 'in_progress')

                    <span class="mc-badge mc-b-progress">
                        Sedang dikerjakan
                    </span>

                @else

                    <span class="mc-badge mc-b-todo">
                        Belum dikerjakan
                    </span>

                @endif


                @if ($task->priority === 'high')

                    <span class="mc-badge mc-b-high">
                        Prioritas Tinggi
                    </span>

                @elseif ($task->priority === 'medium')

                    <span class="mc-badge mc-b-med">
                        Prioritas Sedang
                    </span>

                @else

                    <span class="mc-badge mc-b-low">
                        Prioritas Rendah
                    </span>

                @endif

            </div>


            {{-- Description --}}
            <div>
                <p class="mc-field-label">
                    Deskripsi
                </p>

                <div class="mt-2 mc-tile px-4 py-4">

                    <p class="text-sm leading-relaxed break-words">
                        {{ $task->description ?: 'Tidak ada deskripsi.' }}
                    </p>

                </div>
            </div>


            {{-- Information --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Deadline --}}
                <div class="mc-tile px-4 py-4">

                    <p class="mc-field-label">
                        Deadline
                    </p>

                    <p class="mt-2 text-sm font-semibold">
                        {{ \Carbon\Carbon::parse($task->deadline)->translatedFormat('l, d F Y') }}
                    </p>

                    <p class="mt-1 text-xs mc-muted">
                        {{ \Carbon\Carbon::parse($task->deadline)->format('H:i') }} WIB
                    </p>

                </div>


                {{-- Estimated Duration --}}
                <div class="mc-tile px-4 py-4">

                    <p class="mc-field-label">
                        Estimasi Pengerjaan
                    </p>

                    <p class="mt-2 text-sm font-semibold">
                        {{ $task->estimated_duration }} menit
                    </p>

                    <p class="mt-1 text-xs mc-muted">
                        ≈ {{ round($task->estimated_duration / 60, 1) }} jam
                    </p>

                </div>


                {{-- Category --}}
                <div class="mc-tile px-4 py-4">

                    <p class="mc-field-label">
                        Kategori
                    </p>

                    <p class="mt-2 text-sm font-semibold">

                        @if ($task->category === 'academic')
                            Akademik
                        @elseif ($task->category === 'organization')
                            Organisasi
                        @elseif ($task->category === 'personal')
                            Pribadi
                        @else
                            Lainnya
                        @endif

                    </p>

                </div>


                {{-- Created --}}
                <div class="mc-tile px-4 py-4">

                    <p class="mc-field-label">
                        Dibuat
                    </p>

                    <p class="mt-2 text-sm font-semibold">
                        {{ $task->created_at->translatedFormat('d F Y') }}
                    </p>

                </div>

            </div>


            {{-- Actions --}}
            <div class="border-t pt-6" style="border-color: var(--mc-line);">

                <div class="flex flex-col sm:flex-row gap-3">

                    <a
                        href="{{ route('tasks.edit', $task) }}"
                        class="mc-btn mc-btn-primary flex-1">

                        Edit Task

                    </a>


                    <form
                        action="{{ route('tasks.destroy', $task) }}"
                        method="POST"
                        class="flex-1"
                        onsubmit="return confirm('Yakin ingin menghapus task ini?')">

                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="mc-btn mc-btn-danger w-full">

                            Hapus Task

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection