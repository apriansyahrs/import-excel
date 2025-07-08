# Fitur Lanjutan

[← Kembali ke README](../README.md) | [← Contoh Implementasi](contoh-implementasi.md) | [Selanjutnya: Testing →](testing.md)

## Custom Job Processing

Plugin menggunakan job `ImportExcel` yang bisa di-extend untuk kebutuhan custom.

### Cara Menggunakan Custom Job

1. Buat custom job yang extends dari `ImportExcel`:

```php
<?php

namespace App\Jobs;

use Apriansyahrs\ImportExcel\Actions\Imports\Jobs\ImportExcel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Notifications\ImportCompletedNotification;

class CustomProductImportJob extends ImportExcel
{
    public function handle(): void
    {
        // Log mulai import
        Log::info('Product import started', [
            'import_id' => $this->importId,
        ]);
        
        // Jalankan proses import parent
        parent::handle();
        
        // Custom post-processing
        $this->sendNotificationToAdmins();
        $this->updateProductCache();
    }
    
    protected function sendNotificationToAdmins(): void
    {
        $import = \Apriansyahrs\ImportExcel\Models\Import::find($this->importId);
        $admins = User::where('role', 'admin')->get();
        
        Notification::send($admins, new ImportCompletedNotification([
            'import_id' => $import->id,
            'imported_rows' => $import->imported_rows,
            'failed_rows' => $import->failed_rows,
        ]));
    }
    
    protected function updateProductCache(): void
    {
        // Clear cache produk setelah import selesai
        cache()->forget('products_list');
        cache()->forget('products_count');
    }
}
```

2. Gunakan custom job di action:

```php
FullImportAction::make()
    ->importer(ProductImporter::class)
    ->job(CustomProductImportJob::class)
```

## Custom Notification

Buat notifikasi custom untuk import:

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ImportCompletedNotification extends Notification
{
    use Queueable;

    protected array $importData;

    public function __construct(array $importData)
    {
        $this->importData = $importData;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Import Data Selesai')
            ->line("Import data dengan ID {$this->importData['import_id']} telah selesai.")
            ->line("Berhasil diimpor: {$this->importData['imported_rows']} baris")
            ->line("Gagal diimpor: {$this->importData['failed_rows']} baris")
            ->action('Lihat Detail', url('/admin/imports/' . $this->importData['import_id']))
            ->line('Terima kasih telah menggunakan aplikasi kami!');
    }

    public function toArray($notifiable): array
    {
        return [
            'import_id' => $this->importData['import_id'],
            'imported_rows' => $this->importData['imported_rows'],
            'failed_rows' => $this->importData['failed_rows'],
            'message' => "Import selesai: {$this->importData['imported_rows']} berhasil, {$this->importData['failed_rows']} gagal",
        ];
    }
}
```

## Event Listeners

⚠️ **Catatan**: Plugin menggunakan event bawaan Filament (`ImportStarted`, `ImportCompleted`).

Anda bisa mendengarkan event Filament:

```php
<?php

namespace App\Listeners;

use Filament\Actions\Imports\Events\ImportCompleted;
use Illuminate\Support\Facades\Log;

class ImportCompletedListener
{
    public function handle(ImportCompleted $event): void
    {
        $import = $event->import;
        
        // Log detail import
        Log::info('Import completed', [
            'import_id' => $import->id,
            'user_id' => $import->user_id,
            'file_name' => $import->file_name,
            'successful_rows' => $import->imported_rows,
            'failed_rows' => $import->failed_rows,
        ]);
        
        // Kirim notifikasi ke Slack jika ada error
        if ($import->failed_rows > 0) {
            $this->sendSlackNotification($import);
        }
    }
    
