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
        Schema::create('case_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_case_id')->constrained('beneficiary_cases');
            $table->foreignId('service_id')->constrained('services');
            $table->foreignId('receiver_entity_id')->constrained('entities');
            $table->string('referral_type');
            $table->string('direction');
            $table->string('status');
            $table->string('urgency_level');
            $table->text('reason');
            $table->text('notes')->nullable();
            $table->date('referral_date');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->date('followup_date');
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();

            $table->index(['receiver_entity_id', 'status']);
            $table->index(['beneficiary_case_id', 'status']);
            $table->index(['followup_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_referral_controllers');
    }
};
