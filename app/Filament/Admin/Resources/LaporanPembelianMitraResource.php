<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\LaporanPembelianMitraResource\Pages;
use App\Models\Pembelian;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LaporanPembelianMitraResource extends Resource
{
    protected static ?string $model = Pembelian::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $navigationLabel = 'Laporan Pembelian Mitra';
    protected static ?string $navigationGroup = 'Laporan - Laporan';
    protected static ?int $navigationSort = 2;

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
                Tables\Columns\TextColumn::make('tgl_beli')
                    ->label('Tanggal Pembelian')
                    ->date('d-m-Y')->searchable(),
                Tables\Columns\TextColumn::make('nama_penerima')
                    ->label('Nama Penerima')->searchable(),
                Tables\Columns\TextColumn::make('kategori_pembelian')
                    ->label('Kategori Pembelian')->searchable(),
                Tables\Columns\TextColumn::make('total_beli')
                    ->label('Total Pembelian')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('total_bonus')
                    ->label('Total Bonus')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
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

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->orderByDesc('tgl_beli');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporanPembelianMitras::route('/'),
            'create' => Pages\CreateLaporanPembelianMitra::route('/create'),
            'edit' => Pages\EditLaporanPembelianMitra::route('/{record}/edit'),
        ];
    }
}
