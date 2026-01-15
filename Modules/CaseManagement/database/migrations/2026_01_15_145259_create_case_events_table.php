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
        Schema::create('case_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_case_id')->constrained('beneficiary_cases');
            $table->foreignId('created_by')->constrained('users');
            $table->string('event_type');
            $table->text('summary');
            $table->string('event_ref_type');
            $table->integer('event_ref_id');
            $table->timestamps();

            $table->index(['beneficiary_case_id', 'created_at']);
            $table->index(['event_type']);
            $table->index(['event_ref_type', 'event_ref_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_events');
    }
};
