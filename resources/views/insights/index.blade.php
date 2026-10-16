@extends('layouts.app')

@section('title', 'Insights')
@section('page-title', 'Insights')

@section('content')

{{-- Gaya khusus halaman ini (memakai warna dari layout, sama dengan Dashboard) --}}
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

    /* Switch periode */
    .mc-seg {
        display: inline-flex;
        align-items: center;
        border-radius: .875rem;
        background: var(--mc-soft);
        padding: .25rem;
    }

    .mc-seg-item {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 2.5rem;
        padding: .5rem 1rem;
        border-radius: .7rem;
        font-size: .75rem;
        font-weight: 600;
        color: var(--mc-muted);
        transition: background .2s, color .2s;
    }

    .mc-seg-item:hover {
        color: var(--mc-ink);
    }

    .mc-seg-item.is-active {
        background: #fff;
        color: var(--mc-primary);
        box-shadow: 0 1px 2px rgba(22, 48, 43, .08);
    }

    /* Tombol navigasi periode */
    .mc-nav {
        width: 2.75rem;
        height: 2.75rem;
        border-radius: 9999px;
        border: 1px solid #C9D6CF;
        background: #fff;
        color: var(--mc-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background .2s, color .2s;
    }

    .mc-nav:hover {
        background: #F5F9F6;
        color: var(--mc-ink);
    }

    /* Bar chart */
    .mc-bar {
        background: var(--mc-primary);
        transition: background .2s, height .3s;
    }

    .mc-bar:hover {
        background: var(--mc-ink);
    }

    /* Panel info kecil */
    .mc-panel {
        background: #F5F9F6;
        border-radius: 1rem;
        padding: 1rem;
    }

    .mc-seg-item:focus-visible,
    .mc-nav:focus-visible {
        outline: 3px solid var(--mc-sun);
        outline-offset: 2px;
    }
</style>

<div class="space-y-6">


    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div>

        <h1 class="text-2xl sm:text-3xl font-semibold" style="color: var(--mc-ink);">
            Insights
        </h1>

        <p class="mc-muted mt-1">
            Pahami pola aktivitasmu dan lihat apa yang bisa kamu lakukan.
        </p>

    </div>


    {{-- ========================================================= --}}
    {{-- RINGKASAN --}}
    {{-- ========================================================= --}}

    <div class="grid grid-cols-2 md:grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">


        {{-- Beban Hari Ini (kartu paling menonjol) --}}
        <div class="col-span-2 md:col-span-1 p-5 rounded-[1.25rem]"
             style="background: var(--mc-ink); color: #fff;">

            <p class="text-sm" style="color: var(--mc-side-text);">
                Beban Hari Ini
            </p>

            <div class="flex items-end gap-2 mt-2">

                <h2 class="text-2xl sm:text-3xl font-semibold">

                    {{ round(($todayData['total_minutes'] ?? 0) / 60, 1) }}

                </h2>

                <span class="text-sm mb-1" style="color: var(--mc-side-text);">
                    jam
                </span>

            </div>


            @if($loadDifference !== null && $loadDifference > 0)

            <p class="text-xs mt-2" style="color: #F6C979;">

                ↑ {{ $loadDifference }}%
                dari rata-rata 7 hari

            </p>

            @elseif($loadDifference !== null && $loadDifference < 0)

            <p class="text-xs mt-2" style="color: #9FD8BF;">

                ↓ {{ abs($loadDifference) }}%
                dari rata-rata 7 hari

            </p>

            @else

            <p class="text-xs mt-2" style="color: var(--mc-side-text);">
                Belum ada pembanding yang cukup.
            </p>

            @endif

        </div>


        {{-- Energy --}}
        <div class="mc-card p-5 col-span-2 md:col-span-1">

            <p class="text-sm mc-muted">
                Energy Hari Ini
            </p>

            @if($todayData['energy'])

            <h2 class="text-2xl sm:text-3xl font-semibold mt-2">

                {{ $todayData['energy'] }}

                <span class="text-base mc-faint">
                    / 5
                </span>

            </h2>

            <p class="text-xs mc-faint mt-2">

                Rata-rata:
                {{ $averageEnergy ?? '-' }}/5

            </p>

            @else

            <h2 class="text-xl font-semibold mt-3">
                Belum ada data
            </h2>

            <p class="text-xs mc-faint mt-2">
                Isi check-in hari ini untuk melihat pola.
            </p>

            @endif

        </div>


        {{-- Beban yang dirasakan --}}
        <div class="mc-card p-5 col-span-2 md:col-span-1">

            <p class="text-sm mc-muted">
                Beban yang Dirasakan
            </p>


            @if($todayData['workload'])

            @php

            $workloadLabel = match($todayData['workload']) {

            1 => 'Ringan',
            2 => 'Normal',
            3 => 'Berat',

            default => '-',

            };

            @endphp

            <h2 class="text-2xl sm:text-3xl font-semibold mt-2">

                {{ $workloadLabel }}

            </h2>

            <p class="text-xs mc-faint mt-2">
                Berdasarkan check-in hari ini
            </p>

            @else

            <h2 class="text-xl font-semibold mt-3">
                Belum ada data
            </h2>

            <p class="text-xs mc-faint mt-2">
                Belum ada check-in hari ini.
            </p>

            @endif

        </div>


        {{-- Recovery --}}
        <div class="mc-card p-5 col-span-2 md:col-span-1">

            <p class="text-sm mc-muted">
                Waktu Istirahat
            </p>

            <h2 class="text-2xl sm:text-3xl font-semibold mt-2">

                {{ $todayData['rest_minutes'] ?? 0 }}

                <span class="text-base mc-faint">
                    menit
                </span>

            </h2>

            <p class="text-xs mc-faint mt-2">
                Istirahat yang terjadwal hari ini
            </p>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- GRAFIK --}}
    {{-- ========================================================= --}}

    <div class="mc-card p-4 sm:p-6">


        {{-- HEADER GRAFIK --}}
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">

            <div>

                <h2 class="text-xl font-semibold">
                    {{ $chartTitle }}
                </h2>

                <p class="text-sm mc-muted mt-1">
                    {{ $chartDescription }}
                </p>

            </div>


            {{-- PILIH MODE --}}
            <div class="mc-seg self-start">

                <a
                    href="{{ route('insights', ['period' => '7d', 'offset' => 0]) }}"
                    class="mc-seg-item {{ $period === '7d' ? 'is-active' : '' }}">
                    7 Hari
                </a>

                <a
                    href="{{ route('insights', ['period' => 'monthly', 'offset' => 0]) }}"
                    class="mc-seg-item {{ $period === 'monthly' ? 'is-active' : '' }}">
                    Bulanan
                </a>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- NAVIGASI KALENDER --}}
        {{-- ===================================================== --}}

        <div class="flex items-center justify-between mt-8 mb-5">


            {{-- PREVIOUS --}}
            <a
                href="{{ route('insights', [
                    'period' => $period,
                    'offset' => $offset - 1
                ]) }}"
                class="mc-nav"
                title="Periode sebelumnya">
                ←
            </a>


            {{-- PERIOD LABEL --}}
            <div class="text-center">

                @if($period === '7d')

                <p class="text-sm font-semibold">

                    {{ \Carbon\Carbon::parse($chartData->first()['date_key'])->translatedFormat('d F') }}

                    —

                    {{ \Carbon\Carbon::parse($chartData->last()['date_key'])->translatedFormat('d F Y') }}

                </p>

                <p class="text-xs mc-faint mt-1">
                    7 hari
                </p>

                @else

                <p class="text-sm font-semibold">
                    {{ $chartData->first()['date'] }}
                </p>

                <p class="text-xs mc-faint mt-1">
                    Januari — Desember
                </p>

                @endif

            </div>


            {{-- NEXT --}}
            <a
                href="{{ route('insights', [
                    'period' => $period,
                    'offset' => $offset + 1
                ]) }}"
                class="mc-nav"
                title="Periode berikutnya">
                →
            </a>

        </div>


        {{-- ===================================================== --}}
        {{-- BAR CHART --}}
        {{-- ===================================================== --}}

        <div class="mt-2">

            @php

            $chartMax = collect($chartData)
            ->max('minutes');

            $chartMax = max(
            1,
            $chartMax ?? 0
            );

            @endphp


            {{-- CHART AREA --}}
            <div class="w-full overflow-x-auto">

                <div
                    class="
                        flex
                        items-end
                        justify-between
                        gap-3
                        h-72
                        px-2
                        sm:px-6
                        lg:px-10
                        min-w-full
                        {{ $period === 'monthly' ? 'min-w-[900px]' : 'min-w-[700px]' }}
                    ">

                    @foreach($chartData as $item)

                    @php

                    $minutes = $item['minutes'] ?? 0;

                    $barHeight = $minutes > 0
                    ? max(
                    10,
                    ($minutes / $chartMax) * 210
                    )
                    : 4;

                    @endphp


                    {{-- COLUMN --}}
                    <div class="flex flex-col items-center justify-end flex-1 min-w-0 h-full">

                        {{-- VALUE --}}
                        <div class="h-7 flex items-center justify-center mb-2 whitespace-nowrap">

                            @if($minutes > 0)

                            <span class="text-xs font-semibold" style="color: var(--mc-ink);">
                                {{ round($minutes / 60, 1) }} jam
                            </span>

                            @else

                            <span class="text-xs" style="color: #C9D6CF;">
                                0
                            </span>

                            @endif

                        </div>


                        {{-- BAR --}}
                        <div
                            class="mc-bar w-8 sm:w-10 lg:w-11 rounded-t-xl"
                            style="
                            height: {{ $barHeight }}px;
                        "
                            title="
                            {{ $item['full_date'] ?? $item['date'] ?? '' }}
                            —
                            {{ round($minutes / 60, 1) }} jam
                        ">
                        </div>


                        {{-- LABEL --}}
                        <p class="text-xs font-semibold mt-3 whitespace-nowrap" style="color: var(--mc-ink);">
                            {{ $item['label'] }}
                        </p>


                        {{-- DATE / YEAR --}}
                        <p class="text-[10px] mc-faint mt-1 whitespace-nowrap">

                            @if($period === '7d')

                            {{ $item['date'] }}

                            @else

                            {{ $item['date'] }}

                            @endif

                        </p>

                    </div>

                    @endforeach

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- INFO --}}
            {{-- ================================================= --}}

            <div
                class="
                    flex
                    flex-col
                    sm:flex-row
                    sm:items-center
                    sm:justify-between
                    gap-2
                    mt-6
                    pt-4
                    border-t
                "
                style="border-color: var(--mc-line);">

                @if($period === '7d')

                <p class="text-xs mc-faint">
                    ← → Geser 7 hari seperti kalender
                </p>

                <p class="text-xs mc-faint">
                    Hari tanpa aktivitas tetap ditampilkan sebagai 0.
                </p>

                @else

                <p class="text-xs mc-faint">
                    ← → Pindah tahun
                </p>

                <p class="text-xs mc-faint">
                    Nilai = rata-rata aktivitas per hari dalam bulan.
                </p>

                @endif

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- POLA YANG TERLIHAT --}}
    {{-- ========================================================= --}}

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">


        {{-- PATTERN --}}
        <div class="mc-card p-4 sm:p-6">

            <h2 class="text-xl font-semibold">
                Pola yang Terlihat
            </h2>

            <p class="text-sm mc-muted mt-1">
                Ringkasan berdasarkan data pribadimu.
            </p>


            <div class="mt-5 space-y-4">


                {{-- HEAVIEST DAY --}}
                @if($heaviestDay && $heaviestDay['total_minutes'] > 0)

                <div class="mc-panel">

                    <p class="text-xs uppercase tracking-wide mc-faint">
                        Hari dengan beban tertinggi
                    </p>

                    <p class="font-semibold mt-1">

                        {{ \Carbon\Carbon::parse($heaviestDay['date'])->translatedFormat('l, d F') }}

                    </p>

                    <p class="text-sm mc-muted mt-1">

                        Sekitar
                        {{ round($heaviestDay['total_minutes'] / 60, 1) }}
                        jam aktivitas.

                    </p>

                </div>

                @else

                <div class="mc-panel">

                    <p class="text-xs uppercase tracking-wide mc-faint">
                        Hari dengan beban tertinggi
                    </p>

                    <p class="font-semibold mt-1">
                        Belum ada data
                    </p>

                    <p class="text-sm mc-muted mt-1">
                        Belum cukup aktivitas untuk menentukan hari terberat.
                    </p>

                </div>

                @endif


                {{-- AVERAGE --}}
                <div class="mc-panel">

                    <p class="text-xs uppercase tracking-wide mc-faint">
                        Rata-rata beban
                    </p>

                    <p class="font-semibold mt-1">

                        {{ round(($averageMinutes ?? 0) / 60, 1) }}
                        jam per hari

                    </p>

                    <p class="text-sm mc-muted mt-1">

                        Berdasarkan aktivitas 7 hari sebelumnya.

                    </p>

                </div>


                {{-- CHECK-IN --}}
                <div class="mc-panel">

                    <p class="text-xs uppercase tracking-wide mc-faint">
                        Kelengkapan data
                    </p>

                    <p class="font-semibold mt-1">

                        {{ $checkinCount }}/7 hari
                        melakukan check-in

                    </p>

                    <p class="text-sm mc-muted mt-1">

                        Semakin konsisten check-in,
                        semakin jelas pola pribadi yang bisa terlihat.

                    </p>

                </div>

            </div>

        </div>


        {{-- TOP ACTIVITIES --}}
        <div class="mc-card p-4 sm:p-6">

            <h2 class="text-xl font-semibold">
                Aktivitas Paling Memakan Waktu
            </h2>

            <p class="text-sm mc-muted mt-1">
                Aktivitas dengan estimasi atau durasi terbesar.
            </p>


            <div class="mt-5 space-y-3">

                @forelse($topActivities as $activity)

                <div
                    class="mc-row flex items-center justify-between rounded-2xl border p-4"
                    style="border-color: var(--mc-line);">

                    <div class="min-w-0">

                        <p class="font-semibold truncate"
                           style="font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;">
                            {{ $activity['title'] }}
                        </p>

                        <p class="text-xs mc-muted mt-1">
                            {{ $activity['type'] }}
                        </p>

                    </div>


                    <div class="text-right ml-4 shrink-0">

                        <p class="font-semibold">
                            {{ $activity['minutes'] }} min
                        </p>

                        <p class="text-xs mc-muted">
                            {{ round($activity['minutes'] / 60, 1) }} jam
                        </p>

                    </div>

                </div>

                @empty

                <div
                    class="rounded-2xl border border-dashed p-8 text-center"
                    style="border-color: #C9D6CF;">

                    <p class="text-sm mc-muted">
                        Belum cukup aktivitas untuk dianalisis.
                    </p>

                </div>

                @endforelse

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- ACTIONABLE INSIGHT --}}
    {{-- ========================================================= --}}

    <div>

        <div class="mb-4">

            <h2 class="text-xl font-semibold">
                Yang Bisa Kamu Lakukan
            </h2>

            <p class="text-sm mc-muted mt-1">
                Insight tidak berhenti pada data.
                Berikut beberapa tindakan yang bisa kamu pertimbangkan.
            </p>

        </div>


        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            @forelse($insights as $insight)

            @php

            $boxStyle = match($insight['type']) {

            'warning' =>
            'background:#FDF3DE;border-color:#F0DDB0;',

            'positive' =>
            'background:var(--mc-soft);border-color:var(--mc-line);',

            'rest' =>
            'background:#EEF5F2;border-color:var(--mc-line);',

            default =>
            'background:#F5F9F6;border-color:var(--mc-line);',

            };

            @endphp


            <div class="rounded-[1.25rem] border p-5" style="{{ $boxStyle }}">

                <p class="text-xs font-semibold uppercase tracking-wide mc-faint">
                    Insight
                </p>


                <h3 class="font-semibold mt-2"
                    style="font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;">

                    {{ $insight['title'] }}

                </h3>


                <p class="text-sm mt-2 leading-6" style="color: #3F5A54;">

                    {{ $insight['message'] }}

                </p>


                <div class="mt-4 pt-4 border-t border-black/5">

                    <p class="text-xs font-semibold" style="color: var(--mc-primary);">
                        Coba lakukan
                    </p>

                    <p class="text-sm mt-1" style="color: var(--mc-ink);">
                        {{ $insight['action'] }}
                    </p>

                </div>

            </div>

            @empty

            <div
                class="lg:col-span-3 rounded-2xl border border-dashed p-8 text-center"
                style="border-color: #C9D6CF;">

                <p class="text-sm mc-muted">
                    Belum ada insight yang cukup untuk ditampilkan.
                </p>

            </div>

            @endforelse

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- NOTICE --}}
    {{-- ========================================================= --}}

    <div class="rounded-2xl px-5 py-4" style="background: var(--mc-soft);">

        <p class="text-xs leading-relaxed" style="color: var(--mc-primary);">

            <strong>
                Tentang Insight:
            </strong>

            Insight dibuat dari pola jadwal, task,
            estimasi waktu, waktu istirahat, dan check-in kamu.
            Insight ini membantu memahami pola aktivitas pribadi
            dan bukan merupakan diagnosis atau penilaian kondisi kesehatan.

        </p>

    </div>

</div>

@endsection