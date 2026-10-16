<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checkins', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('date');

            // 1 = sangat rendah
            // 5 = sangat tinggi
            $table->unsignedTinyInteger('energy_level');

            $table->enum('workload_level', [
                'light',
                'normal',
                'heavy'
            ])->default('normal');

            $table->text('note')->nullable();

            $table->timestamps();

            $table->unique([
                'user_id',
                'date'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checkins');
    }
};