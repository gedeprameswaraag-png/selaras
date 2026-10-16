@extends('layouts.app')

@section('content')

{{-- Gaya halaman ini (memakai warna dari layout) --}}
<style>
    .mc-card {
        background: #fff;
        border: 1px solid var(--mc-line);
        border-radius: 1.25rem;
    }

    .mc-muted {
        color: var(--mc-muted);
    }

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

    .mc-check {
        width: 1.2rem;
        height: 1.2rem;
        accent-color: var(--mc-primary);
        flex-shrink: 0;
    }

    .mc-error-text {
        color: #B4443C;
    }

    .mc-alert-error {
        background: #FBF1F0;
        border: 1px solid #EBC9C5;
        border-radius: 1rem;
    }

    .mc-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 2.75rem;
        padding: .6rem 1.25rem;
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

    .mc-btn:focus-visible {
        outline: 3px solid var(--mc-sun);
        outline-offset: 2px;
    }
</style>

<div class="max-w-3xl mx-auto">

    <div class="mb-6">

        <h1 class="text-2xl sm:text-3xl font-semibold">
            Edit Jadwal
        </h1>

        <p class="mt-1 text-sm mc-muted">
            Perbarui informasi jadwal kamu.
        </p>

    </div>


    {{-- Validation Error --}}
    @if ($errors->any())

        <div class="mc-alert-error mb-6 p-4">

            <div class="flex items-start gap-3">

                <div class="mc-error-text text-lg">
                    ⚠
                </div>

                <div>

                    <p class="text-sm font-semibold" style="color: #8E342D;">
                        Ada data yang belum lengkap atau tidak valid.
                    </p>

                    <ul class="mt-2 space-y-1 text-sm mc-error-text">

                        @foreach ($errors->all() as $error)

                            <li>
                                • {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            </div>

        </div>

    @endif


    <div class="mc-card p-4 sm:p-6">

        <form
            id="edit-schedule-form"
            action="{{ route('calendar.update', $schedule) }}"
            method="POST"
            class="space-y-5">

            @csrf
            @method('PUT')


            {{-- Title --}}
            <div>

                <label class="mc-label">
                    Nama Jadwal
                </label>

                <input
                    type="text"
                    name="title"
                    value="{{ old('title', $schedule->title) }}"
                    required
                    class="mc-input">

                @error('title')

                    <p class="mt-1 text-sm mc-error-text">
                        {{ $message }}
                    </p>

                @enderror

            </div>


            {{-- Start --}}
            <div>

                <label class="mc-label">
                    Mulai
                </label>

                <input
                    type="datetime-local"
                    name="start_time"
                    value="{{ old(
                        'start_time',
                        \Carbon\Carbon::parse(
                            $schedule->start_time
                        )->format('Y-m-d\TH:i')
                    ) }}"
                    required
                    class="mc-input">

                @error('start_time')

                    <p class="mt-1 text-sm mc-error-text">
                        {{ $message }}
                    </p>

                @enderror

            </div>


            {{-- End --}}
            <div>

                <label class="mc-label">
                    Selesai
                </label>

                <input
                    type="datetime-local"
                    name="end_time"
                    value="{{ old(
                        'end_time',
                        \Carbon\Carbon::parse(
                            $schedule->end_time
                        )->format('Y-m-d\TH:i')
                    ) }}"
                    required
                    class="mc-input">

                @error('end_time')

                    <p class="mt-1 text-sm mc-error-text">
                        {{ $message }}
                    </p>

                @enderror

            </div>


            {{-- Type --}}
            <div>

                <label class="mc-label">
                    Tipe Jadwal
                </label>

                <select
                    name="type"
                    required
                    class="mc-input">

                    <option value="academic"
                        {{ old('type', $schedule->type) === 'academic' ? 'selected' : '' }}>
                        Akademik
                    </option>

                    <option value="organization"
                        {{ old('type', $schedule->type) === 'organization' ? 'selected' : '' }}>
                        Organisasi
                    </option>

                    <option value="personal"
                        {{ old('type', $schedule->type) === 'personal' ? 'selected' : '' }}>
                        Pribadi
                    </option>

                    <option value="rest"
                        {{ old('type', $schedule->type) === 'rest' ? 'selected' : '' }}>
                        Istirahat
                    </option>

                    <option value="other"
                        {{ old('type', $schedule->type) === 'other' ? 'selected' : '' }}>
                        Lainnya
                    </option>

                </select>

            </div>


            {{-- Fixed --}}
            <div class="flex items-center gap-3">

                <input
                    type="checkbox"
                    name="is_fixed"
                    value="1"
                    {{ old(
                        'is_fixed',
                        $schedule->is_fixed
                    ) ? 'checked' : '' }}
                    class="mc-check">

                <label class="text-sm">
                    Jadwal tetap
                </label>

            </div>


            {{-- Description --}}
            <div>

                <label class="mc-label">
                    Deskripsi
                </label>

                <textarea
                    name="description"
                    rows="4"
                    class="mc-input">{{ old(
                               'description',
                               $schedule->description
                           ) }}</textarea>

            </div>


            {{-- Buttons --}}
            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3 pt-4">

                <a
                    href="{{ route('calendar') }}"
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


