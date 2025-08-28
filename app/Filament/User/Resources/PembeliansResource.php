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

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Laporan - laporan';
    protected static ?string $navigationLabel = 'Laporan Pembelian';

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
                // Tables\Columns\TextColumn::make('id')
                //     ->searchable()->label("id")
                //     ->url(fn($record) => static::getUrl('detail', ['record' => $record])),
                Tables\Columns\TextColumn::make('user.nama')
                    ->label('Detail Pembelian')
                    ->html()
                    ->formatStateUsing(function ($state, $record) {
                        $status = match ($record->status_pembelian) {
                            'menunggu' => "<span class='inline-flex items-center text-gray-600 text-xs font-semibold'>Menunggu</span>",
                            'transfer' => "<span class='inline-flex items-center text-yellow-600 text-xs font-semibold'>Transfer</span>",
                            'proses' => "<span class='inline-flex items-center text-blue-600 text-xs font-semibold'>Proses</span>",
                            'ditolak' => "<span class='inline-flex items-center text-red-600 text-xs font-semibold'>Ditolak</span>",
                            'selesai' => "<span class='inline-flex items-center text-green-600 text-xs font-semibold'>Selesai</span>",
                            default => "<span class='inline-flex items-center text-gray-500 text-xs font-semibold'>-</span>",
                        };

                        return "
            <div class='flex flex-col'>
                <span class='font-bold text-gray-800'>{$record->user->nama}</span>
                <span class='text-sm text-gray-600'>Penjual: {$record->seller->nama}</span>
                <span class='text-sm text-gray-600'>Tanggal: {$record->created_at->format('d M Y')}</span>
                <div class='mt-1'>{$status}</div>
            </div>
        ";
                    })
                    ->url(fn($record) => static::getUrl('detail', ['record' => $record]))
                    ->searchable(),

                Tables\Columns\TextColumn::make('kategori_pembelian')
                    ->label('Kategori')
                    ->badge()
                    ->color('primary')
                    ->url(fn($record) => static::getUrl('detail', ['record' => $record]))
                    ->searchable(),

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
                ->orWhere('id_sponsor', auth()->id());
        })->orderByDesc('updated_at');
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
