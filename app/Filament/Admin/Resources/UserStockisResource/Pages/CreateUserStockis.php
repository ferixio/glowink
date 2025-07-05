<?php

namespace App\Filament\Admin\Resources\UserStockisResource\Pages;

use App\Filament\Admin\Resources\UserStockisResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUserStockis extends CreateRecord
{
    protected static string $resource = UserStockisResource::class;

           protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
