<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\PembeliansResource\Pages;
use App\Models\Pembelian;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PembeliansResource extends Resource
{
    protected static ?string $model = Pembelian::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $label = 'Daftar Pembelian Stockis';

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
                Tables\Columns\TextColumn::make('id')
                    ->searchable()->label("id")
                    ->url(fn($record) => static::getUrl('detail', ['record' => $record])),
                Tables\Columns\TextColumn::make('nama_penerima')
                    ->searchable()->label("Nama Penerima"),

            ])
            ->filters([
                //
            ])
            ->actions([
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPembelians::route('/'),
            'create' => Pages\CreatePembelians::route('/create'),
            'edit' => Pages\EditPembelians::route('/{record}/edit'),
            'detail' => Pages\DetailPembelian::route('/{record}/detail'),
        ];
    }
}
