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
        Schema::create('case_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_case_id')->constrained('beneficiary_cases');
            $table->string('session_type');
            $table->date('session_date');
            $table->integer('duration_minutes');
            $table->text('notes')->nullable();
            $table->text('recommendations')->nullable();
            $table->foreignId('conducted_by')->constrained('trainers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_sessions');
    }
};
