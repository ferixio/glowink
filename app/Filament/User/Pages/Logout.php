<?php

namespace App\Filament\User\Pages;

use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Logout extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-arrow-left-start-on-rectangle';

    protected static string $view = 'filament.user.pages.logout';

    protected static ?int $navigationSort = 10;

    public $defaultAction = 'showModal';

    public function getTitle(): string
    {
        return '';
    }
    public function showModal()
    {
        return Action::make('logout')
            ->color('danger')
            ->label('Logout')
            ->requiresConfirmation()
            ->action(function () {
                Auth::logout();
                return redirect('/');
            });
    }
}
