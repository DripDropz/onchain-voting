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
        // Schema::table('petitions', function (Blueprint $table) {
        //     $table->renameColumn('start_at', 'started_at');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('petitions', function (Blueprint $table) {
            $table->renameColumn('started_at', 'start_at');
        });
    }
};
