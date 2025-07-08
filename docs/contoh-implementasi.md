# Contoh Implementasi

[← Kembali ke README](../README.md) | [← Penggunaan](penggunaan.md) | [Selanjutnya: Fitur Lanjutan →](fitur-lanjutan.md)

## Import User Sederhanatoh Implementasi

[← Kembali ke Dokumentasi](../README.md) | [← Penggunaan](penggunaan.md) | [Selanjutnya: Fitur Lanjutan →](fitur-lanjutan.md)

## Import Us---

[← Kembali ke README](../README.md) | [← Penggunaan](penggunaan.md) | [Selanjutnya: Fitur Lanjutan →](fitur-lanjutan.md)Sederhana

Contoh lengkap untuk mengimpor data user dari file Excel:

### 1. Buat Importer Class

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
                ->guess(['nama', 'full_name', 'fullname'])
                ->rules(['required', 'string', 'max:255']),
            
            ImportColumn::make('email')
                ->requiredMapping()
                ->guess(['email', 'alamat_email'])
                ->rules(['required', 'email', 'unique:users,email', 'max:255'])
                ->validationMessages([
                    'email.unique' => 'Email sudah terdaftar.',
                    'email.email' => 'Format email tidak valid.',
                ]),
            
            ImportColumn::make('password')
                ->requiredMapping()
                ->guess(['password', 'kata_sandi'])
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

### 2. Tambahkan ke Resource

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
                ->importer(UserImporter::class)
                ->headerRow(1)
                ->chunkSize(50),
        ];
    }
}
```

## Import Product dengan Kategori

Contoh import produk dengan relasi kategori:

```php
<?php

namespace App\Filament\Imports;

use App\Models\Product;
use App\Models\Category;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;

class ProductImporter extends Importer
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),
            
            ImportColumn::make('price')
                ->requiredMapping()
                ->rules(['required', 'numeric', 'min:0'])
                ->transform(function ($value) {
                    // Hapus karakter non-numeric kecuali titik
                    return preg_replace('/[^0-9.]/', '', $value);
                }),
            
            ImportColumn::make('category_name')
                ->requiredMapping()
                ->guess(['kategori', 'category', 'category_name'])
                ->rules(['required', 'string', 'max:255']),
            
            ImportColumn::make('stock')
                ->requiredMapping()
                ->rules(['required', 'integer', 'min:0'])
                ->transform(function ($value) {
                    return (int) $value;
                }),
        ];
    }

    public function resolveRecord(): ?Product
    {
        // Cari atau buat kategori berdasarkan nama
        $categoryName = $this->data['category_name'] ?? null;
        $category = null;
        
        if ($categoryName) {
            $category = Category::firstOrCreate([
                'name' => $categoryName
            ]);
        }
        
        // Buat instance Product baru
        $product = new Product();
        
        // Set category_id jika kategori ditemukan
        if ($category) {
            $product->category_id = $category->id;
        }
        
        return $product;
    }

    public static function getLabel(): string
    {
        return 'Product';
    }
}
```

## Import dengan Update Data Existing

⚠️ **Catatan Penting**: Untuk update existing records, Filament Importer menggunakan mekanisme yang berbeda. Data diakses melalui parameter di method `import()` bukan melalui `$this->data`.

Contoh untuk notification custom:

```php
<?php

namespace App\Filament\Imports;

use App\Models\Customer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;

class CustomerImporter extends Importer
{
    protected static ?string $model = Customer::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('email')
                ->requiredMapping()
                ->rules(['required', 'email', 'max:255']),
            
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),
            
            ImportColumn::make('phone')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:20'])
                ->transform(function ($value) {
                    // Normalisasi format telepon
                    return preg_replace('/[^0-9+]/', '', $value);
                }),
        ];
    }

    public function resolveRecord(): ?Customer
    {
        // Untuk update existing records, gunakan firstOrNew
        // Data akan diakses saat proses import berlangsung
        return new Customer();
    }
    
    public static function getCompletedNotificationBody($import): string
    {
        $body = 'Import customer selesai. ' . number_format($import->successful_rows) . ' berhasil';
        
        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ', ' . number_format($failedRowsCount) . ' gagal.';
        } else {
            $body .= '.';
        }
        
        return $body;
    }

    public static function getLabel(): string
    {
        return 'Customer';
    }
}
```

## Import dengan Validasi Custom

Contoh dengan validasi yang lebih kompleks:

```php
<?php

namespace App\Filament\Imports;

use App\Models\Employee;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Carbon\Carbon;

class EmployeeImporter extends Importer
{
    protected static ?string $model = Employee::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('employee_id')
                ->requiredMapping()
                ->rules([
                    'required',
                    'string',
                    'unique:employees,employee_id',
                    'regex:/^EMP[0-9]{4}$/'
                ])
                ->validationMessages([
                    'employee_id.regex' => 'Format ID karyawan harus EMP0000.',
                    'employee_id.unique' => 'ID karyawan sudah terdaftar.',
                ]),
            
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),
            
            ImportColumn::make('birth_date')
                ->requiredMapping()
                ->rules(['required', 'date', 'before:today'])
                ->transform(function ($value) {
                    try {
                        return Carbon::parse($value)->format('Y-m-d');
                    } catch (\Exception $e) {
                        return null;
                    }
                }),
            
            ImportColumn::make('salary')
                ->requiredMapping()
                ->rules(['required', 'numeric', 'min:1000000'])
                ->transform(function ($value) {
                    // Hapus karakter non-numeric
                    return preg_replace('/[^0-9.]/', '', $value);
                }),
        ];
    }

    public function resolveRecord(): ?Employee
    {
        return new Employee();
    }

    public static function getLabel(): string
    {
        return 'Employee';
    }
}
```

## Import dengan Multiple Sheet

Konfigurasi untuk file Excel dengan multiple sheet:

```php
FullImportAction::make()
    ->importer(UserImporter::class)
    ->activeSheet(0) // Sheet pertama sebagai default
    ->headerRow(1)
    ->chunkSize(100)
    ->fileValidationRules([
        'required',
        'file',
        'mimes:xlsx,xls,csv',
        'max:5120' // Maksimal 5MB
    ])
```

## Konfigurasi Lanjutan

Contoh konfigurasi lengkap dengan berbagai opsi:

```php
FullImportAction::make()
    ->importer(UserImporter::class)
    ->headerRow(2)                 // Baris kedua sebagai header
    ->activeSheet(0)               // Sheet pertama
    ->chunkSize(50)                // 50 baris per job
    ->maxRows(1000)                // Maksimal 1000 baris
    ->options([
        'update_existing' => true,
        'send_notifications' => true,
    ])
    ->fileValidationRules([
        'required',
        'file',
        'mimes:xlsx,xls,csv',
        'max:10240' // Maksimal 10MB
    ])
```

---

[← Kembali ke README](../README.md) | [← Penggunaan](penggunaan.md) | [Selanjutnya: Fitur Lanjutan →](fitur-lanjutan.md)
