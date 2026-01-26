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
        Schema::create('assessment_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->constrained()->onDelete('cascade');
            $table->foreignId('issue_type_id')->constrained()->onDelete('cascade');
            $table->integer('score')->default(0);
            $table->integer('max_score')->default(0);
            $table->decimal('normalized_score', 5, 2)->default(0.00);
            $table->string('priority_suggested', 50);
            $table->string('priority_final', 50)->nullable();
            $table->text('justification')->nullable();
            $table->boolean('is_latest')->default(1);
            $table->timestamp('assessed_at')->useCurrent();
            $table->foreignId('assessed_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->index(['beneficiary_id', 'issue_type_id', 'is_latest']);
            $table->index(['beneficiary_id', 'assessed_at']);
            $table->index(['normalized_score', 'priority_final']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_results');
    }
};
