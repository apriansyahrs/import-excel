# Penggunaan

[← Kembali ke README](../README.md) | [← Instalasi](instalasi.md) | [Selanjutnya: Contoh Implementasi →](contoh-implementasi.md)

## Tentang Models Plugin

Plugin ini menggunakan model custom yang extends dari Filament's base models:

- `Apriansyahrs\ImportExcel\Models\Import` - Extends dari `Filament\Actions\Imports\Models\Import`
- `Apriansyahrs\ImportExcel\Models\FailedImportRow` - Model khusus untuk tracking baris yang gagal

Plugin menggunakan namespace `import-excel` untuk sistem terjemahan.

## Membuat Importer Class

Langkah pertama adalah membuat class importer yang mendefinisikan bagaimana data Excel akan diproses:

```php
<?php

namespace App\Filament\Imports;

use App\Models\User;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Illuminate\Support\Facades\Hash;

class UserImporter extends Importer
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),
            ImportColumn::make('email')
                ->requiredMapping()
                ->rules(['required', 'email', 'unique:users,email']),
            ImportColumn::make('password')
                ->requiredMapping()
                ->rules(['required', 'string', 'min:8']),
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

## Menambahkan ke Resource Filament

Tambahkan action import ke resource Filament Anda:

```php
<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Imports\UserImporter;
use Apriansyahrs\ImportExcel\Actions\FullImportAction;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            FullImportAction::make()
                ->importer(UserImporter::class),
        ];
    }
}
```

## Konfigurasi Dasar

### Mengatur Header Row

Tentukan baris mana yang berisi header dalam file Excel:

```php
FullImportAction::make()
    ->importer(UserImporter::class)
    ->headerRow(2) // Gunakan baris kedua sebagai header
```

### Mengatur Sheet Aktif

Pilih sheet yang akan digunakan secara default:

```php
FullImportAction::make()
    ->importer(UserImporter::class)
    ->activeSheet(0) // Sheet pertama (index 0)
```

### Mengatur Chunk Size

Tentukan jumlah baris yang diproses per job:

```php
FullImportAction::make()
    ->importer(UserImporter::class)
    ->chunkSize(100) // Proses 100 baris per job
```

## Validasi Data

### Aturan Validasi

Tambahkan aturan validasi pada setiap kolom:

```php
ImportColumn::make('email')
    ->requiredMapping()
    ->rules([
        'required',
        'email',
        'unique:users,email',
        'max:255'
    ])
```

### Custom Validation Messages

Buat pesan validasi custom:

```php
ImportColumn::make('email')
    ->requiredMapping()
    ->rules(['required', 'email', 'unique:users,email'])
    ->validationMessages([
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email sudah terdaftar.',
    ])
```

## Transformasi Data

### Mengubah Data Sebelum Validasi

```php
ImportColumn::make('phone')
    ->requiredMapping()
    ->rules(['required', 'string'])
    ->example('08123456789')
    ->transform(function ($value) {
        // Hapus spasi dan karakter khusus
        return preg_replace('/[^0-9]/', '', $value);
    })
```

### Menggunakan Guess Column

Untuk mencocokkan kolom secara otomatis:

```php
ImportColumn::make('name')
    ->requiredMapping()
    ->guess(['nama', 'full_name', 'fullname'])
    ->rules(['required', 'string', 'max:255'])
```

## Progress Tracking

Plugin otomatis menampilkan progress import dengan notifikasi real-time. Untuk import besar, proses akan dijalankan di background menggunakan Laravel Queue.

Pastikan queue worker berjalan:

```bash
php artisan queue:work
```

## Format File yang Didukung

Plugin mendukung format file berikut:

- `.xlsx` - Excel 2007+ XML Format
- `.xls` - Excel 97-2003 Binary Format
- `.xlsm` - Excel 2007+ Macro-Enabled XML Format
- `.csv` - CSV Format

## Penanganan Error

Plugin otomatis menangani error dengan:

- Menyimpan baris yang gagal ke database (`failed_import_rows` table)
- Menampilkan pesan error yang detail
- Melanjutkan proses import meskipun ada error
- Memberikan laporan lengkap setelah import selesai

### Mengakses Failed Rows

Anda dapat mengakses baris yang gagal melalui relasi model:

```php
use Apriansyahrs\ImportExcel\Models\Import;

$import = Import::find(1);
$failedRows = $import->failedRows; // Collection of FailedImportRow
$failedCount = $import->getFailedRowsCount(); // Integer count
```

---

[← Kembali ke README](../README.md) | [← Instalasi](instalasi.md) | [Selanjutnya: Contoh Implementasi →](contoh-implementasi.md)
