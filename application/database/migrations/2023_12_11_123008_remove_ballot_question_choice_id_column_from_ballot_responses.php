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
        Schema::table('ballot_responses', function (Blueprint $table) {
            $table->dropForeign(['ballot_question_choice_id']);
            $table->dropColumn('ballot_question_choice_id');
            $table->dropColumn('rank');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ballot_responses', function (Blueprint $table) {
            $table->integer('rank')->nullable();
            $table->foreignId('ballot_question_choice_id')->constrained('ballot_question_choices');
        });
    }
};
