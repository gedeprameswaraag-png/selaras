<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Task;
use App\Models\Schedule;
use App\Models\Checkin;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil user pertama yang sudah terdaftar
        $user = User::first();

        if (!$user) {
            $this->command->error('Belum ada user. Silakan register terlebih dahulu.');
            return;
        }

        $this->command->info("Membuat dummy data untuk: {$user->email}");

        /*
        |--------------------------------------------------------------------------
        | Hapus dummy data lama
        |--------------------------------------------------------------------------
        */

        Task::where('user_id', $user->id)->delete();
        Schedule::where('user_id', $user->id)->delete();
        Checkin::where('user_id', $user->id)->delete();

        /*
        |--------------------------------------------------------------------------
        | Senin - 22 September
        |--------------------------------------------------------------------------
        */

        $this->createDayData(
            $user,
            '2026-09-22',
            energy: 4,
            workload: 'normal',
            tasks: [
                ['Mengerjakan laporan RPL', 180, 'high', 'academic'],
            ],
            schedules: [
                ['Kuliah Pemrograman Web', '08:00', '10:00', 'academic'],
                ['Kuliah Rekayasa Perangkat Lunak', '13:00', '15:00', 'academic'],
                ['Rapat PMK', '19:00', '20:00', 'organization'],
                ['Istirahat', '21:00', '22:00', 'rest'],
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Selasa - 23 September
        |--------------------------------------------------------------------------
        */

        $this->createDayData(
            $user,
            '2026-09-23',
            energy: 3,
            workload: 'heavy',
            tasks: [
                ['Menyelesaikan tugas Pemrograman Web', 180, 'high', 'academic'],
                ['Membuat desain UI', 60, 'medium', 'academic'],
            ],
            schedules: [
                ['Kuliah Struktur Data', '08:00', '10:00', 'academic'],
                ['Praktikum Pemrograman Web', '13:00', '15:00', 'academic'],
                ['Rapat Organisasi', '18:30', '20:00', 'organization'],
                ['Belajar mandiri', '20:30', '22:30', 'academic'],
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Rabu - 24 September
        |--------------------------------------------------------------------------
        */

        $this->createDayData(
            $user,
            '2026-09-24',
            energy: 4,
            workload: 'normal',
            tasks: [
                ['Review materi JavaScript', 90, 'medium', 'academic'],
                ['Merapikan dokumentasi project', 30, 'low', 'academic'],
            ],
            schedules: [
                ['Kuliah Basis Data', '09:00', '11:00', 'academic'],
                ['Mengerjakan project MindCraft', '13:00', '15:00', 'academic'],
                ['Waktu pribadi', '17:00', '18:00', 'personal'],
                ['Istirahat', '21:00', '23:00', 'rest'],
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Kamis - 25 September
        |--------------------------------------------------------------------------
        */

        $this->createDayData(
            $user,
            '2026-09-25',
            energy: 2,
            workload: 'heavy',
            tasks: [
                ['Deadline tugas ASD', 180, 'high', 'academic'],
                ['Menyelesaikan laporan kelompok', 120, 'high', 'academic'],
                ['Persiapan mengajar Pemrograman Web', 60, 'medium', 'academic'],
            ],
            schedules: [
                ['Kuliah Algoritma', '08:00', '10:00', 'academic'],
                ['Asisten Dosen Pemrograman Web', '13:00', '15:00', 'organization'],
                ['Rapat kelompok', '16:00', '18:00', 'academic'],
                ['Mengerjakan tugas', '19:00', '22:00', 'academic'],
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Jumat - 26 September
        |--------------------------------------------------------------------------
        */

        $this->createDayData(
            $user,
            '2026-09-26',
            energy: 3,
            workload: 'normal',
            tasks: [
                ['Menyelesaikan revisi project', 120, 'medium', 'academic'],
                ['Membaca materi kuliah', 60, 'low', 'academic'],
            ],
            schedules: [
                ['Kuliah Interaksi Manusia dan Komputer', '08:00', '10:00', 'academic'],
                ['Mengerjakan project', '13:00', '15:00', 'academic'],
                ['Ibadah PU', '19:00', '21:00', 'organization'],
                ['Istirahat', '21:30', '23:00', 'rest'],
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Sabtu - 27 September
        |--------------------------------------------------------------------------
        */

        $this->createDayData(
            $user,
            '2026-09-27',
            energy: 4,
            workload: 'light',
            tasks: [
                ['Belajar mandiri Laravel', 120, 'medium', 'academic'],
                ['Merapikan GitHub', 60, 'low', 'personal'],
            ],
            schedules: [
                ['Belajar Laravel', '09:00', '11:00', 'academic'],
                ['Olahraga', '16:00', '17:00', 'personal'],
                ['Waktu santai', '19:00', '21:00', 'rest'],
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Minggu - 28 September
        |--------------------------------------------------------------------------
        */

        $this->createDayData(
            $user,
            '2026-09-28',
            energy: 5,
            workload: 'light',
            tasks: [
                ['Review rencana minggu depan', 60, 'low', 'personal'],
            ],
            schedules: [
                ['Ibadah', '09:00', '11:00', 'personal'],
                ['Waktu keluarga', '13:00', '15:00', 'personal'],
                ['Istirahat', '16:00', '21:00', 'rest'],
            ]
        );

        $this->command->info('Dummy data berhasil dibuat!');
    }

    private function createDayData(
        User $user,
        string $date,
        int $energy,
        string $workload,
        array $tasks,
        array $schedules
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Check-in
        |--------------------------------------------------------------------------
        */

        Checkin::create([
            'user_id' => $user->id,
            'date' => $date,
            'energy_level' => $energy,
            'workload_level' => $workload,
            'note' => $this->getNote($energy, $workload),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Tasks
        |--------------------------------------------------------------------------
        */

        foreach ($tasks as $task) {

            [$title, $duration, $priority, $category] = $task;

            $deadline = Carbon::parse($date)
                ->setTime(23, 0);

            Task::create([
                'user_id' => $user->id,
                'title' => $title,
                'description' => 'Dummy data untuk pengujian MindCraft.',
                'deadline' => $deadline,
                'estimated_duration' => $duration,
                'priority' => $priority,
                'status' => 'completed',
                'category' => $category,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Schedules
        |--------------------------------------------------------------------------
        */

        foreach ($schedules as $schedule) {

            [$title, $start, $end, $type] = $schedule;

            Schedule::create([
                'user_id' => $user->id,
                'title' => $title,
                'start_time' => Carbon::parse("$date $start"),
                'end_time' => Carbon::parse("$date $end"),
                'type' => $type,
                'is_fixed' => $type !== 'rest',
                'description' => 'Dummy data untuk pengujian MindCraft.',
            ]);
        }
    }

    private function getNote(int $energy, string $workload): string
    {
        if ($energy <= 2) {
            return 'Hari terasa cukup padat dan energi mulai menurun.';
        }

        if ($workload === 'heavy') {
            return 'Ada cukup banyak aktivitas dan pekerjaan hari ini.';
        }

        if ($energy >= 4) {
            return 'Energi cukup baik hari ini.';
        }

        return 'Hari berjalan cukup normal.';
    }
}