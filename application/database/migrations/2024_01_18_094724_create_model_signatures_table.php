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
        Schema::create('model_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('signature_id')
                ->references('id')
                ->on('signatures')
                ->onDelete('cascade');
            $table->morphs('model');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_signatures');
    }
};
