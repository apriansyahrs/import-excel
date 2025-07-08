<?php

namespace Apriansyahrs\ImportExcel\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class SetupImportExcelCommand extends Command
{
    protected $signature = 'import-excel:setup';
    
    protected $description = 'Setup Import Excel plugin - create or update database tables';

    public function handle()
    {
        $this->info('🚀 Setting up Import Excel plugin...');
        
        // Check and setup imports table
        $this->setupImportsTable();
        
        // Check and setup failed_import_rows table
        $this->setupFailedImportRowsTable();
        
        $this->info('✅ Import Excel plugin setup completed successfully!');
        $this->line('');
        $this->line('You can now use the plugin in your Filament resources.');
        
        return 0;
    }

    protected function setupImportsTable(): void
    {
        $this->line('📋 Checking imports table...');
        
        if (!Schema::hasTable('imports')) {
            $this->info('   Creating imports table...');
            
            Schema::create('imports', function (Blueprint $table) {
                $table->id();
                $table->timestamp('completed_at')->nullable();
                $table->string('file_name');
                $table->string('file_path');
                $table->string('importer');
                $table->unsignedInteger('processed_rows')->default(0);
                $table->unsignedInteger('total_rows')->default(0);
                $table->unsignedInteger('successful_rows')->default(0);
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                
                // Kolom tambahan untuk plugin ini
                $table->unsignedInteger('imported_rows')->default(0);
                $table->unsignedInteger('failed_rows')->default(0);
                
                $table->timestamps();
            });
            
            $this->info('   ✅ imports table created successfully');
        } else {
            $this->info('   ⚡ imports table already exists, checking for required columns...');
            
            $columnsToAdd = [];
            
            // Check for additional columns that might be missing
            if (!Schema::hasColumn('imports', 'imported_rows')) {
                $columnsToAdd[] = 'imported_rows';
            }
            
            if (!Schema::hasColumn('imports', 'failed_rows')) {
                $columnsToAdd[] = 'failed_rows';
            }
            
            if (!empty($columnsToAdd)) {
                $this->info('   📝 Adding missing columns: ' . implode(', ', $columnsToAdd));
                
                Schema::table('imports', function (Blueprint $table) use ($columnsToAdd) {
                    if (in_array('imported_rows', $columnsToAdd)) {
                        $table->unsignedInteger('imported_rows')->default(0)->after('successful_rows');
                    }
                    if (in_array('failed_rows', $columnsToAdd)) {
                        $table->unsignedInteger('failed_rows')->default(0)->after('imported_rows');
                    }
                });
                
                $this->info('   ✅ Missing columns added successfully');
            } else {
                $this->info('   ✅ All required columns already exist');
            }
        }
    }

    protected function setupFailedImportRowsTable(): void
    {
        $this->line('📋 Checking failed_import_rows table...');
        
        if (!Schema::hasTable('failed_import_rows')) {
            $this->info('   Creating failed_import_rows table...');
            
            Schema::create('failed_import_rows', function (Blueprint $table) {
                $table->id();
                $table->json('data');
                $table->foreignId('import_id')->constrained()->cascadeOnDelete();
                $table->json('validation_error')->nullable();
                $table->text('error')->nullable();
                $table->timestamps();
            });
            
            $this->info('   ✅ failed_import_rows table created successfully');
        } else {
            $this->info('   ⚡ failed_import_rows table already exists, checking for required columns...');
            
            $columnsToAdd = [];
            
            // Check if error column exists (additional column for this plugin)
            if (!Schema::hasColumn('failed_import_rows', 'error')) {
                $columnsToAdd[] = 'error';
            }
            
            // Check if validation_error column exists and is the right type
            if (!Schema::hasColumn('failed_import_rows', 'validation_error')) {
                $columnsToAdd[] = 'validation_error';
            }
            
            if (!empty($columnsToAdd)) {
                $this->info('   📝 Adding missing columns: ' . implode(', ', $columnsToAdd));
                
                Schema::table('failed_import_rows', function (Blueprint $table) use ($columnsToAdd) {
                    if (in_array('validation_error', $columnsToAdd)) {
                        $table->json('validation_error')->nullable()->after('import_id');
                    }
                    if (in_array('error', $columnsToAdd)) {
                        $table->text('error')->nullable()->after('validation_error');
                    }
                });
                
                $this->info('   ✅ Missing columns added successfully');
            } else {
                $this->info('   ✅ All required columns already exist');
            }
        }
    }
}
