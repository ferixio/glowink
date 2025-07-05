<?php

namespace App\Filament\User\Resources\PembeliansResource\Pages;

use App\Filament\User\Resources\PembeliansResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPembelians extends EditRecord
{
    protected static string $resource = PembeliansResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
