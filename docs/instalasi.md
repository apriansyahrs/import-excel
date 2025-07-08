# Instalasi

[← Kembali ke README](../README.md) | [Selanjutnya: Penggunaan →](penggunaan.md)

## Persyaratan

- PHP 8.2+
- Laravel 10.0+
- Filament 3.0+

## Kompatibilitas dengan Filament Import

**Plugin ini 100% kompatibel dengan Filament Import bawaan!**

Jika Anda sudah memiliki table `imports` dan `failed_import_rows` dari setup Filament sebelumnya, migrasi plugin ini akan:

- ✅ **Mendeteksi table yang sudah ada** dan hanya menambahkan kolom yang diperlukan
- ✅ **Tidak menimpa** struktur table existing
- ✅ **Menggunakan nama kolom yang sama** dengan Filament bawaan (`validation_error` singular)
- ✅ **Menambahkan backward compatibility** untuk code yang menggunakan `validation_errors` (plural)

### Struktur Table yang Didukung

**Table `imports`:**
```sql
-- Bawaan Filament + kolom tambahan:
id, completed_at, file_name, file_path, importer, 
processed_rows, total_rows, successful_rows, user_id,
imported_rows, failed_rows, timestamps
```

**Table `failed_import_rows`:**
```sql
-- Bawaan Filament + kolom tambahan:
id, data, import_id, validation_error, error, timestamps
```

## Instalasi Plugin

### Dari GitHub Repository (Recommended untuk saat ini)

Karena plugin belum di-publish ke Packagist, install dari GitHub repository:

1. Tambahkan repository ke `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/apriansyahrs/import-excel.git"
        }
    ]
}
```

2. Install plugin:

```bash
composer require apriansyahrs/import-excel:dev-main
```

> **Catatan**: Gunakan `dev-main` untuk branch main, atau tentukan tag/branch tertentu seperti `v1.0.0` jika tersedia.

### Untuk Development/Testing

Jika ingin development/testing dari local clone:

```bash
# Clone repository
git clone https://github.com/apriansyahrs/import-excel.git

# Di composer.json project Laravel Anda
{
    "repositories": [
        {
            "type": "path",
            "url": "../path/to/import-excel"
        }
    ]
}

# Install dari path local
composer require apriansyahrs/import-excel:@dev
```

### Dari Packagist (Coming Soon)

Setelah plugin di-publish ke Packagist:

```bash
composer require apriansyahrs/import-excel
```

Plugin akan otomatis terdaftar melalui Laravel package discovery menggunakan `Apriansyahrs\ImportExcel\ImportExcelServiceProvider`.

## Update Plugin

### Update dari GitHub

Untuk mendapatkan update terbaru dari GitHub:

```bash
composer update apriansyahrs/import-excel
```

### Troubleshooting Installation

**Error "minimum-stability":**
```json
{
    "minimum-stability": "dev",
    "prefer-stable": true
}
```

**Error "VCS repository":**
Pastikan Git dapat mengakses repository. Untuk private repository, setup SSH key atau access token.

**Cache Issues:**
```bash
composer clear-cache
composer update apriansyahrs/import-excel --no-cache
```

## Setup Database 

Setelah install plugin, ada **2 cara** untuk setup database:

### 🧠 Cara 1: Smart Publish (Direkomendasikan) ⭐

```bash
php artisan import-excel:publish-migrations
```

Command ini akan:
1. **Cek kondisi database** saat ini
2. **Hanya publish migration** yang benar-benar diperlukan
3. **Memberikan feedback** apa yang akan dilakukan

**Contoh output untuk kasus table sudah ada:**
```
🔍 Checking current database state...
   ✅ imports table: OK (all columns exist)
   📋 failed_import_rows table: EXISTS → will ADD columns: error
📝 Publishing required migrations...
✅ Successfully published migrations:
   - 2025_07_08_154731_add_columns_to_failed_import_rows_table.php
💡 Next step: Run php artisan migrate
```

**Contoh output untuk fresh install:**
```
🔍 Checking current database state...
   📋 imports table: NOT EXISTS → will CREATE
   📋 failed_import_rows table: NOT EXISTS → will CREATE
📝 Publishing required migrations...
✅ Successfully published migrations:
   - 2025_07_08_154731_create_imports_table.php
   - 2025_07_08_154732_create_failed_import_rows_table.php
💡 Next step: Run php artisan migrate
```

### 📋 Cara 2: Traditional Publish (Manual)

```bash
php artisan vendor:publish --tag="import-excel-migrations"
```

Command ini akan publish **semua opsi migration** (4 files), lalu Anda pilih mana yang perlu dijalankan.

### 🚀 Jalankan Migration

Setelah publish migration (dengan cara manapun):

```bash
php artisan migrate
```

### ✨ Keunggulan Smart Publish

- ✅ **Intelligent**: Hanya publish yang diperlukan
- ✅ **Clean**: Tidak ada file migration yang tidak perlu
- ✅ **User-friendly**: Feedback yang jelas
- ✅ **Fast**: Langsung bisa `php artisan migrate`
- ✅ **Safe**: Tidak akan publish ulang jika sudah lengkap

### 📋 Table Structure Setelah Setup

**Table `imports`:**
```sql
-- Struktur lengkap setelah setup
id, completed_at, file_name, file_path, importer,
processed_rows, total_rows, successful_rows, user_id,
imported_rows, failed_rows, timestamps
```

**Table `failed_import_rows`:**
```sql  
-- Struktur lengkap setelah setup
id, data, import_id, validation_error, error, timestamps
```

### File Terjemahan (Opsional)

Publikasikan file terjemahan jika ingin menyesuaikan pesan:

```bash
php artisan vendor:publish --tag="import-excel-translations"
```

## Konfigurasi Queue (Direkomendasikan)

Untuk file Excel yang besar, konfigurasikan Laravel Queue:

### 1. Atur Driver Queue

Di file `.env`:

```env
QUEUE_CONNECTION=database
# atau gunakan Redis untuk performa lebih baik
# QUEUE_CONNECTION=redis
```

### 2. Jalankan Queue Worker

```bash
php artisan queue:work
```

Atau untuk production dengan supervisor:

```bash
php artisan queue:work --queue=default --sleep=3 --tries=3
```

## Verifikasi Instalasi

Setelah instalasi, plugin siap digunakan tanpa konfigurasi tambahan. Tabel migrasi akan otomatis dibuat saat menjalankan `php artisan migrate`.

---

[← Kembali ke README](../README.md) | [Selanjutnya: Penggunaan →](penggunaan.md)
