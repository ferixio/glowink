<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\ProductStockResource\Pages;
use App\Models\ProdukStok;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductStockResource extends Resource
{
    protected static ?string $model = ProdukStok::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

     protected static ?string $navigationLabel = 'Stok';
    protected static ?int $navigationSort = 8;
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
     
                Tables\Columns\TextColumn::make('produk.nama')
                    ->label('Nama Produk')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stok')
                    ->label('Stok')
                    ->sortable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id())
            ->orderByDesc('updated_at');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductStocks::route('/'),
            'create' => Pages\CreateProductStock::route('/create'),
            'edit' => Pages\EditProductStock::route('/{record}/edit'),
        ];
    }
}
