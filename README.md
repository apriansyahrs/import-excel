# Import Excel untuk Laravel Filament

Plugin Laravel Filament v3 untuk mengimpor data dari file Excel dengan dukungan multi-format, background processing, dan error handling yang robust.

## ✨ Fitur

- 📊 **Multi-format Support** - .xlsx, .xls, .xlsm, .csv
- ⚡ **Background Processing** - Integrasi Laravel Queue untuk file besar
- 📋 **Multi-sheet Handling** - Pemilihan sheet dinamis
- 🎯 **Smart Column Mapping** - Deteksi header otomatis dan mapping
- 🛡️ **Robust Error Handling** - Tracking baris gagal dengan laporan detail
- 📈 **Progress Tracking** - Notifikasi real-time dan progress updates
- 🔧 **Plug & Play** - Integrasi seamless dengan Filament resources
- 🌍 **Multi-language** - Dukungan terjemahan bahasa Indonesia dan Inggris
- 🗃️ **Custom Models** - Extends Filament Import models dengan fitur tambahan
- 🎛️ **Configurable** - Chunk size, max rows, header row, dan sheet selection

## 🚀 Quick Start

### Instalasi

Karena plugin belum di-publish ke Packagist, install dari GitHub:

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

> **Catatan**: 
> - Jika ada error "minimum-stability", tambahkan `"minimum-stability": "dev"` ke `composer.json` atau gunakan flag `--with-dependencies`
> - Plugin akan tersedia di Packagist setelah stable release

### Setup Database

**Smart Publish (Direkomendasikan)** ⭐

```bash
# Otomatis detect dan publish migration yang diperlukan
php artisan import-excel:publish-migrations

# Jalankan migration
php artisan migrate
```

**Traditional Publish (Manual)**

```bash
# Publish semua opsi migration
php artisan vendor:publish --tag="import-excel-migrations"

# Pilih dan jalankan migration sesuai kondisi database
php artisan migrate
```

**🧠 Smart Publish Features:**
- **Cek database** saat ini otomatis
- **Hanya publish** migration yang benar-benar diperlukan  
- **Feedback jelas** tentang apa yang akan dilakukan
- **No waste** - tidak ada file migration yang tidak perlu

> Plugin ini 100% kompatibel dengan setup Filament Import yang sudah ada!

### Buat Importer Class

```php
<?php

namespace App\Filament\Imports;

use App\Models\User;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;

class UserImporter extends Importer
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')->requiredMapping(),
            ImportColumn::make('email')->requiredMapping(),
        ];
    }

    public function resolveRecord(): ?User
    {
        return new User();
    }

    public static function getLabel(): string
    {
        return 'User';
    }
}
```

### Tambahkan ke Resource

```php
use Apriansyahrs\ImportExcel\Actions\FullImportAction;

protected function getHeaderActions(): array
{
    return [
        FullImportAction::make()
            ->importer(UserImporter::class),
    ];
}
```

## 🏗️ Arsitektur Plugin

Plugin ini menggunakan:

- **Namespace**: `Apriansyahrs\ImportExcel`
- **Service Provider**: `ImportExcelServiceProvider`
- **Models**: 
  - `Import` - Extends dari `Filament\Actions\Imports\Models\Import`
  - `FailedImportRow` - Model khusus untuk tracking error
- **Job**: `ImportExcel` - Background processing dengan queue
- **Translation Namespace**: `import-excel`

## 📚 Dokumentasi

- [Instalasi](docs/instalasi.md) - Panduan instalasi dan konfigurasi
- [Penggunaan](docs/penggunaan.md) - Cara menggunakan plugin
- [Contoh Implementasi](docs/contoh-implementasi.md) - Contoh kode lengkap
- [Fitur Lanjutan](docs/fitur-lanjutan.md) - Kustomisasi dan fitur advanced
- [Testing](docs/testing.md) - Panduan testing
- [Panduan Development](docs/panduan-development.md) - Untuk developer
- [CHANGELOG](CHANGELOG.md) - Release notes dan perubahan versi

## 🤝 Contributing

Kami sangat menyambut kontribusi dari komunitas! Silakan baca:

- [CONTRIBUTING.md](CONTRIBUTING.md) - Panduan kontribusi dalam bahasa Indonesia
- [Template Issue](.github/ISSUE_TEMPLATE/) - Template laporan bug dan permintaan fitur
- [Template PR](.github/pull_request_template.md) - Template Pull Request

### Quick Contributing Guide

1. Fork repository
2. Create feature branch: `git checkout -b feature/amazing-feature`
3. Commit changes: `git commit -m 'feat: add amazing feature'`
4. Push branch: `git push origin feature/amazing-feature`
5. Open Pull Request

## 🛠️ Persyaratan

- PHP 8.2+
- Laravel 10.0+
- Filament 3.0+

### Dependencies

Plugin ini menggunakan dependencies berikut:

- `phpoffice/phpspreadsheet` ^1.27 - Untuk membaca dan memproses file Excel
- `spatie/laravel-package-tools` ^1.15.0 - Untuk struktur package Laravel

### Queue Requirements

Untuk file Excel yang besar (>100 baris), sangat disarankan menggunakan:

- Redis atau Database queue driver
- Supervisor untuk menjalankan queue worker
- Minimum 512MB memory limit untuk PHP

## 🎯 Use Cases

- Migrasi data dari sistem lama
- Import bulk user/customer data
- Upload katalog produk
- Import data keuangan
- Semua kebutuhan import Excel skala besar

## 🔧 Troubleshooting

### Memory Issues
```php
// Untuk file besar, sesuaikan chunk size
FullImportAction::make()
    ->importer(UserImporter::class)
    ->chunkSize(50) // Kurangi jika memory error
```

### Queue Not Processing
```bash
# Pastikan queue worker berjalan
php artisan queue:work

# Check queue connection di .env
QUEUE_CONNECTION=database
```

### Import Stuck
```php
// Check failed jobs
php artisan queue:failed

// Retry failed jobs
php artisan queue:retry all
```

## ❓ FAQ

**Q: Apakah mendukung file Excel dengan password?**  
A: Tidak, file harus tidak terproteksi password.

**Q: Berapa maksimal ukuran file yang bisa diproses?**  
A: Tergantung konfigurasi server. Disarankan maksimal 10MB. File besar diproses di background.

**Q: Bagaimana cara melihat progress import?**  
A: Plugin otomatis menampilkan notifikasi progress via Filament notifications.

**Q: Bisa import data dengan relasi?**  
A: Ya, gunakan `resolveRecord()` method untuk handle relasi. Lihat [Contoh Implementasi](docs/contoh-implementasi.md).

## 📄 License

MIT License. Lihat [License File](LICENSE) untuk informasi lengkap.
