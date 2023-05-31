<?php

use App\Enums\ModelStatusEnum;
use App\Enums\SnapshotTypeEnum;
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
        Schema::create('snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->nullOnDelete();
            $table->foreignId('ballot_id')->nullable()->constrained('ballots')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('policy_id');
            $table->enum('status', ModelStatusEnum::values())->default(ModelStatusEnum::DRAFT->value);
            $table->enum('type', SnapshotTypeEnum::values());
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snapshots');
    }
};
