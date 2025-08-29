<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ApprovePembelianStockisResource\Pages;
use App\Models\Pembelian;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ApprovePembelianStockisResource extends Resource
{
    protected static ?string $model = Pembelian::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Approve Pembelian Stockis';

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
                    ->searchable()
                    ->label("ID Pembelian"),
      

                Tables\Columns\TextColumn::make('user.nama')
                    ->searchable()
                    ->label("Nama Pembeli"),
                Tables\Columns\TextColumn::make('nama_penerima')
                    ->searchable()
                    ->label("Nama Penerima"),
                Tables\Columns\TextColumn::make('alamat_tujuan')
                    ->searchable()
                    ->label("Alamat Tujuan"),
                Tables\Columns\TextColumn::make('no_telp')
                    ->searchable()
                    ->label("No HP Penerima"),

                Tables\Columns\BadgeColumn::make('status_pembelian')
                    ->color(fn(string $state): string => match ($state) {
                        'menunggu' => 'gray',
                        'transfer' => 'warning',
                        'proses' => 'info',
                        'ditolak' => 'danger',
                        'selesai' => 'success',
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
        return parent::getEloquentQuery()
            ->where('beli_dari', Auth::id())->orderByDesc('updated_at');
    }

    public static function getNavigationBadge(): ?string
    {
        return Pembelian::where('beli_dari', 1)->where('status_pembelian', 'menunggu')->count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApprovePembelianStockis::route('/'),
            'create' => Pages\CreateApprovePembelianStockis::route('/create'),
            'edit' => Pages\EditApprovePembelianStockis::route('/{record}/edit'),
        ];
    }
}
