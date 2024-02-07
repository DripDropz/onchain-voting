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
            $table->foreignId('question_choice_id')->constrained('question_choices')->cascadeOnDelete();
            $table->foreignId('question_response_id')->constrained('question_responses')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_responses_question_choices');
    }
};
