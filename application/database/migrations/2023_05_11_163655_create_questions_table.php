<?php

use App\Enums\ModelStatusEnum;
use App\Enums\QuestionTypeEnum;
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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('ballot_id')->nullable()->constrained('ballots')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('supplemental')->nullable();
            $table->unsignedTinyInteger('max_choices')->nullable()->default(1);
            $table->enum('status', ModelStatusEnum::values())->default(ModelStatusEnum::DRAFT->value);
            $table->enum('type', QuestionTypeEnum::values());
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
