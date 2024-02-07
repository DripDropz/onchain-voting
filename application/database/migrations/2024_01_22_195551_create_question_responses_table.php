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
        Schema::create('question_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->nullOnDelete();
            $table->foreignId('model_id')->constrained('ballots')->nullOnDelete();
            $table->text('model_type')->constrained('ballots')->nullOnDelete();
            $table->foreignId('question_id')->constrained('questions')->nullOnDelete();
            $table->foreignId('voting_power_id')->constrained('voting_powers')->nullOnDelete();
            $table->integer('rank')->nullable();
            $table->text('submit_tx')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_responses');
    }
};
