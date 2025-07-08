<?php

namespace App\Filament\Admin\Resources\ApprovePembelianStockisResource\Pages;

use App\Filament\Admin\Resources\ApprovePembelianStockisResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApprovePembelianStockis extends ListRecords
{
    protected static string $resource = ApprovePembelianStockisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
