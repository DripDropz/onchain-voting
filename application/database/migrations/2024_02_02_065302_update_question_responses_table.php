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
        Schema::table('question_responses', function (Blueprint $table) {
            $table->foreignId('voting_power_id')->nullable()->change();
            $table->dropForeign(['ballot_question_choice_id']);
            $table->dropColumn('ballot_question_choice_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('question_responses', function (Blueprint $table) {
            //
        });
    }
};
