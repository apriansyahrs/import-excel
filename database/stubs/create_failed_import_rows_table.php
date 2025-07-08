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
        if (! Schema::hasTable('failed_import_rows')) {
            // Buat table baru jika belum ada
            Schema::create('failed_import_rows', function (Blueprint $table) {
                $table->id();
                $table->json('data');
                $table->foreignId('import_id')->constrained()->cascadeOnDelete();
                $table->json('validation_error')->nullable(); // Gunakan nama kolom yang sama dengan Filament
                $table->text('error')->nullable(); // Kolom tambahan untuk error lainnya
                $table->timestamps();
            });
        } else {
            // Table sudah ada (dari Filament bawaan), tambahkan kolom yang diperlukan
            Schema::table('failed_import_rows', function (Blueprint $table) {
                // Tambahkan kolom error jika belum ada (Filament bawaan hanya punya validation_error)
                if (! Schema::hasColumn('failed_import_rows', 'error')) {
                    $table->text('error')->nullable()->after('validation_error');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('failed_import_rows');
    }
};
