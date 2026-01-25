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
        Schema::create('case_support_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_case_id')->constrained('beneficiary_cases');
            $table->integer('version');
            $table->boolean('is_active')->default(1);
            $table->date('start_date');
            $table->date('end_date');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_support_plans');
    }
};
