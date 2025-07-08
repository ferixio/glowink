<?php

namespace App\Filament\User\Resources\ProductStockResource\Pages;

use App\Filament\User\Resources\ProductStockResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductStock extends EditRecord
{
    protected static string $resource = ProductStockResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\DeleteAction::make(),
    //     ];
    // }
}
