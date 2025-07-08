<?php

namespace App\Filament\User\Resources\ProductStockResource\Pages;

use App\Filament\User\Resources\ProductStockResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductStocks extends ListRecords
{
    protected static string $resource = ProductStockResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\CreateAction::make(),
    //     ];
    // }
}
