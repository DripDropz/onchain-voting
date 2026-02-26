<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE polls DROP CONSTRAINT IF EXISTS polls_status_check');
        DB::statement('ALTER TABLE polls ALTER COLUMN status TYPE varchar(255) USING status::varchar');
        DB::statement('ALTER TABLE polls ALTER COLUMN status SET NOT NULL');
        DB::statement("ALTER TABLE polls ALTER COLUMN status SET DEFAULT 'draft'");
        DB::statement('ALTER TABLE polls ADD CONSTRAINT polls_status_check CHECK (status IN (\'draft\', \'pending\', \'published\', \'approved\', \'rejected\'))');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE polls DROP CONSTRAINT IF EXISTS polls_status_check');
        DB::statement("ALTER TABLE polls ALTER COLUMN status SET DEFAULT 'pending'");
        DB::statement('ALTER TABLE polls ADD CONSTRAINT polls_status_check CHECK (status IN (\'draft\', \'pending\', \'published\', \'approved\', \'rejected\'))');
    }
};
