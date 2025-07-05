<?php

namespace App\Filament\Admin\Resources\UserStockisResource\Pages;

use App\Filament\Admin\Resources\UserStockisResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserStockis extends EditRecord
{
    protected static string $resource = UserStockisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
