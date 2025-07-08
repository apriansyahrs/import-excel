<?php

namespace Apriansyahrs\ImportExcel\Traits;

use Apriansyahrs\ImportExcel\Models\Import;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\Authenticatable;

trait HasImportProgressNotifications
{
    protected function notifyImportProgress(Import $import, Authenticatable $user): void
    {
        if (! $import->processed_rows) {
            return;
        }

        $progress = ($import->processed_rows / $import->total_rows) * 100;

        if ($progress < 100) {
            return;
        }

        $failedRowsCount = $import->getFailedRowsCount();

        Notification::make()
            ->title($import->importer::getCompletedNotificationTitle($import))
            ->body($import->importer::getCompletedNotificationBody($import))
            ->when(
                ! $failedRowsCount,
                fn (Notification $notification) => $notification->success(),
            )
            ->when(
                $failedRowsCount && ($failedRowsCount < $import->total_rows),
                fn (Notification $notification) => $notification->warning(),
            )
            ->when(
                $failedRowsCount === $import->total_rows,
                fn (Notification $notification) => $notification->danger(),
            )
            ->sendToDatabase($user);
    }
}
