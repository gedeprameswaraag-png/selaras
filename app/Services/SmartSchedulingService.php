<?php

namespace App\Services;

use App\Models\Schedule;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SmartSchedulingService
{
    /**
     * Maksimal satu slot yang ditampilkan.
     */
    private int $maxSlotMinutes = 120;

    /**
     * Jam mulai aktivitas yang direkomendasikan.
     */
    private int $activeStartHour = 5;

    /**
     * Jam akhir aktivitas yang direkomendasikan.
     *
     * Setelah pukul 22.00 tidak direkomendasikan
     * untuk pengerjaan task.
     */
    private int $activeEndHour = 22;

    /**
     * Mencari seluruh waktu kosong sebelum deadline.
     *
     * Catatan:
     * - Sistem tetap menghitung waktu mulai dari sekarang.
     * - Sistem tidak membuat rekomendasi aktivitas
     *   pada jam 22.00 - 05.00.
     */
    public function findAvailableSlots(Task $task): Collection
    {
        $now = Carbon::now();
        $deadline = Carbon::parse($task->deadline);

        if ($deadline->lessThanOrEqualTo($now)) {
            return collect();
        }

        $schedules = Schedule::where('user_id', $task->user_id)
            ->where('start_time', '<', $deadline)
            ->where('end_time', '>', $now)
            ->orderBy('start_time')
            ->get();

        $slots = collect();

        $currentDate = $now->copy()->startOfDay();

        while ($currentDate->lessThanOrEqualTo($deadline)) {

            /*
             * Batas awal aktivitas:
             * normalnya pukul 05.00.
             */
            $dayStart = $currentDate->copy()
                ->startOfDay()
                ->setTime($this->activeStartHour, 0, 0);

            /*
             * Batas akhir aktivitas:
             * pukul 22.00.
             */
            $dayEnd = $currentDate->copy()
                ->startOfDay()
                ->setTime($this->activeEndHour, 0, 0);

            /*
             * Kalau hari ini, jangan mencari slot sebelum sekarang.
             */
            if ($currentDate->isSameDay($now)) {
                if ($now->greaterThanOrEqualTo($dayEnd)) {
                    $currentDate->addDay();
                    continue;
                }

                if ($now->greaterThan($dayStart)) {
                    $dayStart = $now->copy();
                }
            }

            /*
             * Kalau hari deadline, jangan melewati deadline.
             */
            if ($currentDate->isSameDay($deadline)) {
                if ($deadline->lessThanOrEqualTo($dayStart)) {
                    break;
                }

                if ($deadline->lessThan($dayEnd)) {
                    $dayEnd = $deadline->copy();
                }
            }

            if ($dayStart->lessThan($dayEnd)) {

                /*
                 * Ambil jadwal yang beririsan dengan hari ini.
                 */
                $daySchedules = $schedules->filter(
                    function ($schedule) use ($currentDate) {

                        $scheduleStart = Carbon::parse(
                            $schedule->start_time
                        );

                        $scheduleEnd = Carbon::parse(
                            $schedule->end_time
                        );

                        return $scheduleStart->lessThan(
                            $currentDate->copy()->endOfDay()
                        )
                        &&
                        $scheduleEnd->greaterThan(
                            $currentDate->copy()->startOfDay()
                        );
                    }
                )->sortBy('start_time');

                $current = $dayStart->copy();

                foreach ($daySchedules as $schedule) {

                    $scheduleStart = Carbon::parse(
                        $schedule->start_time
                    );

                    $scheduleEnd = Carbon::parse(
                        $schedule->end_time
                    );

                    /*
                     * Jadwal selesai sebelum batas aktivitas.
                     */
                    if ($scheduleEnd->lessThanOrEqualTo($dayStart)) {
                        continue;
                    }

                    /*
                     * Jadwal dimulai setelah batas aktivitas.
                     */
                    if ($scheduleStart->greaterThanOrEqualTo($dayEnd)) {
                        continue;
                    }

                    /*
                     * Jangan mulai slot sebelum batas aktivitas.
                     */
                    if ($scheduleStart->lessThan($dayStart)) {
                        $scheduleStart = $dayStart->copy();
                    }

                    /*
                     * Jangan melewati batas aktivitas.
                     */
                    if ($scheduleEnd->greaterThan($dayEnd)) {
                        $scheduleEnd = $dayEnd->copy();
                    }

                    /*
                     * Ada waktu kosong sebelum jadwal.
                     */
                    if ($current->lessThan($scheduleStart)) {

                        $this->addSlot(
                            $slots,
                            $current,
                            $scheduleStart
                        );
                    }

                    /*
                     * Geser pointer ke akhir jadwal.
                     */
                    if ($scheduleEnd->greaterThan($current)) {
                        $current = $scheduleEnd->copy();
                    }
                }

                /*
                 * Masih ada waktu kosong setelah semua jadwal.
                 */
                if ($current->lessThan($dayEnd)) {

                    $this->addSlot(
                        $slots,
                        $current,
                        $dayEnd
                    );
                }
            }

            $currentDate->addDay();
        }

        return $slots;
    }

    /**
     * Memecah waktu kosong menjadi beberapa slot.
     */
    private function addSlot(
        Collection $slots,
        Carbon $start,
        Carbon $end
    ): void {

        /*
         * Pastikan slot tidak pernah berada
         * di luar jam aktivitas.
         */
        $activeStart = $start->copy()
            ->startOfDay()
            ->setTime($this->activeStartHour, 0, 0);

        $activeEnd = $start->copy()
            ->startOfDay()
            ->setTime($this->activeEndHour, 0, 0);

        if ($start->lessThan($activeStart)) {
            $start = $activeStart->copy();
        }

        if ($end->greaterThan($activeEnd)) {
            $end = $activeEnd->copy();
        }

        if ($start->greaterThanOrEqualTo($end)) {
            return;
        }

        $current = $start->copy();

        while ($current->lessThan($end)) {

            $slotEnd = $current->copy()
                ->addMinutes($this->maxSlotMinutes);

            if ($slotEnd->greaterThan($end)) {
                $slotEnd = $end->copy();
            }

            /*
             * Jangan sampai slot melewati 22.00.
             */
            if ($slotEnd->hour > $this->activeEndHour) {
                $slotEnd = $current->copy()
                    ->startOfDay()
                    ->setTime($this->activeEndHour, 0, 0);
            }

            $minutes = $current->diffInMinutes($slotEnd);

            if ($minutes > 0) {

                $slots->push([
                    'start' => $current->copy(),
                    'end' => $slotEnd->copy(),
                    'minutes' => $minutes,
                ]);
            }

            $current = $slotEnd->copy();

            /*
             * Pengaman agar tidak terjadi loop
             * jika batas waktu sudah tercapai.
             */
            if ($current->greaterThanOrEqualTo($end)) {
                break;
            }
        }
    }

    /**
     * Menghitung total waktu yang tersedia.
     */
    public function calculateAvailableMinutes(Task $task): int
    {
        return $this->findAvailableSlots($task)
            ->sum('minutes');
    }

    /**
     * Mengecek apakah task masih bisa dikerjakan
     * sebelum deadline.
     */
    public function checkTaskFeasibility(Task $task): array
    {
        $requiredMinutes = (int) $task->estimated_duration;

        $availableMinutes = $this->calculateAvailableMinutes(
            $task
        );

        return [
            'required_minutes' => $requiredMinutes,

            'available_minutes' => $availableMinutes,

            'is_feasible' =>
                $availableMinutes >= $requiredMinutes,

            'shortage_minutes' =>
                max(
                    0,
                    $requiredMinutes - $availableMinutes
                ),
        ];
    }

    /**
     * Mencari slot yang cukup untuk menyelesaikan
     * task sekaligus.
     */
    public function findSchedulableSlots(Task $task): Collection
    {
        $requiredMinutes = (int) $task->estimated_duration;

        if ($requiredMinutes <= 0) {
            return collect();
        }

        $availableSlots = $this->findAvailableSlots($task);

        return $availableSlots
            ->filter(function ($slot) use ($requiredMinutes) {

                return $slot['minutes'] >= $requiredMinutes;

            })
            ->map(function ($slot) use ($requiredMinutes) {

                $start = $slot['start']->copy();

                $end = $start->copy()
                    ->addMinutes($requiredMinutes);

                /*
                 * Pengaman tambahan:
                 * rekomendasi tidak boleh melewati 22.00.
                 */
                $activeEnd = $start->copy()
                    ->startOfDay()
                    ->setTime($this->activeEndHour, 0, 0);

                if ($end->greaterThan($activeEnd)) {
                    return null;
                }

                return [
                    'start' => $start,

                    'end' => $end,

                    'minutes' => $requiredMinutes,
                ];
            })
            ->filter()
            ->values();
    }
}