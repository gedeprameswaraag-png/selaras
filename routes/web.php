<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CheckinController;
use App\Http\Controllers\InsightController;
use App\Http\Controllers\RecommendationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('verified')
        ->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | Tasks
    |--------------------------------------------------------------------------
    */

    Route::resource('tasks', TaskController::class);


    /*
    |--------------------------------------------------------------------------
    | Smart Scheduling
    |--------------------------------------------------------------------------
    */

    Route::get('/tasks/{task}/smart-schedule', [
        TaskController::class,
        'smartSchedule'
    ])->name('tasks.smart-schedule');

    Route::post('/tasks/{task}/schedule', [
        TaskController::class,
        'schedule'
    ])->name('tasks.schedule');


    /*
    |--------------------------------------------------------------------------
    | Task Status
    |--------------------------------------------------------------------------
    */

    Route::post('/tasks/{task}/complete', [
        TaskController::class,
        'complete'
    ])->name('tasks.complete');

    Route::post('/tasks/{task}/reopen', [
        TaskController::class,
        'reopen'
    ])->name('tasks.reopen');


    /*
    |--------------------------------------------------------------------------
    | Calendar
    |--------------------------------------------------------------------------
    */

    Route::get('/calendar', [ScheduleController::class, 'index'])
        ->name('calendar');

    Route::get('/calendar/create', [ScheduleController::class, 'create'])
        ->name('calendar.create');

    Route::post('/calendar', [ScheduleController::class, 'store'])
        ->name('calendar.store');


    /*
    |--------------------------------------------------------------------------
    | Edit Schedule
    |--------------------------------------------------------------------------
    */

    Route::get('/calendar/schedule-{schedule}/edit', [
        ScheduleController::class,
        'edit'
    ])->name('calendar.edit.prefixed');

    Route::get('/calendar/{schedule}/edit', [
        ScheduleController::class,
        'edit'
    ])->name('calendar.edit');


    /*
    |--------------------------------------------------------------------------
    | Update Schedule
    |--------------------------------------------------------------------------
    */

    Route::put('/calendar/{schedule}', [
        ScheduleController::class,
        'update'
    ])->name('calendar.update');


    /*
    |--------------------------------------------------------------------------
    | Delete Schedule
    |--------------------------------------------------------------------------
    */

    Route::delete('/calendar/schedule-{schedule}', [
        ScheduleController::class,
        'destroy'
    ])->name('calendar.destroy.prefixed');

    Route::delete('/calendar/{schedule}', [
        ScheduleController::class,
        'destroy'
    ])->name('calendar.destroy');


    /*
    |--------------------------------------------------------------------------
    | Check-in
    |--------------------------------------------------------------------------
    */

    Route::post('/checkin', [CheckinController::class, 'store'])
        ->name('checkin.store');


    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');


    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    */

    Route::get('/settings', [ProfileController::class, 'settings'])
        ->name('settings');

    Route::put('/settings/password', [
        ProfileController::class,
        'updatePassword'
    ])->name('settings.password');


    /*
    |--------------------------------------------------------------------------
    | Insights
    |--------------------------------------------------------------------------
    */

    Route::get('/insights', [InsightController::class, 'index'])
        ->name('insights');


    /*
    |--------------------------------------------------------------------------
    | Recommendations
    |--------------------------------------------------------------------------
    */

    Route::post('/recommendations/{recommendation}/accept', [
        RecommendationController::class,
        'accept'
    ])->name('recommendations.accept');

    Route::post('/recommendations/{recommendation}/reject', [
        RecommendationController::class,
        'reject'
    ])->name('recommendations.reject');

});

require __DIR__ . '/auth.php';