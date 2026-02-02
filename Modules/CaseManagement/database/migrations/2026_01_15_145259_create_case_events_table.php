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
        Schema::create('case_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->index();
            $table->foreignId('beneficiary_case_id')->index();
            $table->morphs('subject');
            $table->string('event_tag');
            $table->json('payload')->nullable();
            $table->foreignId('actor_id')->constrained('users');
            $table->timestamp('occurred_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_events');
    }
};
