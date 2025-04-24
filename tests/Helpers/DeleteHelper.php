<?php

namespace Tests\Helpers;

use Exception;
use Illuminate\Support\Facades\File;

class DeleteHelper
{
    /**
     *
     *
     * @param array
     * @return void
     * @throws Exception
     */
    public static function delete(array $tableNames): void
    {
        foreach ($tableNames as $tableName) {
            $modelClass = self::findModelByTableName($tableName);

            if (!$modelClass) {
                throw new Exception("Model bulunamadı. Tablo: $tableName");
            }

            foreach ($modelClass::all() as $record) {
                $record->delete(); 
            }
        }
    }
    /*
    @param string $tableName
     * @return string|null
     */
    private static function findModelByTableName(string $tableName): ?string
    {
        $modelPath = app_path('Models');
        $modelFiles =  File::allFiles($modelPath);

        foreach ($modelFiles as $file) {
            $className = 'App\\Models\\' . $file->getFilenameWithoutExtension();

            if (!class_exists($className)) {
                continue;
            }

            $instance = new $className();

            if (method_exists($instance, 'getTable') && $instance->getTable() === $tableName) {
                return $className;
            }
        }

        return null;
    }
}
