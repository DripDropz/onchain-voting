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
        Schema::table('question_responses_question_choices', function (Blueprint $table) {
            $table->dropForeign(['question_response_id']);
            $table->dropForeign(['question_choice_id']);

            $table->foreign('question_response_id')->references('id')->on('question_responses');
            $table->foreign('question_choice_id')->references('id')->on('question_choices');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
