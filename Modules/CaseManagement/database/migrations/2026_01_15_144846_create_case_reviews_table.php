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
        Schema::create('case_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId(column: 'beneficiary_case_id')->constrained('beneficiary_cases');
            $table->foreignId('specialist_id')->constrained('specialists');
            $table->string('progress_status');
            $table->text('notes')->nullable();
            $table->timestamp('reviewed_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_reviews');
    }
};
