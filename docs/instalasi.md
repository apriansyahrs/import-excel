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

Setelah install plugin, jalankan command berikut untuk setup database:

```bash
php artisan vendor:publish --tag="import-excel-migrations"
php artisan migrate
```

### 🧠 Intelligent Migration System

Plugin ini menggunakan **intelligent migration system** yang otomatis mendeteksi kondisi database Anda:

#### Skenario 1: Table Belum Ada
```bash
# Jika table imports dan failed_import_rows belum ada
php artisan vendor:publish --tag="import-excel-migrations"
```

**Output:** 
```
2025_07_08_080000_create_imports_table.php
2025_07_08_080001_create_failed_import_rows_table.php
```

Plugin akan generate migration untuk **membuat table baru** dengan struktur lengkap.

#### Skenario 2: Table Sudah Ada (dari Filament)
```bash
# Jika sudah ada table imports dan failed_import_rows dari Filament
php artisan vendor:publish --tag="import-excel-migrations"
```

**Output:**
```
2025_07_08_080000_add_columns_to_imports_table.php
2025_07_08_080001_add_columns_to_failed_import_rows_table.php
```

Plugin akan generate migration untuk **menambah kolom yang diperlukan** saja:
- `imports` table: menambah `imported_rows`, `failed_rows`
- `failed_import_rows` table: menambah `error`

#### Skenario 3: Sudah Setup Lengkap
```bash
# Jika semua table dan kolom sudah ada
php artisan vendor:publish --tag="import-excel-migrations"
```

**Output:** Tidak ada migration yang di-generate karena semua sudah lengkap.

### ✨ Keunggulan Sistem Ini

- ✅ **Smart Detection**: Auto-detect kondisi database
- ✅ **No Conflict**: Tidak akan crash jika table sudah ada
- ✅ **Incremental**: Hanya tambah yang diperlukan
- ✅ **Dynamic Timestamp**: Timestamp selalu current time
- ✅ **Filament Compatible**: 100% kompatibel dengan Filament Import
- ✅ **Zero Configuration**: Tidak perlu config manual

### 📋 Table Structure

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
