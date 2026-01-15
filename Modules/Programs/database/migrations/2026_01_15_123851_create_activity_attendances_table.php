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
        Schema::create('activity_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_session_id')->constrained('activity_sessions');
            $table->foreignId('beneficiary_id')->constrained('beneficiaries');
            $table->foreignId('recorded_by')->constrained('trainers');
            $table->string('attendance_status');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_attendances');
    }
};
