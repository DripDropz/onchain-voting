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
        Schema::create('ballot_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->nullOnDelete();
            $table->foreignId('ballot_id')->constrained('ballots')->nullOnDelete();
            $table->foreignId('question_id')->constrained('questions')->nullOnDelete();
            $table->foreignId('voting_power_id')->constrained('voting_powers')->nullOnDelete();
            $table->foreignId('ballot_question_choice_id')->constrained('ballot_question_choices')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ballot_responses');
    }
};
