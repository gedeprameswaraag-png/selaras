@extends('layouts.app')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')

{{-- Gaya halaman ini (memakai warna dari layout) --}}
<style>
    .mc-card { background: #fff; border: 1px solid var(--mc-line); border-radius: 1.25rem; overflow: hidden; }
    .mc-card-head { border-bottom: 1px solid var(--mc-line); }
    .mc-muted { color: var(--mc-muted); }
    .mc-faint { color: #8CA097; }
    .mc-error-text { color: #B4443C; }

    .mc-ico {
        width: 2.5rem; height: 2.5rem; border-radius: .875rem; flex-shrink: 0;
        background: var(--mc-soft); color: var(--mc-primary);
        display: flex; align-items: center; justify-content: center;
    }

    /* Baris pengaturan */
    .mc-item { border: 1px solid var(--mc-line); border-radius: 1rem; }
    .mc-tile { background: #F5F9F6; border: 1px solid var(--mc-line); border-radius: 1rem; }

    /* Form */
    .mc-input {
        width: 100%; border: 1px solid #C9D6CF; border-radius: .875rem;
        padding: .75rem .9rem; background: #fff; color: var(--mc-ink);
        font-size: 1rem; min-height: 2.75rem;
    }
    .mc-input:focus { outline: none; border-color: var(--mc-primary); box-shadow: 0 0 0 3px var(--mc-soft); }
    .mc-input:disabled { background: #EEF3EF; color: var(--mc-muted); cursor: not-allowed; }
    .mc-label { display: block; font-size: .875rem; font-weight: 600; margin-bottom: .4rem; }

    .mc-btn {
        display: inline-flex; align-items: center; justify-content: center;
        min-height: 2.75rem; padding: .6rem 1.25rem; border-radius: .875rem;
        font-size: .875rem; font-weight: 600; transition: background .2s, color .2s;
    }
    .mc-btn-primary { background: var(--mc-ink); color: #fff; }
    .mc-btn-primary:hover { background: var(--mc-primary); }
    .mc-btn-sun { background: var(--mc-sun); color: var(--mc-ink); }
    .mc-btn-sun:hover { background: #fff; }
    .mc-btn:focus-visible { outline: 3px solid var(--mc-sun); outline-offset: 2px; }

    /* Badge */
    .mc-badge { border-radius: 9999px; padding: .375rem .75rem; font-size: .75rem; font-weight: 600; }
    .mc-b-ok { background: var(--mc-soft); color: var(--mc-primary); }
    .mc-b-warn { background: #FDF3DE; color: #8A5A0B; }

    /* Toggle statis (notifikasi) */
    .mc-switch-on { background: var(--mc-primary); }
</style>

<div class="max-w-5xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div>
        <h1 class="text-2xl sm:text-3xl font-semibold">
            Settings
        </h1>

        <p class="mt-1 text-sm mc-muted">
            Kelola akun dan preferensi Selaras kamu.
        </p>
    </div>


    {{-- ACCOUNT --}}
    <section class="mc-card">

        <div class="mc-card-head px-4 sm:px-6 py-5">

            <div class="flex items-center gap-3">

                <div class="mc-ico">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.8"
                        stroke="currentColor"
                        class="w-5 h-5">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a7.5 7.5 0 0115 0" />

                    </svg>

                </div>

                <div>

                    <h2 class="text-lg font-semibold">
                        Account
                    </h2>

                    <p class="text-sm mc-muted">
                        Informasi dasar akun kamu.
                    </p>

                </div>

            </div>

        </div>


        <form
            method="POST"
            action="{{ route('profile.update') }}"
            class="p-4 sm:p-6 space-y-5">

            @csrf
            @method('PATCH')


            {{-- NAME --}}
            <div>

                <label
                    for="name"
                    class="mc-label">

                    Nama

                </label>

                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name', $user->name) }}"
                    required
                    autocomplete="name"
                    class="mc-input">

                @error('name')
                    <p class="mt-2 text-sm mc-error-text">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- EMAIL --}}
            <div>

                <label
                    for="email"
                    class="mc-label">

                    Email

                </label>

                <input
                    id="email"
                    type="email"
                    value="{{ $user->email }}"
                    disabled
                    class="mc-input">

                <p class="mt-2 text-xs mc-faint">
                    Email tidak dapat diubah.
                </p>

            </div>


            {{-- EMAIL STATUS --}}
            <div class="mc-tile flex items-center justify-between gap-3
                        px-4 py-3">

                <div>

                    <p class="text-sm font-semibold">
                        Status email
                    </p>

                    <p class="text-xs mc-muted mt-1">

                        @if($user->email_verified_at)

                            Email sudah terverifikasi.

                        @else

                            Email belum terverifikasi.

                        @endif

                    </p>

                </div>


                @if($user->email_verified_at)

                    <span class="mc-badge mc-b-ok shrink-0">

                        Terverifikasi

                    </span>

                @else

                    <span class="mc-badge mc-b-warn shrink-0">

                        Belum diverifikasi

                    </span>

                @endif

            </div>


            <div class="flex sm:justify-end">

                <button
                    type="submit"
                    class="mc-btn mc-btn-primary w-full sm:w-auto">

                    Simpan Perubahan

                </button>

            </div>

        </form>

    </section>


    {{-- PLANNER --}}
    <section class="mc-card">

        <div class="mc-card-head px-4 sm:px-6 py-5">

            <div class="flex items-center gap-3">

                <div class="mc-ico">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.8"
                        stroke="currentColor"
                        class="w-5 h-5">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 6v6l4 2M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />

                    </svg>

                </div>

                <div>

                    <h2 class="text-lg font-semibold">
                        Planner
                    </h2>

                    <p class="text-sm mc-muted">
                        Pengaturan dasar perencanaan aktivitas.
                    </p>

                </div>

            </div>

        </div>


        <div class="p-4 sm:p-6 space-y-4">

            {{-- ACTIVE HOURS --}}
            <div
                class="mc-item flex flex-col sm:flex-row sm:items-center sm:justify-between
                       gap-2 sm:gap-6
                       px-4 py-4">

                <div>

                    <p class="font-semibold">
                        Jam aktivitas
                    </p>

                    <p class="text-sm mc-muted mt-1">
                        Rentang waktu yang digunakan Selaras
                        untuk membantu menyusun jadwal.
                    </p>

                </div>

                <div class="shrink-0 text-lg font-semibold"
                     style="font-family: 'Fraunces', Georgia, serif; color: var(--mc-primary);">
                    05.00 — 22.00
                </div>

            </div>


            {{-- REST --}}
            <div
                class="mc-item flex items-center justify-between
                       gap-4 sm:gap-6
                       px-4 py-4">

                <div>

                    <p class="font-semibold">
                        Waktu recovery
                    </p>

                    <p class="text-sm mc-muted mt-1">
                        Selaras dapat menyarankan jeda
                        ketika beban aktivitas cukup tinggi.
                    </p>

                </div>

                <span class="mc-badge mc-b-ok shrink-0">

                    Aktif

                </span>

            </div>


            {{-- SMART SCHEDULING --}}
            <div
                class="mc-item flex items-center justify-between
                       gap-4 sm:gap-6
                       px-4 py-4">

                <div>

                    <p class="font-semibold">
                        Smart Scheduling
                    </p>

                    <p class="text-sm mc-muted mt-1">
                        Mencari waktu kosong sebelum deadline
                        berdasarkan jadwal yang sudah ada.
                    </p>

                </div>

                <span class="mc-badge mc-b-ok shrink-0">

                    Aktif

                </span>

            </div>

        </div>

    </section>


    {{-- NOTIFICATIONS --}}
    <section class="mc-card">

        <div class="mc-card-head px-4 sm:px-6 py-5">

            <div class="flex items-center gap-3">

                <div class="mc-ico">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.8"
                        stroke="currentColor"
                        class="w-5 h-5">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9a6 6 0 10-12 0v.75c0 2.093-.71 4.024-2.31 5.56a23.85 23.85 0 005.453 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />

                    </svg>

                </div>

                <div>

                    <h2 class="text-lg font-semibold">
                        Notifications
                    </h2>

                    <p class="text-sm mc-muted">
                        Informasi dan pengingat dari Selaras.
                    </p>

                </div>

            </div>

        </div>


        <div class="p-4 sm:p-6 space-y-3">

            {{-- DEADLINE --}}
            <div
                class="mc-item flex items-center justify-between
                       gap-4
                       px-4 py-4">

                <div>

                    <p class="font-semibold">
                        Pengingat deadline
                    </p>

                    <p class="text-sm mc-muted mt-1">
                        Membantu kamu memperhatikan task
                        sebelum deadline mendekat.
                    </p>

                </div>

                <span
                    class="mc-switch-on w-11 h-6 rounded-full
                           relative shrink-0">

                    <span
                        class="absolute
                               top-1 right-1
                               w-4 h-4
                               rounded-full
                               bg-white">
                    </span>

                </span>

            </div>


            {{-- SCHEDULE --}}
            <div
                class="mc-item flex items-center justify-between
                       gap-4
                       px-4 py-4">

                <div>

                    <p class="font-semibold">
                        Pengingat jadwal
                    </p>

                    <p class="text-sm mc-muted mt-1">
                        Mengingatkan aktivitas yang sudah
                        kamu masukkan ke Calendar.
                    </p>

                </div>

                <span
                    class="mc-switch-on w-11 h-6 rounded-full
                           relative shrink-0">

                    <span
                        class="absolute
                               top-1 right-1
                               w-4 h-4
                               rounded-full
                               bg-white">
                    </span>

                </span>

            </div>


            {{-- CHECK-IN --}}
            <div
                class="mc-item flex items-center justify-between
                       gap-4
                       px-4 py-4">

                <div>

                    <p class="font-semibold">
                        Check-in harian
                    </p>

                    <p class="text-sm mc-muted mt-1">
                        Pengingat untuk melakukan check-in
                        kondisi harian.
                    </p>

                </div>

                <span
                    class="mc-switch-on w-11 h-6 rounded-full
                           relative shrink-0">

                    <span
                        class="absolute
                               top-1 right-1
                               w-4 h-4
                               rounded-full
                               bg-white">
                    </span>

                </span>

            </div>

        </div>

    </section>


    {{-- SECURITY --}}
    <section class="mc-card">

        <div class="mc-card-head px-4 sm:px-6 py-5">

            <div class="flex items-center gap-3">

                <div class="mc-ico">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.8"
                        stroke="currentColor"
                        class="w-5 h-5">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M16.5 10.5V7.875a4.5 4.5 0 00-9 0V10.5m-1.125 0h11.25A1.125 1.125 0 0118.75 11.625v7.5a1.125 1.125 0 01-1.125 1.125H6.375a1.125 1.125 0 01-1.125-1.125v-7.5A1.125 1.125 0 016.375 10.5z" />

                    </svg>

                </div>

                <div>

                    <h2 class="text-lg font-semibold">
                        Security
                    </h2>

                    <p class="text-sm mc-muted">
                        Kelola keamanan akun kamu.
                    </p>

                </div>

            </div>

        </div>


        <form
            method="POST"
            action="{{ route('settings.password') }}"
            class="p-4 sm:p-6 space-y-5">

            @csrf
            @method('PUT')


            {{-- CURRENT PASSWORD --}}
            <div>

                <label
                    for="current_password"
                    class="mc-label">

                    Password saat ini

                </label>

                <input
                    id="current_password"
                    name="current_password"
                    type="password"
                    required
                    autocomplete="current-password"
                    class="mc-input">

                @error('current_password')
                    <p class="mt-2 text-sm mc-error-text">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- NEW PASSWORD --}}
            <div>

                <label
                    for="password"
                    class="mc-label">

                    Password baru

                </label>

                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="new-password"
                    class="mc-input">

                @error('password')
                    <p class="mt-2 text-sm mc-error-text">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- CONFIRM PASSWORD --}}
            <div>

                <label
                    for="password_confirmation"
                    class="mc-label">

                    Konfirmasi password baru

                </label>

                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    class="mc-input">

            </div>


            <div class="flex sm:justify-end">

                <button
                    type="submit"
                    class="mc-btn mc-btn-primary w-full sm:w-auto">

                    Ubah Password

                </button>

            </div>

        </form>

    </section>


    {{-- ABOUT --}}
    <section class="rounded-[1.25rem] p-5 sm:p-6"
             style="background: var(--mc-ink); color: #fff;">

        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-5 sm:gap-6">

            <div>

                <p class="text-sm font-semibold" style="color: var(--mc-sun);">
                    Selaras
                </p>

                <h2 class="text-2xl font-semibold mt-2">
                    Rencanakan Harimu, Jaga Dirimu.
                </h2>

                <p class="text-sm mt-2 max-w-2xl leading-6" style="color: var(--mc-side-text);">

                    Selaras membantu mahasiswa mengatur jadwal,
                    task, deadline, waktu pengerjaan, dan check-in
                    agar aktivitas dapat direncanakan sebelum pekerjaan
                    menumpuk.

                </p>

            </div>


            <div class="shrink-0 self-start rounded-xl bg-white/10 px-3 py-2">

                <p class="text-xs" style="color: var(--mc-side-text);">
                    Version
                </p>

                <p class="text-sm font-semibold mt-1">
                    1.0.0
                </p>

            </div>

        </div>


        {{-- LAYANAN AHLI --}}
        <div class="mt-6 pt-5 border-t border-white/10
                    flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

            <p class="text-sm leading-6" style="color: var(--mc-side-text);">
                Jika kamu membutuhkan layanan ahli, klik tautan di samping.
            </p>

            <a
                href="https://www.healing119.id/"
                target="_blank"
                rel="noopener noreferrer"
                class="mc-btn mc-btn-sun shrink-0">

                Kunjungi Healing119 →

            </a>

        </div>

    </section>

</div>

@endsection