    protected function sendSlackNotification($import): void
    {
        // Implementasi notifikasi Slack
        // ...
    }
}
```

Daftarkan listener di `EventServiceProvider`:

```php
protected $listen = [
    \Filament\Actions\Imports\Events\ImportCompleted::class => [
        \App\Listeners\ImportCompletedListener::class,
    ],
];
```

## Custom Validation Rules

Buat aturan validasi custom:

```php
<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class IndonesianPhoneNumber implements Rule
{
    public function passes($attribute, $value): bool
    {
        // Validasi format nomor telepon Indonesia
        return preg_match('/^(\+62|62|0)8[1-9][0-9]{6,9}$/', $value);
    }

    public function message(): string
    {
        return 'Format nomor telepon tidak valid. Gunakan format: 08xxxxxxxxxx';
    }
}
```

Gunakan dalam importer:

```php
ImportColumn::make('phone')
    ->requiredMapping()
    ->rules(['required', new IndonesianPhoneNumber()])
    ->transform(function ($value) {
        // Normalisasi format
        $value = preg_replace('/[^0-9+]/', '', $value);
        
        // Konversi ke format standar
        if (str_starts_with($value, '+62')) {
            return '0' . substr($value, 3);
        } elseif (str_starts_with($value, '62')) {
            return '0' . substr($value, 2);
        }
        
        return $value;
    })
```

## Progress Tracking Custom

Plugin sudah menyediakan progress tracking otomatis melalui `HasImportProgressNotifications` trait yang digunakan di job `ImportExcel`.

### Custom Notification Messages

Anda bisa override notification messages di importer class:

```php
<?php

namespace App\Filament\Imports;

use Filament\Actions\Imports\Importer;

class UserImporter extends Importer
{
    protected static ?string $model = User::class;

    // Override notification messages
    public static function getCompletedNotificationTitle($import): string
    {
        return 'Import User Selesai';
    }

    public static function getCompletedNotificationBody($import): string
    {
        $body = 'Import user selesai. ' . number_format($import->imported_rows) . ' berhasil';
        
        if ($failedRowsCount = $import->failed_rows) {
            $body .= ', ' . number_format($failedRowsCount) . ' gagal.';
        } else {
            $body .= '.';
        }
        
        return $body;
    }

    // ... kolom lainnya
}
```

## Batch Processing

Untuk file yang sangat besar, gunakan batch processing dengan chunk size yang sesuai:

```php
FullImportAction::make()
    ->importer(UserImporter::class)
    ->chunkSize(100)        // Proses 100 baris per job
    ->maxRows(10000)        // Batasi maksimal 10,000 baris
    ->headerRow(1)
    ->job(CustomImportJob::class)
```

## Error Handling Lanjutan

### Mengakses Failed Rows

```php
use Apriansyahrs\ImportExcel\Models\Import;
use Apriansyahrs\ImportExcel\Models\FailedImportRow;

// Ambil import tertentu
$import = Import::find(1);

// Ambil semua failed rows
$failedRows = $import->failedRows;

// Ambil failed rows dengan paginasi
$failedRows = $import->failedRows()->paginate(50);

// Export failed rows ke CSV
$csvData = $import->failedRows()
    ->get()
    ->map(function (FailedImportRow $row) {
        return array_merge($row->data, ['error' => $row->error]);
    })
    ->toArray();
```

### Custom Error Handling

```php
<?php

namespace App\Filament\Imports;

use Filament\Actions\Imports\Importer;
use Illuminate\Support\Facades\Log;

class RobustUserImporter extends Importer
{
    protected static ?string $model = User::class;

    public function import($data, $columnMap, $options): ?\Illuminate\Database\Eloquent\Model
    {
        try {
            return parent::import($data, $columnMap, $options);
        } catch (\Exception $exception) {
            // Log error detail
            Log::error('Import error occurred', [
                'row_data' => $data,
                'error' => $exception->getMessage(),
                'importer' => static::class,
            ]);

            // Re-throw untuk dicatat di failed_import_rows
            throw $exception;
        }
    }

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255']),
            
            ImportColumn::make('email')
                ->requiredMapping()
                ->rules(['required', 'email', 'unique:users,email'])
                ->validationMessages([
                    'email.unique' => 'Email sudah terdaftar di sistem.',
                ]),
        ];
    }
}
```

## Multi-Sheet Processing

Plugin mendukung pemilihan sheet untuk file Excel multi-sheet:

```php
FullImportAction::make()
    ->importer(UserImporter::class)
    ->activeSheet(0)    // Sheet pertama (index dimulai dari 0)
    ->headerRow(1)      // Baris pertama sebagai header
```

User akan otomatis melihat dropdown untuk memilih sheet jika file Excel memiliki multiple sheets.

---

[← Kembali ke README](../README.md) | [← Contoh Implementasi](contoh-implementasi.md) | [Selanjutnya: Testing →](testing.md)
