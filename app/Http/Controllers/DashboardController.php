<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Schedule;
use App\Models\Checkin;
use App\Models\Recommendation;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        // =========================================================
        // DATA HARI INI
        // =========================================================

        $todaySchedules = Schedule::where('user_id', $userId)
            ->where('start_time', '<', $tomorrow)
            ->where('end_time', '>', $today)
            ->orderBy('start_time')
            ->get();

        $todayTasks = Task::where('user_id', $userId)
            ->where('deadline', '>=', $today)
            ->where('deadline', '<', $tomorrow)
            ->orderBy('deadline')
            ->get();

        $todayCheckin = Checkin::where('user_id', $userId)
            ->whereDate('date', $today)
            ->first();


        // =========================================================
        // WAKTU TASK HARI INI
        // =========================================================

        $todayWorkMinutes = $todayTasks->sum(function ($task) {
            return (int) $task->estimated_duration;
        });


        // =========================================================
        // WAKTU JADWAL HARI INI
        // =========================================================

        $todayScheduleMinutes = $todaySchedules->sum(function ($schedule) {

            $start = Carbon::parse($schedule->start_time);
            $end = Carbon::parse($schedule->end_time);

            return max(
                0,
                $start->diffInMinutes($end)
            );
        });


        // =========================================================
        // TOTAL WAKTU
        // =========================================================

        $totalTodayMinutes =
            $todayWorkMinutes +
            $todayScheduleMinutes;


        // =========================================================
        // WORKLOAD
        // =========================================================

        if ($totalTodayMinutes >= 600) {

            $workloadStatus = 'Overloaded';

        } elseif ($totalTodayMinutes >= 420) {

            $workloadStatus = 'High';

        } elseif ($totalTodayMinutes >= 240) {

            $workloadStatus = 'Moderate';

        } else {

            $workloadStatus = 'Low';
        }


        // =========================================================
        // AGENDA HARI INI
        // =========================================================

        $todayAgenda = collect();


        // ---------------------------------------------------------
        // SCHEDULE
        // ---------------------------------------------------------

        foreach ($todaySchedules as $schedule) {

            $todayAgenda->push([
                'type' => 'schedule',

                'title' => $schedule->title,

                'start' => Carbon::parse($schedule->start_time),

                'end' => Carbon::parse($schedule->end_time),

                'category' => $schedule->type,

                'description' => $schedule->description,

                'priority' => null,
            ]);
        }


        // ---------------------------------------------------------
        // TASK
        // ---------------------------------------------------------

        foreach ($todayTasks as $task) {

            $deadline = Carbon::parse($task->deadline);

            $start = $deadline->copy()
                ->subMinutes((int) $task->estimated_duration);

            $todayAgenda->push([
                'type' => 'task',

                'title' => $task->title,

                'start' => $start,

                'end' => $deadline,

                'category' => $task->category,

                'description' => $task->description,

                'priority' => $task->priority,
            ]);
        }


        // Urutkan berdasarkan waktu mulai
        $todayAgenda = $todayAgenda
            ->sortBy('start')
            ->values();


        // =========================================================
        // UPCOMING TASK
        // =========================================================

        $upcomingTasks = Task::where('user_id', $userId)
            ->where('deadline', '>=', now())
            ->where('status', '!=', 'completed')
            ->orderBy('deadline')
            ->take(5)
            ->get();


        // =========================================================
        // REKOMENDASI
        // =========================================================

        $this->generateRecommendations(
            $userId,
            $todayWorkMinutes,
            $todayScheduleMinutes,
            $todayCheckin
        );


        $recommendations = Recommendation::where('user_id', $userId)
            ->where('status', 'pending')
            ->latest()
            ->take(3)
            ->get();


        // =========================================================
        // RETURN DASHBOARD
        // =========================================================

        return view('dashboard', [

            'todaySchedules' => $todaySchedules,

            'todayTasks' => $todayTasks,

            'todayWorkMinutes' => $todayWorkMinutes,

            'todayScheduleMinutes' => $todayScheduleMinutes,

            'totalTodayMinutes' => $totalTodayMinutes,

            'workloadStatus' => $workloadStatus,

            'todayCheckin' => $todayCheckin,

            'todayAgenda' => $todayAgenda,

            'upcomingTasks' => $upcomingTasks,

            'recommendations' => $recommendations,
        ]);
    }


    // =============================================================
    // GENERATE RECOMMENDATIONS
    // =============================================================

    private function generateRecommendations(
        $userId,
        $taskMinutes,
        $scheduleMinutes,
        $checkin
    ) {

        $totalMinutes =
            $taskMinutes +
            $scheduleMinutes;


        // =========================================================
        // HAPUS REKOMENDASI LAMA YANG MASIH PENDING
        // KECUALI RECOVERY DARI CHECK-IN
        // =========================================================

        Recommendation::where('user_id', $userId)
            ->where('status', 'pending')
            ->where('type', '!=', 'recovery')
            ->delete();


        // =========================================================
        // 1. BEBAN TINGGI
        // =========================================================

        if ($totalMinutes >= 420) {

            Recommendation::create([
                'user_id' => $userId,

                'type' => 'workload',

                'title' => 'Hari ini cukup padat',

                'message' =>
                    'Waktu aktivitasmu hari ini sudah cukup tinggi. Coba sisakan jeda sebelum berpindah ke aktivitas berikutnya.',

                'status' => 'pending',
            ]);
        }


        // =========================================================
        // 2. TASK BANYAK
        // =========================================================

        if ($taskMinutes >= 180) {

            Recommendation::create([
                'user_id' => $userId,

                'type' => 'deadline',

                'title' => 'Waktu pengerjaan task cukup besar',

                'message' =>
                    'Task hari ini membutuhkan sekitar ' .
                    round($taskMinutes / 60, 1) .
                    ' jam. Pertimbangkan mengerjakannya lebih awal agar tidak menumpuk mendekati deadline.',

                'status' => 'pending',
            ]);
        }


        // =========================================================
        // 3. JIKA TIDAK ADA REKOMENDASI
        // =========================================================

        if (
            $totalMinutes < 240 &&
            (!$checkin || $checkin->energy_level >= 3)
        ) {

            Recommendation::create([
                'user_id' => $userId,

                'type' => 'general',

                'title' => 'Jadwalmu cukup ringan',

                'message' =>
                    'Hari ini masih memiliki ruang waktu. Kamu bisa menggunakan sebagian waktu tersebut untuk menyelesaikan task lebih awal atau beristirahat.',

                'status' => 'pending',
            ]);
        }
    }
}