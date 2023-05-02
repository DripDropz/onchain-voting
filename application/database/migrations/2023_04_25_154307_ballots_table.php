<?php

use App\Enums\BallotTypeEnum;
use App\Enums\ModelStatusEnum;
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
        Schema::create('ballots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('authority_id')->nullable()->constrained('authorities')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('version');
            $table->enum('status', ModelStatusEnum::values())->default(ModelStatusEnum::DRAFT->value);
            $table->enum('type', BallotTypeEnum::values())->default(BallotTypeEnum::SNAPSHOT->value);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ballots');
    }
};
