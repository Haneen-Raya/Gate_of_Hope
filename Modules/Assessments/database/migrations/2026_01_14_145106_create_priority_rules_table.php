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
        Schema::create('priority_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_type_id')->constrained()->onDelete('cascade');
            $table->integer('min_score');
            $table->integer('max_score');
            $table->string('priority');
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('priority_rules');
    }
};
