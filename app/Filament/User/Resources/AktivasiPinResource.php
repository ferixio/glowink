<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\AktivasiPinResource\Pages;
use App\Models\AktivasiPin;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AktivasiPinResource extends Resource
{
    protected static ?string $model = AktivasiPin::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pin')->label('PIN')->searchable(),
                Tables\Columns\TextColumn::make('produk.nama')->label('Produk')->searchable(),
                Tables\Columns\IconColumn::make('is_accept')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                ,

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('accept')
                    ->label('Terima')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->button()
                    ->action(function (AktivasiPin $record) {
                        $record->update(['is_accepted' => true]);

                        // event(new PembelianDetailAktivasi($record, auth()->user()));

                        \Filament\Notifications\Notification::make()
                            ->title('PIN berhasil diterima')
                            ->success()
                            ->send();
                    }),

                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where(function ($query) {
            $query->where('user_id', auth()->id());
        })->orderByDesc('updated_at');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAktivasiPins::route('/'),
            'create' => Pages\CreateAktivasiPin::route('/create'),
            'edit' => Pages\EditAktivasiPin::route('/{record}/edit'),
        ];
    }
}
