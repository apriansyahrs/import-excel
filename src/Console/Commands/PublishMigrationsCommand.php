<?php

namespace Apriansyahrs\ImportExcel\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

class PublishMigrationsCommand extends Command
{
    protected $signature = 'import-excel:publish-migrations {--force : Overwrite existing migrations}';
    
    protected $description = 'Intelligently publish Import Excel migrations based on current database state';

    public function handle()
    {
        $this->info('🔍 Checking current database state...');
        
        $migrations = $this->determineMigrationsNeeded();
        
        if (empty($migrations)) {
            $this->info('✅ All required tables and columns already exist. No migrations needed.');
            return 0;
        }
        
        $this->info('📝 Publishing required migrations...');
        
        $baseTimestamp = time();
        $published = [];
        
        foreach ($migrations as $index => $migration) {
            $timestamp = date('Y_m_d_His', $baseTimestamp + $index);
            $published[] = $this->publishMigration($migration, $timestamp);
        }
        
        $this->info('✅ Successfully published migrations:');
        foreach ($published as $file) {
            $this->line("   - {$file}");
        }
        
        $this->line('');
        $this->info('💡 Next step: Run php artisan migrate');
        
        return 0;
    }

    protected function determineMigrationsNeeded(): array
    {
        $migrations = [];
        
        try {
            // Check imports table
            if (!Schema::hasTable('imports')) {
                $migrations[] = 'create_imports_table';
                $this->line('   📋 imports table: NOT EXISTS → will CREATE');
            } else {
                $missingColumns = $this->getMissingImportsColumns();
                if (!empty($missingColumns)) {
                    $migrations[] = 'add_columns_to_imports_table';
                    $this->line('   📋 imports table: EXISTS → will ADD columns: ' . implode(', ', $missingColumns));
                } else {
                    $this->line('   ✅ imports table: OK (all columns exist)');
                }
            }
            
            // Check failed_import_rows table
            if (!Schema::hasTable('failed_import_rows')) {
                $migrations[] = 'create_failed_import_rows_table';
                $this->line('   📋 failed_import_rows table: NOT EXISTS → will CREATE');
            } else {
                $missingColumns = $this->getMissingFailedImportRowsColumns();
                if (!empty($missingColumns)) {
                    $migrations[] = 'add_columns_to_failed_import_rows_table';
                    $this->line('   📋 failed_import_rows table: EXISTS → will ADD columns: ' . implode(', ', $missingColumns));
                } else {
                    $this->line('   ✅ failed_import_rows table: OK (all columns exist)');
                }
            }
            
        } catch (\Exception $e) {
            $this->warn('   ⚠️  Could not check database (maybe not connected). Publishing all migrations.');
            $migrations = ['create_imports_table', 'create_failed_import_rows_table'];
        }
        
        return $migrations;
    }

    protected function getMissingImportsColumns(): array
    {
        $missing = [];
        
        if (!Schema::hasColumn('imports', 'imported_rows')) {
            $missing[] = 'imported_rows';
        }
        if (!Schema::hasColumn('imports', 'failed_rows')) {
            $missing[] = 'failed_rows';
        }
        
        return $missing;
    }

    protected function getMissingFailedImportRowsColumns(): array
    {
        $missing = [];
        
        if (!Schema::hasColumn('failed_import_rows', 'error')) {
            $missing[] = 'error';
        }
        
        return $missing;
    }

    protected function publishMigration(string $migrationName, string $timestamp): string
    {
        $stubPath = base_path("vendor/apriansyahrs/import-excel/database/stubs/{$migrationName}.php");
        $targetFileName = "{$timestamp}_{$migrationName}.php";
        $targetPath = database_path("migrations/{$targetFileName}");
        
        if (!File::exists($stubPath)) {
            throw new \Exception("Stub file not found: {$stubPath}");
        }
        
        if (File::exists($targetPath) && !$this->option('force')) {
            $this->warn("   Migration already exists: {$targetFileName}");
            return $targetFileName;
        }
        
        File::copy($stubPath, $targetPath);
        
        return $targetFileName;
    }
}
