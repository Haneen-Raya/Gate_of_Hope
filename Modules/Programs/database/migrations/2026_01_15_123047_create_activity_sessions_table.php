<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activity_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->constrained('activities');
            $table->foreignId('trainer_id')->constrained('trainers');
            $table->date('session_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->geometry('location', 'point');
            $table->integer('capacity');
            $table->string('status')->index();
            $table->text('session_notes')->nullable();
            $table->timestamps();

            $table->spatialIndex('location');
            $table->index(['activity_id', 'session_date']);
            $table->index(['trainer_id', 'session_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_sessions');
    }
};
