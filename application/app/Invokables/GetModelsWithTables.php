<?php

namespace App\Invokables;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Container\Container;
use Illuminate\Support\Str;

class GetModelsWithTables
{
    public function __invoke(): array
    {
        $models = collect(File::allFiles(app_path()))
            ->map(function ($item) {
                $path = $item->getRelativePathName();
                if ($path === 'helpers.php') {
                    return false;
                }

                return sprintf('\%s%s',
                    Container::getInstance()->getNamespace(),
                    strtr(substr($path, 0, strrpos($path, '.')), '/', '\\'));
            })
            ->filter(function ($class) {
                $valid = false;
                if (! Str::contains($class, 'App\\Models')) {
                    return false;
                }
                if (class_exists($class)) {
                    $reflection = new \ReflectionClass($class);
                    $valid = $reflection->isSubclassOf(Model::class) &&
                        ! $reflection->isAbstract();
                }

                return $valid;
            })
            ->map(fn ($m) => trim($m, '\\'))
            ->values();

        $modelsWithTables = [];
        foreach ($models as $model) {
            $table = (new $model())->getTable();
            $modelsWithTables[$table] = $model;
        }

        return $modelsWithTables;
    }
}
