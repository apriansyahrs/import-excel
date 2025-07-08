# Testing

[← Kembali ke README](../README.md) | [← Fitur Lanjutan](fitur-lanjutan.md) | [Selanjutnya: Panduan Development →](panduan-development.md)

## Menjalankan Test

### Test Lengkap

Jalankan semua test:

```bash
./vendor/bin/pest
```

### Test Spesifik

Jalankan test untuk feature tertentu:

```bash
# Test unit saja
./vendor/bin/pest tests/Unit

# Test feature saja
./vendor/bin/pest tests/Feature

# Test file spesifik
./vendor/bin/pest tests/Feature/FullImportActionTest.php
```

### Test dengan Coverage

Jalankan test dengan coverage report:

```bash
./vendor/bin/pest --coverage
```

## Struktur Test

```
tests/
├── Feature/                         # Integration tests
│   ├── CanImportExcelRecordsTest.php
│   ├── ExcelImportIntegrationTest.php
│   └── FullImportActionTest.php
├── Unit/                            # Unit tests
│   ├── ImportModelTest.php
│   ├── FailedImportRowTest.php
│   ├── ImportExcelJobTest.php
│   └── SimpleImportTest.php
├── Helpers/                         # Test utilities
│   └── ExcelTestHelper.php
├── Importers/                       # Test importers
│   └── TestUserImporter.php
├── Models/                          # Test models
│   └── User.php
└── TestCase.php                     # Base test case
```

## Membuat Test File Excel

Gunakan helper untuk membuat file Excel test:

```php
use Tests\Helpers\ExcelTestHelper;

$filePath = ExcelTestHelper::createTestFile([
    ['name', 'email', 'password'],
    ['John Doe', 'john@example.com', 'password123'],
    ['Jane Doe', 'jane@example.com', 'password456'],
]);
```

## Test Importer

Contoh test untuk importer class:

```php
<?php

use App\Filament\Imports\UserImporter;
use Tests\Helpers\ExcelTestHelper;

it('can import users from excel file', function () {
    // Buat file Excel test
    $filePath = ExcelTestHelper::createTestFile([
        ['name', 'email', 'password'],
        ['John Doe', 'john@example.com', 'password123'],
        ['Jane Doe', 'jane@example.com', 'password456'],
    ]);

    // Buat importer instance
    $importer = new UserImporter();
    
    // Test import
    $result = $importer->import($filePath);
    
    expect($result->successful_rows)->toBe(2);
    expect($result->failed_rows)->toBe(0);
    
    // Verifikasi data tersimpan
    expect(\App\Models\User::count())->toBe(2);
    expect(\App\Models\User::where('email', 'john@example.com')->exists())->toBeTrue();
});
```

## Test Validasi Error

Test untuk memastikan validasi bekerja dengan benar:

```php
it('handles validation errors correctly', function () {
    // File dengan data invalid
    $filePath = ExcelTestHelper::createTestFile([
        ['name', 'email', 'password'],
        ['', 'invalid-email', 'short'], // Data invalid
        ['Valid Name', 'valid@example.com', 'validpassword'],
    ]);

    $importer = new UserImporter();
    $result = $importer->import($filePath);
    
    expect($result->successful_rows)->toBe(1);
    expect($result->failed_rows)->toBe(1);
    
    // Verifikasi failed row tersimpan
    expect(\Apriansyahrs\ImportExcel\Models\FailedImportRow::count())->toBe(1);
});
```

## Performance Testing

Test untuk memastikan performa:

```php
it('imports large datasets efficiently', function () {
    $startTime = microtime(true);
    
    // Buat dataset besar
    $data = [['name', 'email', 'password']];
    for ($i = 1; $i <= 5000; $i++) {
        $data[] = ["User {$i}", "user{$i}@example.com", 'password123'];
    }
    
    $filePath = ExcelTestHelper::createTestFile($data);
    
    $importer = new UserImporter();
    $result = $importer->import($filePath);
    
    $executionTime = microtime(true) - $startTime;
    
    expect($result->successful_rows)->toBe(5000);
    expect($executionTime)->toBeLessThan(30); // Maksimal 30 detik
});
```

---

[← Kembali ke README](../README.md) | [← Fitur Lanjutan](fitur-lanjutan.md) | [Selanjutnya: Panduan Development →](panduan-development.md)
