<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recommendations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('type', [
                'workload',
                'deadline',
                'schedule',
                'recovery',
                'general'
            ])->default('general');

            $table->string('title');
            $table->text('message');

            $table->foreignId('related_task_id')
                ->nullable()
                ->constrained('tasks')
                ->nullOnDelete();

            $table->enum('status', [
                'pending',
                'accepted',
                'rejected',
                'completed'
            ])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommendations');
    }
};