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
        Schema::create('beneficiary_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->constrained('beneficiaries');
            $table->foreignId('issue_type_id')->constrained('issue_types');
            $table->foreignId('case_manager_id')->constrained('users');
            $table->foreignId('region_id')->constrained('regions');
            $table->string('status')->index();
            $table->string('priority');
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->text('closure_reason')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'region_id', 'case_manager_id'], 'case_search_index');
            $table->index(['status', 'priority', 'opened_at']);
            $table->index(['region_id', 'priority', 'status']);
            $table->index(['beneficiary_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cases');
    }
};
