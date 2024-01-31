<?php

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
        Schema::create('petitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->text('title');
            $table->text('description');
            $table->enum('status', ModelStatusEnum::values())->default(ModelStatusEnum::DRAFT->value);
            $table->timestamp('started_at')->nullable()->default(null);
            $table->timestamp('ended_at')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pertitions');
    }
};
