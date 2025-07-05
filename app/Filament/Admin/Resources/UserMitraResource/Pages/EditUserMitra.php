<?php

namespace App\Filament\Admin\Resources\UserMitraResource\Pages;

use App\Filament\Admin\Resources\UserMitraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserMitra extends EditRecord
{
    protected static string $resource = UserMitraResource::class;

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
