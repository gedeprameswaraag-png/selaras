<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Schedule;
use App\Models\Checkin;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InsightController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        /*
        |--------------------------------------------------------------------------
        | MODE
        |--------------------------------------------------------------------------
        */

        $period = $request->get('period', '7d');

        if (!in_array($period, ['7d', 'monthly'])) {
            $period = '7d';
        }


        /*
        |--------------------------------------------------------------------------
        | OFFSET
        |--------------------------------------------------------------------------
        |
        | 7d:
        |   0  = periode sekarang
        |  -1  = 7 hari sebelumnya
        |   1  = 7 hari berikutnya
        |
        | monthly:
        |   0  = tahun sekarang
        |  -1  = tahun sebelumnya
        |   1  = tahun berikutnya
        |
        */

        $offset = (int) $request->get('offset', 0);


        /*
        |--------------------------------------------------------------------------
        | TANGGAL SEKARANG
        |--------------------------------------------------------------------------
        */

        $today = Carbon::today();


        /*
        |--------------------------------------------------------------------------
        | DATA DASAR
        |--------------------------------------------------------------------------
        */

        $tasks = Task::where('user_id', $userId)
            ->get();

        $schedules = Schedule::where('user_id', $userId)
            ->get();

        $checkins = Checkin::where('user_id', $userId)
            ->get()
            ->keyBy(function ($checkin) {
                return Carbon::parse($checkin->date)->format('Y-m-d');
            });


        /*
        |--------------------------------------------------------------------------
        | HELPER DURASI SCHEDULE
        |--------------------------------------------------------------------------
        */

        $scheduleMinutes = function ($schedule) {

            $start = Carbon::parse($schedule->start_time);
            $end = Carbon::parse($schedule->end_time);

            return max(
                0,
                $start->diffInMinutes($end)
            );
        };


        /*
        |--------------------------------------------------------------------------
        | DATA 7 HARI
        |--------------------------------------------------------------------------
        |
        | Kalender selalu memiliki 7 tanggal.
        | Data kosong = 0.
        |
        */

        $currentWeekStart = $today
            ->copy()
            ->subDays(6)
            ->startOfDay();

        $periodStart = $currentWeekStart
            ->copy()
            ->addDays($offset * 7);

        $periodEnd = $periodStart
            ->copy()
            ->addDays(6)
            ->endOfDay();


        /*
        |--------------------------------------------------------------------------
        | CHART DATA
        |--------------------------------------------------------------------------
        */

        $chartData = collect();


        if ($period === '7d') {

            for ($i = 0; $i < 7; $i++) {

                $date = $periodStart
                    ->copy()
                    ->addDays($i);

                $dateKey = $date->format('Y-m-d');

                $taskMinutes = 0;
                $scheduleMinutesTotal = 0;


                /*
                | TASK
                */

                foreach ($tasks as $task) {

                    $deadline = Carbon::parse($task->deadline);

                    if ($deadline->format('Y-m-d') === $dateKey) {

                        $taskMinutes += (int) $task->estimated_duration;
                    }
                }


                /*
                | SCHEDULE
                */

                foreach ($schedules as $schedule) {

                    $start = Carbon::parse($schedule->start_time);

                    if ($start->format('Y-m-d') === $dateKey) {

                        $scheduleMinutesTotal +=
                            $scheduleMinutes($schedule);
                    }
                }


                $totalMinutes =
                    $taskMinutes +
                    $scheduleMinutesTotal;


                $chartData->push([

                    'date_key' => $dateKey,

                    'date' => $date->format('d M'),

                    'label' => $date->format('D'),

                    'full_date' => $date->translatedFormat('l, d F Y'),

                    'minutes' => $totalMinutes,

                    'task_minutes' => $taskMinutes,

                    'schedule_minutes' => $scheduleMinutesTotal,

                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | DATA BULANAN
        |--------------------------------------------------------------------------
        |
        | Menampilkan Januari - Desember.
        |
        | Nilai:
        | total aktivitas dalam bulan
        | --------------------------------
        | jumlah hari dalam bulan
        |
        | Jadi hari kosong tetap dihitung sebagai 0.
        |
        */

        if ($period === 'monthly') {

            $targetYear = $today
                ->copy()
                ->addYears($offset)
                ->year;


            $monthNames = [

                1 => 'Jan',
                2 => 'Feb',
                3 => 'Mar',
                4 => 'Apr',
                5 => 'Mei',
                6 => 'Jun',
                7 => 'Jul',
                8 => 'Agu',
                9 => 'Sep',
                10 => 'Okt',
                11 => 'Nov',
                12 => 'Des',

            ];


            for ($month = 1; $month <= 12; $month++) {

                $monthStart = Carbon::create(
                    $targetYear,
                    $month,
                    1
                )->startOfDay();

                $monthEnd = $monthStart
                    ->copy()
                    ->endOfMonth()
                    ->endOfDay();


                $totalMinutes = 0;


                /*
                | TASK
                */

                foreach ($tasks as $task) {

                    $deadline = Carbon::parse($task->deadline);

                    if (
                        $deadline->between(
                            $monthStart,
                            $monthEnd
                        )
                    ) {

                        $totalMinutes +=
                            (int) $task->estimated_duration;
                    }
                }


                /*
                | SCHEDULE
                */

                foreach ($schedules as $schedule) {

                    $start = Carbon::parse($schedule->start_time);

                    if (
                        $start->between(
                            $monthStart,
                            $monthEnd
                        )
                    ) {

                        $totalMinutes +=
                            $scheduleMinutes($schedule);
                    }
                }


                /*
                | RATA-RATA PER HARI
                |
                | Semua tanggal dalam bulan dihitung.
                | Jadi bulan tanpa aktivitas tetap muncul 0.
                */

                $daysInMonth =
                    $monthStart->daysInMonth;

                $averageMinutes =
                    $daysInMonth > 0
                        ? $totalMinutes / $daysInMonth
                        : 0;


                $chartData->push([

                    'month' => $month,

                    'date' => $targetYear,

                    'label' => $monthNames[$month],

                    'full_date' =>
                        $monthNames[$month] . ' ' . $targetYear,

                    'minutes' =>
                        round($averageMinutes),

                    'total_minutes' =>
                        $totalMinutes,

                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | DATA HARI INI
        |--------------------------------------------------------------------------
        */

        $todayKey = $today->format('Y-m-d');

        $todayData = collect($chartData)
            ->firstWhere('date_key', $todayKey);


        /*
        | Kalau mode monthly, tetap hitung hari ini
        | secara terpisah.
        */

        if (!$todayData) {

            $todayTaskMinutes = 0;
            $todayScheduleMinutes = 0;


            foreach ($tasks as $task) {

                $deadline = Carbon::parse($task->deadline);

                if ($deadline->format('Y-m-d') === $todayKey) {

                    $todayTaskMinutes +=
                        (int) $task->estimated_duration;
                }
            }


            foreach ($schedules as $schedule) {

                $start = Carbon::parse($schedule->start_time);

                if ($start->format('Y-m-d') === $todayKey) {

                    $todayScheduleMinutes +=
                        $scheduleMinutes($schedule);
                }
            }


            $todayData = [

                'date_key' => $todayKey,

                'date' => $today->format('d M'),

                'label' => $today->format('D'),

                'full_date' =>
                    $today->translatedFormat('l, d F Y'),

                'minutes' =>
                    $todayTaskMinutes +
                    $todayScheduleMinutes,

                'task_minutes' =>
                    $todayTaskMinutes,

                'schedule_minutes' =>
                    $todayScheduleMinutes,

            ];
        }


        /*
        |--------------------------------------------------------------------------
        | CHECK-IN HARI INI
        |--------------------------------------------------------------------------
        */

        $todayCheckin = $checkins->get($todayKey);


        $todayEnergy =
            $todayCheckin?->energy_level;


        $todayWorkload = null;


        if ($todayCheckin) {

            $todayWorkload = match (
                $todayCheckin->workload_level
            ) {

                'light' => 1,

                'normal' => 2,

                'heavy' => 3,

                default => null,

            };
        }


        /*
        |--------------------------------------------------------------------------
        | REST HARI INI
        |--------------------------------------------------------------------------
        */

        $todayRestMinutes = 0;


        foreach ($schedules as $schedule) {

            $start = Carbon::parse($schedule->start_time);

            if (
                $start->format('Y-m-d') === $todayKey &&
                $schedule->type === 'rest'
            ) {

                $todayRestMinutes +=
                    $scheduleMinutes($schedule);
            }
        }


        $todayData['total_minutes'] =
            $todayData['minutes'] ?? 0;

        $todayData['energy'] =
            $todayEnergy;

        $todayData['workload'] =
            $todayWorkload;

        $todayData['rest_minutes'] =
            $todayRestMinutes;


        /*
        |--------------------------------------------------------------------------
        | BASELINE 7 HARI SEBELUM HARI INI
        |--------------------------------------------------------------------------
        */

        $baselineDays = collect();

        for ($i = 1; $i <= 7; $i++) {

            $date = $today
                ->copy()
                ->subDays($i);

            $key = $date->format('Y-m-d');

            $taskMinutes = 0;
            $scheduleTotal = 0;


            foreach ($tasks as $task) {

                $deadline = Carbon::parse($task->deadline);

                if ($deadline->format('Y-m-d') === $key) {

                    $taskMinutes +=
                        (int) $task->estimated_duration;
                }
            }


            foreach ($schedules as $schedule) {

                $start = Carbon::parse($schedule->start_time);

                if ($start->format('Y-m-d') === $key) {

                    $scheduleTotal +=
                        $scheduleMinutes($schedule);
                }
            }


            $total =
                $taskMinutes +
                $scheduleTotal;


            $baselineDays->push([

                'date' => $key,

                'total_minutes' => $total,

                'energy' =>
                    $checkins->get($key)?->energy_level,

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | AVERAGE
        |--------------------------------------------------------------------------
        */

        $averageMinutes =
            $baselineDays->avg('total_minutes') ?? 0;


        $energyValues = $baselineDays
            ->whereNotNull('energy')
            ->pluck('energy');


        $averageEnergy =
            $energyValues->count() > 0
                ? round($energyValues->avg(), 1)
                : null;


        /*
        |--------------------------------------------------------------------------
        | PERBEDAAN BEBAN
        |--------------------------------------------------------------------------
        */

        $loadDifference = null;


        if ($averageMinutes > 0) {

            $loadDifference = round(
                (
                    (
                        $todayData['total_minutes'] -
                        $averageMinutes
                    )
                    /
                    $averageMinutes
                )
                * 100
            );
        }


        /*
        |--------------------------------------------------------------------------
        | HARI TERBERAT 7 HARI TERAKHIR
        |--------------------------------------------------------------------------
        */

        $heaviestDay = $baselineDays
            ->sortByDesc('total_minutes')
            ->first();


        if (!$heaviestDay) {

            $heaviestDay = null;

        } else {

            $heaviestDay = [

                'date' =>
                    $heaviestDay['date'],

                'total_minutes' =>
                    $heaviestDay['total_minutes'],

            ];
        }


        /*
        |--------------------------------------------------------------------------
        | TOP ACTIVITIES
        |--------------------------------------------------------------------------
        */

        $topActivities = collect();


        foreach ($tasks as $task) {

            $topActivities->push([

                'title' =>
                    $task->title,

                'type' =>
                    'Task',

                'minutes' =>
                    (int) $task->estimated_duration,

            ]);
        }


        foreach ($schedules as $schedule) {

            $topActivities->push([

                'title' =>
                    $schedule->title,

                'type' =>
                    'Jadwal',

                'minutes' =>
                    $scheduleMinutes($schedule),

            ]);
        }


        $topActivities =
            $topActivities
                ->sortByDesc('minutes')
                ->take(5)
                ->values();


        /*
        |--------------------------------------------------------------------------
        | CHECK-IN COUNT
        |--------------------------------------------------------------------------
        */

        $checkinCount = 0;

        for ($i = 0; $i < 7; $i++) {

            $date = $today
                ->copy()
                ->subDays($i)
                ->format('Y-m-d');

            if ($checkins->has($date)) {

                $checkinCount++;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | INSIGHTS
        |--------------------------------------------------------------------------
        */

        $insights = collect();


        if ($loadDifference !== null && $loadDifference >= 20) {

            $insights->push([

                'type' => 'warning',

                'title' =>
                    'Aktivitas hari ini lebih padat',

                'message' =>
                    'Beban aktivitas hari ini lebih tinggi dibanding rata-rata 7 hari sebelumnya.',

                'action' =>
                    'Coba lihat kembali task yang belum mendesak dan beri ruang untuk istirahat.',

            ]);

        } elseif (
            $loadDifference !== null &&
            $loadDifference <= -20
        ) {

            $insights->push([

                'type' => 'positive',

                'title' =>
                    'Hari ini relatif lebih ringan',

                'message' =>
                    'Total aktivitas hari ini berada di bawah rata-rata 7 hari sebelumnya.',

                'action' =>
                    'Gunakan ruang waktu ini untuk menyelesaikan task penting atau memulihkan energi.',

            ]);

        } else {

            $insights->push([

                'type' => 'default',

                'title' =>
                    'Beban relatif stabil',

                'message' =>
                    'Belum terlihat perbedaan besar antara aktivitas hari ini dan rata-rata beberapa hari sebelumnya.',

                'action' =>
                    'Pertahankan pembagian waktu yang sekarang dan terus lakukan check-in.',

            ]);
        }


        if (
            $todayEnergy !== null &&
            $averageEnergy !== null &&
            $todayEnergy < $averageEnergy
        ) {

            $insights->push([

                'type' => 'warning',

                'title' =>
                    'Energy hari ini lebih rendah',

                'message' =>
                    'Energy yang kamu catat hari ini berada di bawah rata-rata check-in sebelumnya.',

                'action' =>
                    'Pertimbangkan mengurangi aktivitas tambahan yang tidak mendesak.',

            ]);

        }


        if ($todayRestMinutes > 0) {

            $insights->push([

                'type' => 'rest',

                'title' =>
                    'Ada waktu istirahat',

                'message' =>
                    'Hari ini kamu memiliki waktu istirahat yang sudah dijadwalkan.',

                'action' =>
                    'Usahakan waktu tersebut benar-benar digunakan untuk beristirahat.',

            ]);

        } else {

            $insights->push([

                'type' => 'rest',

                'title' =>
                    'Belum ada istirahat terjadwal',

                'message' =>
                    'Belum terdapat jadwal dengan tipe istirahat untuk hari ini.',

                'action' =>
                    'Sisihkan waktu istirahat sebelum aktivitas berikutnya menumpuk.',

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | JUDUL CHART
        |--------------------------------------------------------------------------
        */

        if ($period === '7d') {

            $chartTitle =
                'Beban aktivitas — 7 hari';

            $chartDescription =
                $periodStart->translatedFormat('d F Y')
                . ' — '
                . $periodEnd->translatedFormat('d F Y');

        } else {

            $targetYear = $today
                ->copy()
                ->addYears($offset)
                ->year;

            $chartTitle =
                'Rata-rata aktivitas — ' . $targetYear;

            $chartDescription =
                'Rata-rata aktivitas per hari untuk setiap bulan.';

        }


        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */

        return view('insights.index', [

            'period' =>
                $period,

            'offset' =>
                $offset,

            'chartData' =>
                $chartData,

            'chartTitle' =>
                $chartTitle,

            'chartDescription' =>
                $chartDescription,

            'todayData' =>
                $todayData,

            'averageMinutes' =>
                $averageMinutes,

            'averageEnergy' =>
                $averageEnergy,

            'loadDifference' =>
                $loadDifference,

            'heaviestDay' =>
                $heaviestDay,

            'topActivities' =>
                $topActivities,

            'checkinCount' =>
                $checkinCount,

            'insights' =>
                $insights,

        ]);
    }
}