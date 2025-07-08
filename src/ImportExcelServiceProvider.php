<?php

namespace Apriansyahrs\ImportExcel;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class ImportExcelServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadTranslationsFrom(
            __DIR__.'/../resources/lang', // Correct path to package lang files
            'import-excel' // Translation namespace
        );

        // TIDAK auto-load migrations dari package untuk menghindari duplikasi
        // User harus publish migrations terlebih dahulu ATAU gunakan setup command
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Publishing resources
        if ($this->app->runningInConsole()) {
            // Register setup command (fallback jika user prefer command)
            $this->commands([
                \Apriansyahrs\ImportExcel\Console\Commands\SetupImportExcelCommand::class,
            ]);

            // Publish translations
            $this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/import-excel'),
            ], 'import-excel-translations');

            // Smart publish migrations - auto detect dan generate yang tepat
            $this->publishIntelligentMigrations();
        }
    }

    /**
     * Publish intelligent migrations - auto detect table status and create appropriate migration.
     */
    protected function publishIntelligentMigrations(): void
    {
        // Dynamic check saat publish command dijalankan
        $baseTimestamp = time();
        $publishPaths = [];
        
        // Check imports table
        $importsAction = $this->detectTableAction('imports');
        if ($importsAction) {
            $timestamp = date('Y_m_d_His', $baseTimestamp);
            $sourcePath = __DIR__.'/../database/stubs/'.$importsAction.'.php';
            $targetPath = database_path('migrations/'.$timestamp.'_'.$importsAction.'.php');
            
            if (File::exists($sourcePath)) {
                $publishPaths[$sourcePath] = $targetPath;
            }
        }
        
        // Check failed_import_rows table
        $failedRowsAction = $this->detectTableAction('failed_import_rows');
        if ($failedRowsAction) {
            $timestamp = date('Y_m_d_His', $baseTimestamp + 1);
            $sourcePath = __DIR__.'/../database/stubs/'.$failedRowsAction.'.php';
            $targetPath = database_path('migrations/'.$timestamp.'_'.$failedRowsAction.'.php');
            
            if (File::exists($sourcePath)) {
                $publishPaths[$sourcePath] = $targetPath;
            }
        }

        if (!empty($publishPaths)) {
            $this->publishes($publishPaths, 'import-excel-migrations');
        }
    }

    /**
     * Detect what action is needed for a table.
     */
    protected function detectTableAction(string $tableName): ?string
    {
        try {
            if (!Schema::hasTable($tableName)) {
                // Table doesn't exist, need to create
                return $tableName === 'imports' ? 'create_imports_table' : 'create_failed_import_rows_table';
            }
            
            // Table exists, check if we need to add columns
            $missingColumns = $this->getMissingColumns($tableName);
            if (!empty($missingColumns)) {
                return $tableName === 'imports' ? 'add_columns_to_imports_table' : 'add_columns_to_failed_import_rows_table';
            }
            
            // Table exists and has all required columns
            return null;
        } catch (\Exception $e) {
            // In case of any error (like no database connection), default to create
            return $tableName === 'imports' ? 'create_imports_table' : 'create_failed_import_rows_table';
        }
    }

    /**
     * Get missing columns for a table.
     */
    protected function getMissingColumns(string $tableName): array
    {
        $missingColumns = [];
        
        try {
            if ($tableName === 'imports') {
                if (!Schema::hasColumn('imports', 'imported_rows')) {
                    $missingColumns[] = 'imported_rows';
                }
                if (!Schema::hasColumn('imports', 'failed_rows')) {
                    $missingColumns[] = 'failed_rows';
                }
            } elseif ($tableName === 'failed_import_rows') {
                if (!Schema::hasColumn('failed_import_rows', 'error')) {
                    $missingColumns[] = 'error';
                }
                // Check jika validation_error tidak ada (untuk kasus table yang dibuat manual)
                if (!Schema::hasColumn('failed_import_rows', 'validation_error')) {
                    $missingColumns[] = 'validation_error';
                }
            }
        } catch (\Exception $e) {
            // Jika ada error dalam checking column, return empty array
            // Nanti akan fallback ke create table
        }
        
        return $missingColumns;
    }

    public function register(): void
    {
        // Register any package services/bindings here
    }
}
