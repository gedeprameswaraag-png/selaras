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

    .mc-input:disabled {
        opacity: .5;
    }

    .mc-input-error {
        border-color: #D9827A;
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

    .mc-panel {
        background: #F5F9F6;
        border: 1px solid var(--mc-line);
        border-radius: 1.25rem;
    }

    /* Pilihan hari: chip yang menyala saat dicentang */
    .mc-day {
        display: flex;
        align-items: center;
        gap: .5rem;
        min-height: 2.75rem;
        padding: .6rem 1rem;
        border: 1px solid #C9D6CF;
        border-radius: .875rem;
        background: #fff;
        cursor: pointer;
        transition: background .2s, border-color .2s;
    }

    .mc-day:hover {
        background: #F5F9F6;
    }

    .mc-day:has(input:checked) {
        background: var(--mc-soft);
        border-color: var(--mc-primary);
        color: var(--mc-primary);
        font-weight: 600;
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

<div class="max-w-3xl mx-auto space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl sm:text-3xl font-semibold">
            Tambah Jadwal
        </h1>

        <p class="mt-1 text-sm mc-muted">
            Tambahkan aktivitas sekali atau jadwal yang berulang.
        </p>
    </div>


    {{-- Validation Error --}}
    @if ($errors->any())
        <div class="mc-alert-error p-4">

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


    {{-- Form --}}
    <div class="mc-card p-4 sm:p-6">

        <form
            id="schedule-form"
            action="{{ route('calendar.store') }}"
            method="POST"
            class="space-y-6">

            @csrf


            {{-- ================================================= --}}
            {{-- NAMA JADWAL --}}
            {{-- ================================================= --}}

            <div>

                <label class="mc-label">
                    Nama Jadwal
                    <span class="mc-error-text">*</span>
                </label>

                <input
                    type="text"
                    name="title"
                    value="{{ old('title') }}"
                    placeholder="Contoh: Pemrograman Web"
                    required
                    class="mc-input
                           @error('title') mc-input-error @enderror">

                @error('title')
                    <p class="mt-1 text-sm mc-error-text">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- ================================================= --}}
            {{-- TYPE --}}
            {{-- ================================================= --}}

            <div>

                <label class="mc-label">
                    Tipe
                    <span class="mc-error-text">*</span>
                </label>

                <select
                    name="type"
                    required
                    class="mc-input">

                    <option value="academic"
                        {{ old('type') === 'academic' ? 'selected' : '' }}>
                        Akademik
                    </option>

                    <option value="organization"
                        {{ old('type') === 'organization' ? 'selected' : '' }}>
                        Organisasi
                    </option>

                    <option value="personal"
                        {{ old('type') === 'personal' ? 'selected' : '' }}>
                        Pribadi
                    </option>

                    <option value="rest"
                        {{ old('type') === 'rest' ? 'selected' : '' }}>
                        Istirahat
                    </option>

                    <option value="other"
                        {{ old('type') === 'other' ? 'selected' : '' }}>
                        Lainnya
                    </option>

                </select>

            </div>


            {{-- ================================================= --}}
            {{-- RECURRING TOGGLE --}}
            {{-- ================================================= --}}

            <div class="mc-panel p-5">

                <label class="flex items-start gap-3 cursor-pointer">

                    <input
                        type="checkbox"
                        name="is_recurring"
                        value="1"
                        id="is-recurring"
                        {{ old('is_recurring') ? 'checked' : '' }}
                        class="mc-check mt-1">

                    <div>

                        <p class="font-semibold">
                            Jadwal berulang
                        </p>

                        <p class="text-sm mc-muted mt-1">
                            Aktifkan jika jadwal dilakukan secara rutin,
                            misalnya setiap Senin selama beberapa minggu.
                        </p>

                    </div>

                </label>

            </div>


            {{-- ================================================= --}}
            {{-- JADWAL BIASA --}}
            {{-- ================================================= --}}

            <div
                id="normal-schedule"
                class="space-y-5">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    {{-- Start --}}
                    <div>

                        <label class="mc-label">
                            Mulai
                            <span class="mc-error-text">*</span>
                        </label>

                        <input
                            id="normal-start-time"
                            type="datetime-local"
                            name="start_time"
                            value="{{ old('start_time') }}"
                            class="mc-input">

                        <p
                            id="normal-start-error"
                            class="hidden mt-1 text-sm mc-error-text">

                            Waktu mulai wajib diisi.

                        </p>

                    </div>


                    {{-- End --}}
                    <div>

                        <label class="mc-label">
                            Selesai
                            <span class="mc-error-text">*</span>
                        </label>

                        <input
                            id="normal-end-time"
                            type="datetime-local"
                            name="end_time"
                            value="{{ old('end_time') }}"
                            class="mc-input">

                        <p
                            id="normal-end-error"
                            class="hidden mt-1 text-sm mc-error-text">

                            Waktu selesai wajib diisi.

                        </p>

                    </div>

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- JADWAL BERULANG --}}
            {{-- ================================================= --}}

            <div
                id="recurring-options"
                class="hidden space-y-5 border-t pt-5"
                style="border-color: var(--mc-line);">

                {{-- Type recurring --}}
                <input
                    type="hidden"
                    name="recurrence_type"
                    value="weekly"
                    id="recurrence-type">


                <div>

                    <h2 class="text-lg font-semibold">
                        Pengaturan Jadwal Berulang
                    </h2>

                    <p class="text-sm mc-muted mt-1">
                        Jadwal akan dibuat otomatis untuk setiap hari
                        yang kamu pilih.
                    </p>

                </div>


                {{-- Start Date --}}
                <div>

                    <label class="mc-label">
                        Mulai dari tanggal
                        <span class="mc-error-text">*</span>
                    </label>

                    <input
                        id="recurring-start-date"
                        type="date"
                        name="start_date"
                        value="{{ old('start_date') }}"
                        class="mc-input">

                </div>


                {{-- Time --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <div>

                        <label class="mc-label">
                            Jam mulai
                            <span class="mc-error-text">*</span>
                        </label>

                        <input
                            id="recurring-start-time"
                            type="time"
                            name="recurring_start_time"
                            value="{{ old('recurring_start_time') }}"
                            class="mc-input">

                    </div>


                    <div>

                        <label class="mc-label">
                            Jam selesai
                            <span class="mc-error-text">*</span>
                        </label>

                        <input
                            id="recurring-end-time"
                            type="time"
                            name="recurring_end_time"
                            value="{{ old('recurring_end_time') }}"
                            class="mc-input">

                    </div>

                </div>


                {{-- Days --}}
                <div>

                    <label class="mc-label" style="margin-bottom: .75rem;">
                        Hari
                        <span class="mc-error-text">*</span>
                    </label>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">

                        @php
                            $days = [
                                1 => 'Senin',
                                2 => 'Selasa',
                                3 => 'Rabu',
                                4 => 'Kamis',
                                5 => 'Jumat',
                                6 => 'Sabtu',
                                0 => 'Minggu',
                            ];
                        @endphp

                        @foreach($days as $value => $label)

                            <label class="mc-day">

                                <input
                                    type="checkbox"
                                    name="recurrence_days[]"
                                    value="{{ $value }}"
                                    class="recurrence-day mc-check"
                                    {{ in_array(
                                        $value,
                                        old('recurrence_days', [])
                                    ) ? 'checked' : '' }}>

                                <span class="text-sm">
                                    {{ $label }}
                                </span>

                            </label>

                        @endforeach

                    </div>

                    <p
                        id="days-error"
                        class="hidden mt-2 text-sm mc-error-text">

                        Pilih minimal satu hari.

                    </p>

                </div>


                {{-- End Date --}}
                <div>

                    <label class="mc-label">
                        Berakhir pada
                        <span class="mc-error-text">*</span>
                    </label>

                    <input
                        id="recurring-end-date"
                        type="date"
                        name="recurrence_end_date"
                        value="{{ old('recurrence_end_date') }}"
                        class="mc-input">

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- FIXED --}}
            {{-- ================================================= --}}

            <div class="rounded-2xl border p-4" style="border-color: var(--mc-line);">

                <label class="flex items-center gap-3 cursor-pointer">

                    <input
                        type="checkbox"
                        name="is_fixed"
                        value="1"
                        {{ old('is_fixed') ? 'checked' : '' }}
                        class="mc-check">

                    <div>

                        <p class="text-sm font-semibold">
                            Jadwal tetap
                        </p>

                        <p class="text-xs mc-muted mt-1">
                            Tandai jika jadwal ini sulit atau tidak bisa dipindahkan.
                        </p>

                    </div>

                </label>

            </div>


            {{-- ================================================= --}}
            {{-- DESCRIPTION --}}
            {{-- ================================================= --}}

            <div>

                <label class="mc-label">
                    Deskripsi
                </label>

                <textarea
                    name="description"
                    rows="4"
                    placeholder="Tambahkan catatan jika diperlukan..."
                    class="mc-input">{{ old('description') }}</textarea>

            </div>


            {{-- ================================================= --}}
            {{-- BUTTON --}}
            {{-- ================================================= --}}

            <div class="flex flex-col-reverse sm:flex-row gap-3 pt-2">

                <a
                    href="{{ route('calendar') }}"
                    class="mc-btn mc-btn-ghost flex-1 text-center">

                    Batal

                </a>


                <button
                    type="submit"
                    class="mc-btn mc-btn-primary flex-1">

                    Simpan Jadwal

                </button>

            </div>

        </form>

    </div>

