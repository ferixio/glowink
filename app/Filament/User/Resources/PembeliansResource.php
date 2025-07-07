<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\PembeliansResource\Pages;
use App\Models\Pembelian;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PembeliansResource extends Resource
{
    protected static ?string $model = Pembelian::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $label = 'Daftar Pembelian';
    protected static ?string $navigationLabel = 'Daftar Pembelian';

    protected static ?int $navigationSort = 3;

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
                    ->searchable()->label("Nama Penerima")->url(fn($record) => static::getUrl('detail', ['record' => $record])),

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
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where(function ($query) {
            $query->where('user_id', auth()->id())
                ->orWhereHas('user', function ($q) {
                    $q->where('id', auth()->id());
                });
        });
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
