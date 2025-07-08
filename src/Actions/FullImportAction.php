<?php

namespace Apriansyahrs\ImportExcel\Actions;

use Apriansyahrs\ImportExcel\Actions\Concerns\CanImportExcelRecords;
use Filament\Actions\ImportAction as ActionsImportAction;

class FullImportAction extends ActionsImportAction
{
    use CanImportExcelRecords;
}
