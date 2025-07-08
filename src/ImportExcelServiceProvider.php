<?php

namespace Apriansyahrs\ImportExcel;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class ImportExcelServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadTranslationsFrom(
            __DIR__.'/../resources/lang', // Correct path to package lang files
            'import-excel' // Translation namespace
        );

        // Load migrations from package
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Publishing resources
        if ($this->app->runningInConsole()) {
            // Publish translations
            $this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/import-excel'),
            ], 'import-excel-translations');

            // Publish migrations with dynamic timestamps
            $this->publishMigrationsWithTimestamp();
        }
    }

    /**
     * Publish migrations with current timestamp to avoid conflicts.
     */
    protected function publishMigrationsWithTimestamp(): void
    {
        $baseTimestamp = time();
        
        $migrations = [
            'create_imports_table.php',
            'create_failed_import_rows_table.php',
        ];

        $publishPaths = [];
        foreach ($migrations as $index => $migrationName) {
            $timestamp = date('Y_m_d_His', $baseTimestamp + $index);
            $sourcePath = __DIR__.'/../database/stubs/'.$migrationName;
            $targetPath = database_path('migrations/'.$timestamp.'_'.$migrationName);
            
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
