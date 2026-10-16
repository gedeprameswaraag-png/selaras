<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    // ============================================================
    // INDEX
    // ============================================================

    public function index()
    {
        $schedules = Schedule::where('user_id', auth()->id())
            ->orderBy('start_time')
            ->get();

        $tasks = Task::where('user_id', auth()->id())
            ->orderBy('deadline')
            ->get();

        $calendarEvents = collect();

        // =========================
        // SCHEDULE EVENTS
        // =========================

        foreach ($schedules as $schedule) {

            $calendarEvents->push([
                'id' => 'schedule-' . $schedule->id,

                'title' => $schedule->title,

                'start' => $schedule->start_time,
                'end' => $schedule->end_time,

                'type' => $schedule->type,
                'description' => $schedule->description,
                'is_fixed' => $schedule->is_fixed,

                // Informasi recurring
                'is_recurring' => $schedule->is_recurring,
                'recurrence_id' => $schedule->recurrence_id,
                'recurrence_type' => $schedule->recurrence_type,
                'recurrence_start_date' => $schedule->recurrence_start_date,
                'recurrence_end_date' => $schedule->recurrence_end_date,

                'event_type' => 'schedule',
            ]);
        }

        // =========================
        // TASK EVENTS
        // =========================

        foreach ($tasks as $task) {

            $calendarEvents->push([
                'id' => 'task-' . $task->id,

                'title' => '📝 ' . $task->title,

                // Task ditampilkan pada deadline
                'start' => $task->deadline,

                'type' => $task->category,
                'description' => $task->description,

                'priority' => $task->priority,
                'status' => $task->status,
                'estimated_duration' => $task->estimated_duration,

                'event_type' => 'task',
            ]);
        }

        return view(
            'calendar.index',
            compact(
                'schedules',
                'tasks',
                'calendarEvents'
            )
        );
    }


    // ============================================================
    // CREATE
    // ============================================================

    public function create()
    {
        return view('calendar.create');
    }


    // ============================================================
    // STORE
    // ============================================================

    public function store(Request $request)
    {
        // ========================================================
        // VALIDASI DASAR
        // ========================================================

        $request->validate([
            'title' => 'required|string|max:255',

            'type' => [
                'required',
                'in:academic,organization,personal,rest,other'
            ],

            'is_fixed' => 'nullable|boolean',

            'description' => 'nullable|string',

            'is_recurring' => 'nullable|boolean',
        ]);


        // ========================================================
        // JADWAL BERULANG
        // ========================================================

        if ($request->boolean('is_recurring')) {

            $request->validate([

                'start_date' => [
                    'required',
                    'date',
                ],

                'recurrence_end_date' => [
                    'required',
                    'date',
                    'after_or_equal:start_date',
                ],

                'recurring_start_time' => [
                    'required',
                    'date_format:H:i',
                ],

                'recurring_end_time' => [
                    'required',
                    'date_format:H:i',
                ],

                'recurrence_type' => [
                    'required',
                    'in:weekly',
                ],

                'recurrence_days' => [
                    'required',
                    'array',
                    'min:1',
                ],

                'recurrence_days.*' => [
                    'integer',
                    'min:0',
                    'max:6',
                ],
            ]);


            // ====================================================
            // VALIDASI JAM
            // ====================================================

            $startTime = Carbon::createFromFormat(
                'H:i',
                $request->recurring_start_time
            );

            $endTime = Carbon::createFromFormat(
                'H:i',
                $request->recurring_end_time
            );


            if ($endTime->lessThanOrEqualTo($startTime)) {

                return back()
                    ->withErrors([
                        'recurring_end_time' =>
                            'Jam selesai harus setelah jam mulai.'
                    ])
                    ->withInput();
            }


            // ====================================================
            // TANGGAL
            // ====================================================

            $startDate = Carbon::parse(
                $request->start_date
            )->startOfDay();

            $endDate = Carbon::parse(
                $request->recurrence_end_date
            )->startOfDay();


            // ====================================================
            // HARI YANG DIPILIH
            // ====================================================

            $days = collect($request->recurrence_days)
                ->map(fn ($day) => (int) $day)
                ->unique()
                ->sort()
                ->values()
                ->toArray();


            // ====================================================
            // CEK BENTROK RECURRING
            // ====================================================

            /*
             * force_save = 1 berarti user sudah memilih
             * "Tetap Simpan".
             *
             * Jika belum force_save, kita lakukan pengecekan.
             */

            if (!$request->boolean('force_save')) {

                $conflicts = [];

                $currentDate = $startDate->copy();

                while ($currentDate->lte($endDate)) {

                    if (
                        in_array(
                            $currentDate->dayOfWeek,
                            $days
                        )
                    ) {

                        $startDateTime = Carbon::parse(
                            $currentDate->format('Y-m-d')
                            . ' '
                            . $request->recurring_start_time
                        );

                        $endDateTime = Carbon::parse(
                            $currentDate->format('Y-m-d')
                            . ' '
                            . $request->recurring_end_time
                        );


                        $dateConflicts = $this->findConflicts(
                            $startDateTime,
                            $endDateTime
                        );


                        foreach ($dateConflicts as $conflict) {

                            $conflicts[] = [
                                'date' => $currentDate->format('d-m-Y'),
                                'title' => $conflict->title,
                                'start' => $conflict->start_time->format('H:i'),
                                'end' => $conflict->end_time->format('H:i'),
                            ];
                        }
                    }

                    $currentDate->addDay();
                }


                // =================================================
                // JIKA BENTROK
                // =================================================

                if (!empty($conflicts)) {

                    return back()
                        ->withInput()
                        ->with(
                            'schedule_conflicts',
                            $conflicts
                        );
                }
            }


            // ====================================================
            // RECURRENCE ID
            // ====================================================

            $recurrenceId = (string) Str::uuid();


            // ====================================================
            // GENERATE JADWAL
            // ====================================================

            $currentDate = $startDate->copy();

            $createdCount = 0;


            while ($currentDate->lte($endDate)) {

                /*
                 * Carbon:
                 *
                 * 0 = Minggu
                 * 1 = Senin
                 * 2 = Selasa
                 * 3 = Rabu
                 * 4 = Kamis
                 * 5 = Jumat
                 * 6 = Sabtu
                 */

                if (
                    in_array(
                        $currentDate->dayOfWeek,
                        $days
                    )
                ) {

                    $startDateTime = Carbon::parse(
                        $currentDate->format('Y-m-d')
                        . ' '
                        . $request->recurring_start_time
                    );

                    $endDateTime = Carbon::parse(
                        $currentDate->format('Y-m-d')
                        . ' '
                        . $request->recurring_end_time
                    );


                    Schedule::create([

                        'user_id' => auth()->id(),

                        'title' => $request->title,

                        'start_time' => $startDateTime,

                        'end_time' => $endDateTime,

                        'type' => $request->type,

                        'is_fixed' =>
                            $request->boolean('is_fixed'),

                        'description' =>
                            $request->description,

                        // Recurring information
                        'recurrence_id' =>
                            $recurrenceId,

                        'is_recurring' => true,

                        'recurrence_type' =>
                            'weekly',

                        'recurrence_start_date' =>
                            $startDate->toDateString(),

                        'recurrence_end_date' =>
                            $endDate->toDateString(),

                        'recurrence_days' =>
                            $days,
                    ]);


                    $createdCount++;
                }


                $currentDate->addDay();
            }


            // ====================================================
            // JIKA TIDAK ADA JADWAL YANG TERBENTUK
            // ====================================================

            if ($createdCount === 0) {

                return back()
                    ->withErrors([
                        'recurrence_days' =>
                            'Tidak ada jadwal yang cocok dengan hari dan rentang tanggal yang dipilih.'
                    ])
                    ->withInput();
            }


            return redirect()
                ->route('calendar')
                ->with(
                    'success',
                    "Jadwal berulang berhasil dibuat. {$createdCount} jadwal ditambahkan."
                );
        }


        // ========================================================
        // JADWAL BIASA
        // ========================================================

        $request->validate([

            'start_time' => [
                'required',
                'date',
            ],

            'end_time' => [
                'required',
                'date',
                'after:start_time',
            ],
        ]);


        // ========================================================
        // PARSE WAKTU
        // ========================================================

        $start = Carbon::parse(
            $request->start_time
        );

        $end = Carbon::parse(
            $request->end_time
        );


        // ========================================================
        // CEK BENTROK
        // ========================================================

        if (!$request->boolean('force_save')) {

            $conflicts = $this->findConflicts(
                $start,
                $end
            );


            if ($conflicts->isNotEmpty()) {

                return back()
                    ->withInput()
                    ->with(
                        'schedule_conflicts',
                        $this->prepareConflictData($conflicts)
                    );
            }
        }


        // ========================================================
        // SIMPAN JADWAL BIASA
        // ========================================================

        Schedule::create([

            'user_id' => auth()->id(),

            'title' => $request->title,

            'start_time' => $start,

            'end_time' => $end,

            'type' => $request->type,

            'is_fixed' =>
                $request->boolean('is_fixed'),

            'description' =>
                $request->description,

            'is_recurring' => false,
        ]);


        return redirect()
            ->route('calendar')
            ->with(
                'success',
                'Jadwal berhasil ditambahkan.'
            );
    }


    // ============================================================
    // EDIT
    // ============================================================

    public function edit(Schedule $schedule)
    {
        abort_if(
            $schedule->user_id !== auth()->id(),
            403
        );

        return view(
            'calendar.edit',
            compact('schedule')
        );
    }


    // ============================================================
    // UPDATE
    // ============================================================

    public function update(
        Request $request,
        Schedule $schedule
    ) {
        abort_if(
            $schedule->user_id !== auth()->id(),
            403
        );


        $request->validate([

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'start_time' => [
                'required',
                'date',
            ],

            'end_time' => [
                'required',
                'date',
                'after:start_time',
            ],

            'type' => [
                'required',
                'in:academic,organization,personal,rest,other',
            ],

            'is_fixed' => [
                'nullable',
                'boolean',
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ]);


        // ========================================================
        // PARSE WAKTU
        // ========================================================

        $start = Carbon::parse(
            $request->start_time
        );

        $end = Carbon::parse(
            $request->end_time
        );


        // ========================================================
        // CEK BENTROK
        // ========================================================

        if (!$request->boolean('force_save')) {

            $conflicts = $this->findConflicts(
                $start,
                $end,
                $schedule->id
            );


            if ($conflicts->isNotEmpty()) {

                return back()
                    ->withInput()
                    ->with(
                        'schedule_conflicts',
                        $this->prepareConflictData($conflicts)
                    );
            }
        }


        // ========================================================
        // UPDATE
        // ========================================================

        $schedule->update([

            'title' =>
                $request->title,

            'start_time' =>
                $start,

            'end_time' =>
                $end,

            'type' =>
                $request->type,

            'is_fixed' =>
                $request->boolean('is_fixed'),

            'description' =>
                $request->description,
        ]);


        return redirect()
            ->route('calendar')
            ->with(
                'success',
                'Jadwal berhasil diperbarui.'
            );
    }


    // ============================================================
    // DELETE
    // ============================================================

    public function destroy(Schedule $schedule)
    {
        abort_if(
            $schedule->user_id !== auth()->id(),
            403
        );


        $schedule->delete();


        return redirect()
            ->route('calendar')
            ->with(
                'success',
                'Jadwal berhasil dihapus.'
            );
    }


    // ============================================================
    // HELPER - CARI BENTROK
    // ============================================================

    private function findConflicts(
        Carbon $start,
        Carbon $end,
        ?int $excludeScheduleId = null
    ) {
        $query = Schedule::where(
            'user_id',
            auth()->id()
        )
            ->where(
                'start_time',
                '<',
                $end
            )
            ->where(
                'end_time',
                '>',
                $start
            );


        // Saat edit, jangan cek dirinya sendiri
        if ($excludeScheduleId !== null) {

            $query->where(
                'id',
                '!=',
                $excludeScheduleId
            );
        }


        return $query
            ->orderBy('start_time')
            ->get();
    }


    // ============================================================
    // HELPER - SIAPKAN DATA BENTROK
    // ============================================================

    private function prepareConflictData($conflicts): array
    {
        return $conflicts
            ->map(function ($schedule) {

                return [
                    'date' =>
                        $schedule->start_time
                            ->format('d-m-Y'),

                    'title' =>
                        $schedule->title,

                    'start' =>
                        $schedule->start_time
                            ->format('H:i'),

                    'end' =>
                        $schedule->end_time
                            ->format('H:i'),
                ];

            })
            ->values()
            ->toArray();
    }
}