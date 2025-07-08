<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('imports')) {
            // Buat table baru jika belum ada
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
        } else {
            // Table sudah ada (dari Filament bawaan), tambahkan kolom yang diperlukan
            Schema::table('imports', function (Blueprint $table) {
                if (! Schema::hasColumn('imports', 'imported_rows')) {
                    $table->unsignedInteger('imported_rows')->default(0)->after('successful_rows');
                }
                if (! Schema::hasColumn('imports', 'failed_rows')) {
                    $table->unsignedInteger('failed_rows')->default(0)->after('imported_rows');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imports');
    }
};
