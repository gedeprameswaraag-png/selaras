<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->string('recurrence_id')->nullable()->index();
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_type')->nullable();
            $table->date('recurrence_start_date')->nullable();
            $table->date('recurrence_end_date')->nullable();
            $table->json('recurrence_days')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn([
                'recurrence_id',
                'is_recurring',
                'recurrence_type',
                'recurrence_start_date',
                'recurrence_end_date',
                'recurrence_days',
            ]);
        });
    }
};