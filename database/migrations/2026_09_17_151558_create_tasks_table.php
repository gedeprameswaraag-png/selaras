<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            $table->dateTime('deadline');

            // Estimasi waktu pengerjaan dalam menit
            $table->unsignedInteger('estimated_duration');

            $table->enum('priority', [
                'low',
                'medium',
                'high'
            ])->default('medium');

            $table->enum('status', [
                'todo',
                'in_progress',
                'completed'
            ])->default('todo');

            $table->enum('category', [
                'academic',
                'organization',
                'personal',
                'other'
            ])->default('academic');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};