{{-- ========================================================= --}}
{{-- MODAL BENTROK JADWAL --}}
{{-- ========================================================= --}}

@if (session('schedule_conflicts'))

    <div
        id="conflict-modal"
        class="fixed inset-0 z-[200] flex items-center justify-center
               px-4"
        style="background: rgba(22,48,43,.55);">

        <div
            class="w-full max-w-lg rounded-3xl bg-white shadow-2xl
                   overflow-hidden max-h-[92vh] flex flex-col">

            {{-- Header --}}
            <div class="px-6 pt-6">

                <div class="flex items-start gap-4">

                    <div
                        class="flex h-11 w-11 shrink-0 items-center
                               justify-center rounded-full text-xl"
                        style="background: #FDF3DE;">

                        ⚠️

                    </div>

                    <div>

                        <h2 class="text-xl font-semibold">
                            Jadwal Bertabrakan
                        </h2>

                        <p class="mt-1 text-sm mc-muted">
                            Perubahan jadwal kamu bertabrakan
                            dengan jadwal yang sudah ada.
                        </p>

                    </div>

                </div>

            </div>


            {{-- Conflict List --}}
            <div class="px-6 py-5 overflow-y-auto">

                <div class="space-y-3 max-h-64 overflow-y-auto">

                    @foreach (session('schedule_conflicts') as $conflict)

                        <div
                            class="rounded-xl border p-4"
                            style="border-color: #F0D9A5; background: #FDF3DE;">

                            @if (!empty($conflict['date']))

                                <p class="text-xs font-semibold" style="color: #8A5A0B;">
                                    {{ $conflict['date'] }}
                                </p>

                            @endif

                            <p class="mt-1 font-semibold">
                                {{ $conflict['title'] }}
                            </p>

                            <p class="mt-1 text-sm mc-muted">
                                {{ $conflict['start'] }}
                                –
                                {{ $conflict['end'] }}
                            </p>

                        </div>

                    @endforeach

                </div>


                <p class="mt-4 text-sm leading-6 mc-muted">
                    Kamu tetap dapat menyimpan perubahan ini jika
                    memang diperlukan. Namun jadwal akan tetap
                    memiliki waktu yang bertabrakan.
                </p>

            </div>


            {{-- Actions --}}
            <div
                class="flex flex-col-reverse sm:flex-row
                       gap-3 border-t
                       px-6 py-4"
                style="border-color: var(--mc-line); background: #F5F9F6;">

                {{-- Ubah Jadwal --}}
                <button
                    type="button"
                    onclick="closeConflictModal()"
                    class="mc-btn mc-btn-ghost flex-1">

                    Ubah Jadwal

                </button>


                {{-- Tetap Simpan --}}
                <button
                    type="button"
                    onclick="forceSaveSchedule()"
                    class="mc-btn mc-btn-primary flex-1">

                    Tetap Simpan

                </button>

            </div>

        </div>

    </div>

@endif


{{-- ========================================================= --}}
{{-- JAVASCRIPT --}}
{{-- ========================================================= --}}

<script>

    // =================================================
    // FORM EDIT
    // =================================================

    const editForm =
        document.getElementById('edit-schedule-form');


    // =================================================
    // FORCE SAVE
    // =================================================

    function forceSaveSchedule() {

        let forceInput =
            editForm.querySelector(
                'input[name="force_save"]'
            );


        if (!forceInput) {

            forceInput =
                document.createElement('input');

            forceInput.type = 'hidden';

            forceInput.name = 'force_save';

            editForm.appendChild(forceInput);

        }


        forceInput.value = '1';


        /*
         * Kirim form edit secara spesifik.
         *
         * Jangan menggunakan:
         * document.querySelector('form')
         *
         * karena layout aplikasi juga bisa
         * mempunyai form lain seperti logout.
         */

        editForm.submit();

    }


    // =================================================
    // CLOSE MODAL
    // =================================================

    function closeConflictModal() {

        const modal =
            document.getElementById('conflict-modal');


        if (modal) {

            modal.remove();

        }

    }

</script>

@endsection