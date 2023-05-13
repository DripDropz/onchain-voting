<?php

use Illuminate\Support\Str;
use App\Enums\ModelStatusEnum;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\ExpectationFailedException;


test('it checks if all models with status column support ModelStatusEnum', function () {
    $modelsWithTables = (new \App\Invokables\GetModelsWithTables())();
    $tables = array_keys($modelsWithTables);
    $tablesWithStatusCol = DB::select("
        SELECT table_name, column_name
        FROM information_schema.columns
        WHERE table_schema = 'public' AND column_name = 'status' AND table_name IN ('" . implode("', '", $tables) . "')
    ");

    foreach ($tablesWithStatusCol as $tableWithStatus) {
        $enumValues =  DB::table($tableWithStatus->table_name)->distinct()
            ->pluck('status')
            ->toArray();
        $supportedStatusEnumValues = ModelStatusEnum::values();

        foreach ($enumValues as $enumValue) {
            $modelName = Str::singular(class_basename($modelsWithTables[$tableWithStatus->table_name]));
            $this->assertContains(
                $enumValue,
                $supportedStatusEnumValues,
                "{$modelName} Model doest not support ModelStatusEnum"
            );
        }
    }
});
