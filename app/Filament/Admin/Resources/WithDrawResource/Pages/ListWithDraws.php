<?php

namespace App\Filament\Admin\Resources\WithDrawResource\Pages;

use App\Filament\Admin\Resources\WithDrawResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWithDraws extends ListRecords
{
    protected static string $resource = WithDrawResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
