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
        Schema::create('assessment_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_type_id')->constrained('issue_types')->onDelete('cascade'); 
            $table->text('question_text'); 
            $table->decimal('weight', 5, 2)->default(1.00); 
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->string('code')->unique()->nullable();
            $table->index('issue_type_id');
            $table->index('is_active');
            $table->index('sort_order');
            $table->index(['issue_type_id', 'is_active']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_questions');
    }
};
