<?php

namespace App\Filament\Admin\Resources\UserStockisResource\Pages;

use App\Filament\Admin\Resources\UserStockisResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserStockis extends ListRecords
{
    protected static string $resource = UserStockisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
