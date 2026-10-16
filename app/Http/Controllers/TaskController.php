<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Services\SmartSchedulingService;
use Carbon\Carbon;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::where('user_id', auth()->id())
            ->orderBy('deadline')
            ->get();

        return view('tasks.index', compact('tasks'));
    }


    public function create()
    {
        return view('tasks.create');
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'required|date',
            'estimated_duration' => 'required|integer|min:1',
            'priority' => 'required|in:low,medium,high',
            'category' => 'required|in:academic,organization,personal,other',
        ]);

        $validated['user_id'] = auth()->id();

        Task::create($validated);

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task berhasil ditambahkan.');
    }


    public function show(Task $task)
    {
        $this->authorizeTask($task);

        return view('tasks.show', compact('task'));
    }


    public function edit(Task $task)
    {
        $this->authorizeTask($task);

        return view('tasks.edit', compact('task'));
    }


    public function update(Request $request, Task $task)
    {
        $this->authorizeTask($task);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'required|date',
            'estimated_duration' => 'required|integer|min:1',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:todo,in_progress,completed',
            'category' => 'required|in:academic,organization,personal,other',
        ]);

        $task->update($validated);

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task berhasil diperbarui.');
    }


    /*
    |--------------------------------------------------------------------------
    | Complete Task
    |--------------------------------------------------------------------------
    */

    public function complete(Task $task)
    {
        $this->authorizeTask($task);

        $task->update([
            'status' => 'completed',
        ]);

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task berhasil ditandai sebagai selesai.');
    }


    /*
    |--------------------------------------------------------------------------
    | Reopen Task
    |--------------------------------------------------------------------------
    */

    public function reopen(Task $task)
    {
        $this->authorizeTask($task);

        $task->update([
            'status' => 'todo',
        ]);

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task dibuka kembali.');
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Task
    |--------------------------------------------------------------------------
    */

    public function destroy(Task $task)
    {
        $this->authorizeTask($task);

        $task->delete();

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task berhasil dihapus.');
    }


    /*
    |--------------------------------------------------------------------------
    | Smart Scheduling
    |--------------------------------------------------------------------------
    */

    public function smartSchedule(
        Task $task,
        SmartSchedulingService $smartSchedulingService
    ) {
        $this->authorizeTask($task);

        $slots = $smartSchedulingService->findAvailableSlots($task);

        $recommendedSlots =
            $smartSchedulingService->findSchedulableSlots($task);

        $feasibility =
            $smartSchedulingService->checkTaskFeasibility($task);

        return view('tasks.smart-schedule', compact(
            'task',
            'slots',
            'recommendedSlots',
            'feasibility'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | Create Schedule From Smart Scheduling
    |--------------------------------------------------------------------------
    */

    public function schedule(
        Request $request,
        Task $task
    ) {
        $this->authorizeTask($task);

        $request->validate([
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after:start_time'],
        ]);

        $start = Carbon::parse($request->start_time);
        $end = Carbon::parse($request->end_time);


        /*
        |--------------------------------------------------------------------------
        | Batasi waktu pengerjaan
        |--------------------------------------------------------------------------
        |
        | MindCraft hanya merekomendasikan dan membuat jadwal
        | pengerjaan task pada pukul 05.00 sampai 22.00.
        |
        */

        $startOfDay = $start->copy()
            ->startOfDay()
            ->setTime(5, 0, 0);

        $endOfDay = $start->copy()
            ->startOfDay()
            ->setTime(22, 0, 0);

        if (
            $start->lessThan($startOfDay) ||
            $end->greaterThan($endOfDay)
        ) {
            return back()
                ->withErrors([
                    'start_time' =>
                        'Jadwal pengerjaan task hanya dapat dibuat antara pukul 05.00 sampai 22.00.'
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | Pastikan tidak bentrok dengan jadwal lain
        |--------------------------------------------------------------------------
        */

        $conflict = Schedule::where('user_id', auth()->id())
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start)
            ->exists();

        if ($conflict) {

            return back()
                ->withErrors([
                    'start_time' =>
                        'Slot tersebut sudah digunakan oleh jadwal lain.'
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Pastikan tidak melewati deadline task
        |--------------------------------------------------------------------------
        */

        if (
            $end->greaterThan(
                Carbon::parse($task->deadline)
            )
        ) {

            return back()
                ->withErrors([
                    'start_time' =>
                        'Jadwal tidak boleh melewati deadline task.'
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Tentukan tipe schedule berdasarkan kategori task
        |--------------------------------------------------------------------------
        */

        $scheduleType = match ($task->category) {

            'academic' => 'academic',

            'organization' => 'organization',

            'personal' => 'personal',

            default => 'other',

        };


        /*
        |--------------------------------------------------------------------------
        | Buat jadwal pengerjaan task
        |--------------------------------------------------------------------------
        */

        Schedule::create([
            'user_id' => auth()->id(),

            'title' => 'Pengerjaan: ' . $task->title,

            'start_time' => $start,

            'end_time' => $end,

            'type' => $scheduleType,

            'is_fixed' => false,

            'description' =>
                'Jadwal pengerjaan task: ' . $task->title,

            'is_recurring' => false,
        ]);


        /*
        |--------------------------------------------------------------------------
        | Ubah status task menjadi in progress
        |--------------------------------------------------------------------------
        */

        if ($task->status === 'todo') {

            $task->update([
                'status' => 'in_progress',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Kembali ke Calendar
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('calendar')
            ->with(
                'success',
                'Jadwal pengerjaan task berhasil ditambahkan ke Calendar.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Authorization
    |--------------------------------------------------------------------------
    */

    private function authorizeTask(Task $task): void
    {
        abort_unless(
            $task->user_id === auth()->id(),
            403
        );
    }
}