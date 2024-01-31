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
        Schema::create('question_responses_question_choices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_response_id')->constrained('ballot_responses');
            $table->foreignId('question_choice_id')->constrained('ballot_question_choices');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ballot_responses_ballot_question_choices');
    }
};
