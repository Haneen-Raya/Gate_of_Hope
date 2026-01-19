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
        Schema::create('social_backgrounds', function (Blueprint $table) {
            $table->id();

            $table->foreignId('beneficiary_id')->nullable()->constrained('beneficiaries')->cascadeOnDelete();
            $table->foreignId('education_level_id')->nullable()->constrained('education_levels')->nullOnDelete();
            $table->foreignId('employment_status_id')->nullable()->constrained('employment_statuses')->nullOnDelete();
            $table->foreignId('housing_type_id')->nullable()->constrained('housing_types')->nullOnDelete();
            $table->string('housing_tenure');
            $table->string('income_level');
            $table->string('living_standard');
            $table->integer('family_size')->default(1)->unsigned();
            $table->string('family_stability');
            $table->unique('beneficiary_id');
            $table->index('income_level');
            $table->index('living_standard');
            $table->index('family_stability');
            $table->index(['income_level', 'living_standard']);
            $table->index(['family_size', 'family_stability']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_backgrounds');
    }
};
