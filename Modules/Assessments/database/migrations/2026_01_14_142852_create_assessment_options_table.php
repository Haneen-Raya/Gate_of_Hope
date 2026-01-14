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
        Schema::create('assessment_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('assessment_questions')->onDelete('cascade');
            $table->string('label'); 
            $table->integer('value')->default(0); 
            $table->timestamps();
            $table->index('question_id');
            $table->index('value');
            $table->unique(['question_id', 'value']); 
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_options');
    }
};
