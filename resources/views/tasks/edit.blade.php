@extends('layouts.app')

@section('content')

{{-- Gaya halaman ini (memakai warna dari layout) --}}
<style>
    .mc-card { background: #fff; border: 1px solid var(--mc-line); border-radius: 1.25rem; }
    .mc-muted { color: var(--mc-muted); }
    .mc-faint { color: #8CA097; }
    .mc-error-text { color: #B4443C; }

    .mc-back {
        display: inline-flex; align-items: center; gap: .5rem;
        min-height: 2.5rem; padding: .4rem 1rem .4rem .8rem;
        border-radius: 9999px; background: #fff; border: 1px solid var(--mc-line);
        color: var(--mc-ink); font-size: .875rem; font-weight: 600;
        transition: background .2s, border-color .2s, color .2s;
    }
    .mc-back:hover { background: var(--mc-soft); border-color: var(--mc-primary); color: var(--mc-primary); }
    .mc-back:focus-visible { outline: 3px solid var(--mc-sun); outline-offset: 2px; }

    .mc-input {
        width: 100%; border: 1px solid #C9D6CF; border-radius: .875rem;
        padding: .75rem .9rem; background: #fff; color: var(--mc-ink);
        font-size: 1rem; min-height: 2.75rem;
    }
    .mc-input:focus { outline: none; border-color: var(--mc-primary); box-shadow: 0 0 0 3px var(--mc-soft); }
    .mc-label { display: block; font-size: .875rem; font-weight: 600; }

    .mc-btn {
        display: inline-flex; align-items: center; justify-content: center;
        min-height: 2.75rem; padding: .6rem 1.25rem; border-radius: .875rem;
        font-size: .875rem; font-weight: 600; transition: background .2s, color .2s;
    }
    .mc-btn-primary { background: var(--mc-ink); color: #fff; }
    .mc-btn-primary:hover { background: var(--mc-primary); }
    .mc-btn-ghost { border: 1px solid #C9D6CF; color: var(--mc-muted); background: #fff; }
    .mc-btn-ghost:hover { background: #F5F9F6; color: var(--mc-ink); }
    .mc-btn:focus-visible, .mc-back:focus-visible { outline: 3px solid var(--mc-sun); outline-offset: 2px; }
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

        <h1 class="mt-3 text-2xl sm:text-3xl font-semibold">
            Edit Task
        </h1>

        <p class="mt-1 text-sm mc-muted">
            Perbarui informasi task dan status pengerjaanmu.
        </p>
    </div>


    {{-- Form --}}
    <div class="mc-card">

        <form
            action="{{ route('tasks.update', $task) }}"
            method="POST"
            class="p-4 sm:p-6 space-y-6">

            @csrf
            @method('PUT')


            {{-- Title --}}
            <div>
                <label
                    for="title"
                    class="mc-label">
                    Judul Task
                </label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    value="{{ old('title', $task->title) }}"
                    class="mc-input mt-2"
                    required>

                @error('title')
                    <p class="mt-1 text-sm mc-error-text">
                        {{ $message }}
                    </p>
                @enderror
            </div>


            {{-- Description --}}
            <div>
                <label
                    for="description"
                    class="mc-label">
                    Deskripsi
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    class="mc-input mt-2">{{ old('description', $task->description) }}</textarea>

                @error('description')
                    <p class="mt-1 text-sm mc-error-text">
                        {{ $message }}
                    </p>
                @enderror
            </div>


            {{-- Deadline & Duration --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- Deadline --}}
                <div>
                    <label
                        for="deadline"
                        class="mc-label">
                        Deadline
                    </label>

                    <input
                        type="datetime-local"
                        id="deadline"
                        name="deadline"
                        value="{{ old('deadline', \Carbon\Carbon::parse($task->deadline)->format('Y-m-d\TH:i')) }}"
                        class="mc-input mt-2"
                        required>

                    @error('deadline')
                        <p class="mt-1 text-sm mc-error-text">
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                {{-- Estimated Duration --}}
                <div>
                    <label
                        for="estimated_duration"
                        class="mc-label">
                        Estimasi Durasi
                    </label>

                    <div class="relative mt-2">

                        <input
                            type="number"
                            id="estimated_duration"
                            name="estimated_duration"
                            value="{{ old('estimated_duration', $task->estimated_duration) }}"
                            min="1"
                            class="mc-input pr-20"
                            required>

                        <span
                            class="absolute right-4 top-1/2
                                   -translate-y-1/2
                                   text-sm mc-faint">
                            menit
                        </span>

                    </div>

                    @error('estimated_duration')
                        <p class="mt-1 text-sm mc-error-text">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>


            {{-- Priority & Category --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- Priority --}}
                <div>
                    <label
                        for="priority"
                        class="mc-label">
                        Prioritas
                    </label>

                    <select
                        id="priority"
                        name="priority"
                        class="mc-input mt-2"
                        required>

                        <option value="low"
                            {{ old('priority', $task->priority) === 'low' ? 'selected' : '' }}>
                            Rendah
                        </option>

                        <option value="medium"
                            {{ old('priority', $task->priority) === 'medium' ? 'selected' : '' }}>
                            Sedang
                        </option>

                        <option value="high"
                            {{ old('priority', $task->priority) === 'high' ? 'selected' : '' }}>
                            Tinggi
                        </option>

                    </select>

                    @error('priority')
                        <p class="mt-1 text-sm mc-error-text">
                            {{ $message }}
                        </p>
                    @enderror
                </div>


                {{-- Category --}}
                <div>
                    <label
                        for="category"
                        class="mc-label">
                        Kategori
                    </label>

                    <select
                        id="category"
                        name="category"
                        class="mc-input mt-2"
                        required>

                        <option value="academic"
                            {{ old('category', $task->category) === 'academic' ? 'selected' : '' }}>
                            Akademik
                        </option>

                        <option value="organization"
                            {{ old('category', $task->category) === 'organization' ? 'selected' : '' }}>
                            Organisasi
                        </option>

                        <option value="personal"
                            {{ old('category', $task->category) === 'personal' ? 'selected' : '' }}>
                            Pribadi
                        </option>

                        <option value="other"
                            {{ old('category', $task->category) === 'other' ? 'selected' : '' }}>
                            Lainnya
                        </option>

                    </select>

                    @error('category')
                        <p class="mt-1 text-sm mc-error-text">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>


            {{-- Status --}}
            <div>
                <label
                    for="status"
                    class="mc-label">
                    Status Pengerjaan
                </label>

                <select
                    id="status"
                    name="status"
                    class="mc-input mt-2"
                    required>

                    <option value="todo"
                        {{ old('status', $task->status) === 'todo' ? 'selected' : '' }}>
                        Belum dikerjakan
                    </option>

                    <option value="in_progress"
                        {{ old('status', $task->status) === 'in_progress' ? 'selected' : '' }}>
                        Sedang dikerjakan
                    </option>

                    <option value="completed"
                        {{ old('status', $task->status) === 'completed' ? 'selected' : '' }}>
                        Selesai
                    </option>

                </select>

                @error('status')
                    <p class="mt-1 text-sm mc-error-text">
                        {{ $message }}
                    </p>
                @enderror
            </div>


            {{-- Buttons --}}
            <div class="flex flex-col-reverse sm:flex-row
                        sm:justify-end gap-3 pt-2">

                <a
                    href="{{ route('tasks.show', $task) }}"
                    class="mc-btn mc-btn-ghost">

                    Batal

                </a>

                <button
                    type="submit"
                    class="mc-btn mc-btn-primary">

                    Simpan Perubahan

                </button>

            </div>

        </form>

    </div>

</div>

@endsection