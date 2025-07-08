# Instalasi

[← Kembali ke README](../README.md) | [Selanjutnya: Penggunaan →](penggunaan.md)

## Persyaratan

- PHP 8.2+
- Laravel 10.0+
- Filament 3.0+

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

## Publikasi Asset

### Migrasi Database

Publikasikan dan jalankan migrasi untuk membuat tabel yang diperlukan:

```bash
php artisan vendor:publish --tag="import-excel-migrations"
php artisan migrate
```

Migrasi akan membuat tabel:
- `imports` - Menyimpan data import
- `failed_import_rows` - Menyimpan baris yang gagal diimpor

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
