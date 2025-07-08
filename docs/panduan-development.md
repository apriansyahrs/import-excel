# Panduan Development

[← Kembali ke README](../README.md) | [← Testing](testing.md)

## Setup Development Environment

### 1. Clone Repository

```bash
git clone https://github.com/apriansyahrs/import-excel.git
cd import-excel
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Setup Testing Database

Plugin menggunakan SQLite in-memory untuk testing:

```bash
# File .env.testing sudah dikonfigurasi
cp .env.example .env.testing
```

### 4. Jalankan Test

```bash
./vendor/bin/pest
```

## Struktur Kode

### Namespace

Plugin menggunakan namespace `Apriansyahrs\ImportExcel`:

```
src/
├── ImportExcelServiceProvider.php   # Service provider utama
├── Actions/
│   ├── FullImportAction.php         # Action utama untuk import
│   └── Imports/
│       └── Jobs/
│           └── ImportExcel.php      # Job untuk background processing
├── Models/
│   ├── Import.php                   # Model import
│   └── FailedImportRow.php          # Model untuk row yang gagal
└── Traits/
    └── HasImportProgressNotifications.php  # Trait untuk notifikasi
```

### Database Schema

Plugin menggunakan 2 tabel utama:

1. **imports** - Menyimpan data import
2. **failed_import_rows** - Menyimpan row yang gagal diimpor

## Code Style

### PHP CS Fixer

Plugin menggunakan PHP CS Fixer untuk code formatting:

```bash
# Format kode
./vendor/bin/pint

# Check style tanpa fix
./vendor/bin/pint --test
```

### PHPStan

Untuk static analysis:

```bash
# Jalankan PHPStan
./vendor/bin/phpstan analyse

# Level 5 analysis pada src dan tests
```

## Testing Guidelines

### 1. Test Coverage

Pastikan semua fitur baru memiliki test coverage:

```bash
./vendor/bin/pest --coverage
```

### 2. Test Structure

- **Unit Tests** - Test individual class/method
- **Feature Tests** - Test integration antar komponen
- **Helper Classes** - Utilities untuk membuat test data

### 3. Naming Convention

```php
// Test method naming
it('can import users from excel file')
it('handles validation errors correctly')
it('processes large files in background')
```

## Contributing Workflow

### 1. Branch Naming

```bash
# Feature branch
feature/add-custom-validation

# Bug fix branch
fix/import-progress-notification

# Documentation branch
docs/update-installation-guide
```

### 2. Commit Convention

```bash
# Format: type(scope): description
feat(import): add custom validation rules
fix(notification): fix progress tracking
docs(readme): update installation steps
test(unit): add test for failed import rows
```

### 3. Pull Request Process

1. Fork repository
2. Buat feature branch
3. Implement changes dengan test
4. Pastikan semua test pass
5. Submit pull request dengan deskripsi lengkap

## Architecture Overview

### Import Flow

1. **User Upload** - User upload file Excel via Filament action
2. **File Validation** - Validasi format dan ukuran file
3. **Sheet Selection** - Pilih sheet jika multiple sheets
4. **Column Mapping** - Map kolom Excel ke field database
5. **Data Processing** - Proses data dengan validasi
6. **Background Job** - Large files diproses di background
7. **Error Handling** - Failed rows disimpan ke database
8. **Notification** - User mendapat notifikasi hasil import

### Key Components

1. **FullImportAction** - Main action class untuk Filament
2. **ImportExcel Job** - Background job processing
3. **Import Model** - Model untuk tracking import
4. **FailedImportRow Model** - Model untuk failed rows

## Extending the Plugin

### Custom Import Action

Buat custom action yang extend dari base class:

```php
<?php

namespace App\Actions;

use Apriansyahrs\ImportExcel\Actions\FullImportAction;

class CustomImportAction extends FullImportAction
{
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->label('Custom Import')
             ->icon('heroicon-o-arrow-up-tray')
             ->color('success');
    }
    
    protected function beforeImport(): void
    {
        // Custom logic sebelum import
    }
    
    protected function afterImport(): void
    {
        // Custom logic setelah import
    }
}
```

### Custom Import Job

Override job untuk processing custom:

```php
<?php

namespace App\Jobs;

use Apriansyahrs\ImportExcel\Actions\Imports\Jobs\ImportExcel;

class CustomImportJob extends ImportExcel
{
    public function handle(): void
    {
        // Pre-processing
        $this->logImportStart();
        
        // Jalankan parent processing
        parent::handle();
        
        // Post-processing
        $this->sendCustomNotification();
    }
}
```

## Debugging

### 1. Enable Debug Mode

Di file `.env`:

```env
APP_DEBUG=true
LOG_LEVEL=debug
```

### 2. Log Import Process

Plugin otomatis log proses import ke Laravel log. Check file:

```bash
tail -f storage/logs/laravel.log
```

### 3. Debug Failed Imports

Query failed import rows:

```php
use Apriansyahrs\ImportExcel\Models\FailedImportRow;

$failedRows = FailedImportRow::where('import_id', $importId)->get();
foreach ($failedRows as $row) {
    dump($row->data, $row->validation_error);
}
```

## Performance Optimization

### 1. Chunk Size

Sesuaikan chunk size berdasarkan server:

```php
// Untuk server dengan resource terbatas
->chunkSize(50)

// Untuk server dengan resource besar
->chunkSize(200)
```

### 2. Queue Configuration

Gunakan Redis untuk queue yang lebih performant:

```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 3. Database Optimization

Pastikan index database optimal:

```sql
-- Index untuk import lookup
CREATE INDEX idx_imports_user_id ON imports(user_id);
CREATE INDEX idx_imports_created_at ON imports(created_at);

-- Index untuk failed rows
CREATE INDEX idx_failed_import_rows_import_id ON failed_import_rows(import_id);
```

---

[← Kembali ke README](../README.md) | [← Testing](testing.md)
