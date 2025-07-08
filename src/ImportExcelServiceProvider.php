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
            // Register commands
            $this->commands([
                \Apriansyahrs\ImportExcel\Console\Commands\SetupImportExcelCommand::class,
                \Apriansyahrs\ImportExcel\Console\Commands\PublishMigrationsCommand::class,
            ]);

            // Publish translations
            $this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/import-excel'),
            ], 'import-excel-translations');

            // Keep traditional publish method as fallback
            $this->publishAllMigrationOptions();
        }
    }

    /**
     * Publish all migration options - let user decide which to run based on their setup.
     */
    protected function publishAllMigrationOptions(): void
    {
        $baseTimestamp = time();
        $publishPaths = [];
        
        // Always publish all migration options with dynamic timestamps
        $migrations = [
            'create_imports_table',
            'create_failed_import_rows_table', 
            'add_columns_to_imports_table',
            'add_columns_to_failed_import_rows_table'
        ];
        
        foreach ($migrations as $index => $migrationName) {
            $timestamp = date('Y_m_d_His', $baseTimestamp + $index);
            $sourcePath = __DIR__.'/../database/stubs/'.$migrationName.'.php';
            $targetPath = database_path('migrations/'.$timestamp.'_'.$migrationName.'.php');
            
            if (File::exists($sourcePath)) {
                $publishPaths[$sourcePath] = $targetPath;
            }
        }

        if (!empty($publishPaths)) {
            $this->publishes($publishPaths, 'import-excel-migrations');
        }
    }

    public function register(): void
    {
        // Register any package services/bindings here
    }
}