</div>


{{-- ========================================================= --}}
{{-- MODAL BENTROK --}}
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
                            Jadwal yang kamu masukkan bertabrakan
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
                    Kamu tetap dapat menyimpan jadwal ini jika
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
    // AMBIL FORM KHUSUS JADWAL
    // =================================================

    const form =
        document.getElementById('schedule-form');


    const recurringCheckbox =
        document.getElementById('is-recurring');


    const normalSchedule =
        document.getElementById('normal-schedule');


    const recurringOptions =
        document.getElementById('recurring-options');


    // =================================================
    // NORMAL FIELDS
    // =================================================

    const normalStart =
        document.getElementById('normal-start-time');


    const normalEnd =
        document.getElementById('normal-end-time');


    // =================================================
    // RECURRING FIELDS
    // =================================================

    const recurringStartDate =
        document.getElementById('recurring-start-date');


    const recurringStartTime =
        document.getElementById('recurring-start-time');


    const recurringEndTime =
        document.getElementById('recurring-end-time');


    const recurringEndDate =
        document.getElementById('recurring-end-date');


    const recurrenceDays =
        document.querySelectorAll('.recurrence-day');


    const daysError =
        document.getElementById('days-error');


    // =================================================
    // TOGGLE RECURRING
    // =================================================

    function toggleRecurring() {

        const isRecurring =
            recurringCheckbox.checked;


        if (isRecurring) {

            // Tampilkan recurring
            normalSchedule.classList.add('hidden');

            recurringOptions.classList.remove('hidden');


            // Disable normal fields
            normalStart.disabled = true;
            normalEnd.disabled = true;

            normalStart.required = false;
            normalEnd.required = false;


            // Enable recurring fields
            recurringStartDate.disabled = false;
            recurringStartTime.disabled = false;
            recurringEndTime.disabled = false;
            recurringEndDate.disabled = false;

            recurringStartDate.required = true;
            recurringStartTime.required = true;
            recurringEndTime.required = true;
            recurringEndDate.required = true;

        } else {

            // Tampilkan normal
            normalSchedule.classList.remove('hidden');

            recurringOptions.classList.add('hidden');


            // Enable normal fields
            normalStart.disabled = false;
            normalEnd.disabled = false;

            normalStart.required = true;
            normalEnd.required = true;


            // Disable recurring fields
            recurringStartDate.disabled = true;
            recurringStartTime.disabled = true;
            recurringEndTime.disabled = true;
            recurringEndDate.disabled = true;

            recurringStartDate.required = false;
            recurringStartTime.required = false;
            recurringEndTime.required = false;
            recurringEndDate.required = false;

        }

    }


    // =================================================
    // CHECKBOX RECURRING
    // =================================================

    recurringCheckbox.addEventListener(
        'change',
        toggleRecurring
    );


    // =================================================
    // VALIDASI RECURRING DAYS
    // =================================================

    function validateDays() {

        if (!recurringCheckbox.checked) {

            daysError.classList.add('hidden');

            return true;
        }


        const checked =
            Array.from(recurrenceDays)
                .some(day => day.checked);


        if (!checked) {

            daysError.classList.remove('hidden');

            return false;
        }


        daysError.classList.add('hidden');

        return true;

    }


    recurrenceDays.forEach(day => {

        day.addEventListener(
            'change',
            validateDays
        );

    });


    // =================================================
    // VALIDASI FORM
    // =================================================

    form.addEventListener(
        'submit',
        function(event) {

            if (!validateDays()) {

                event.preventDefault();

                daysError.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                return;
            }

        }
    );


    // =================================================
    // TETAP SIMPAN
    // =================================================

    function forceSaveSchedule() {

        let forceInput =
            form.querySelector(
                'input[name="force_save"]'
            );


        if (!forceInput) {

            forceInput =
                document.createElement('input');

            forceInput.type = 'hidden';

            forceInput.name = 'force_save';

            form.appendChild(forceInput);

        }


        forceInput.value = '1';


        /*
         * Jangan menggunakan document.querySelector('form')
         * di sini.
         *
         * Kita secara spesifik mengirim
         * form jadwal.
         */

        form.submit();

    }


    // =================================================
    // TUTUP MODAL
    // =================================================

    function closeConflictModal() {

        const modal =
            document.getElementById('conflict-modal');


        if (modal) {

            modal.remove();

        }

    }


    // =================================================
    // JALANKAN PERTAMA KALI
    // =================================================

    toggleRecurring();

</script>

@endsection