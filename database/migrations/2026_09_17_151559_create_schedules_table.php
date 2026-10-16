<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title');

            $table->dateTime('start_time');
            $table->dateTime('end_time');

            $table->enum('type', [
                'academic',
                'organization',
                'personal',
                'rest',
                'other'
            ])->default('academic');

            // true = tidak boleh dipindahkan otomatis
            // false = masih fleksibel
            $table->boolean('is_fixed')->default(true);

            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};