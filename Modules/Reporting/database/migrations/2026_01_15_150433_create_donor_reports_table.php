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
        Schema::create('donor_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_entity_id')->constrained('entities');
            $table->foreignId('program_id')->constrained('programs');
            $table->json('aggregated_data');
            $table->date('reporting_period_start');
            $table->date('reporting_period_end');
            $table->timestamps();

            $table->index(['donor_entity_id', 'reporting_period_start', 'reporting_period_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donor_reports');
    }
};
