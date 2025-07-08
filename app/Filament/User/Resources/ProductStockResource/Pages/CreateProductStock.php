<?php

namespace App\Filament\User\Resources\ProductStockResource\Pages;

use App\Filament\User\Resources\ProductStockResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProductStock extends CreateRecord
{
    protected static string $resource = ProductStockResource::class;
}
