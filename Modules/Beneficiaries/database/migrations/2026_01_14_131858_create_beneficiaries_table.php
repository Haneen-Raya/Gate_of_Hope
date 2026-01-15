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
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('governorate');
            $table->string('gender');
            $table->date('date_of_birth');
            $table->string('address');
            $table->string('residence_type');
            $table->boolean('is_displaced')->default(0);
            $table->boolean('has_other_provider')->default(0);
            $table->string('original_hometown');
            $table->string('disability_type');
            $table->string('system_code')->unique()->index();
            $table->integer('serial_number');
            $table->string('identity_hash')->unique()->index();
            $table->string('national_id')->unique();
            $table->boolean('is_verified')->default(false);
            $table->timestamp('consent_withdrawn_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};
