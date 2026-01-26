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
        Schema::create('case_plan_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('case_support_plans');
            $table->text('goal_description');
            $table->string('status');
            $table->date('target_date');
            $table->date('achieved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_plan_goals');
    }
};
