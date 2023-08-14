<?php

use App\Enums\PolicyTypeEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $enum_values = PolicyTypeEnum::values();
        $enum_values_string = "'" . join("', '", $enum_values) . "'";

        $current_values = DB::select('SELECT context, id FROM policies');

        DB::transaction(function () use ($enum_values_string, $current_values, $enum_values) {
            DB::statement('ALTER TABLE policies ALTER COLUMN context TYPE TEXT');
            DB::statement("ALTER TABLE policies ADD CONSTRAINT check_context CHECK (context::TEXT = ANY (ARRAY[{$enum_values_string}]::TEXT[]))");

            foreach ($current_values as $row) {
                $context = $row->context;
                $id = $row->id;

                if (!in_array($context, $enum_values)) {
                    $context = null;
                }

                DB::table('policies')->where('id', $id)->update(['context' => $context]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('policies', function (Blueprint $table) {
            // Add logic here to revert the migration
        });
    }
};
