<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProdukResource\Pages;
use App\Models\Produk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProdukResource extends Resource
{
    protected static ?string $model = Produk::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Data Produk';

    protected static ?string $navigationGroup = "Master Data";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Produk')
                    ->schema([
                        Forms\Components\Select::make('paket')
                            ->options([
                                1 => 'Paket Aktivasi',
                                2 => 'Paket Quick Reward',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('nama')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('harga_stokis')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->maxValue(42949672.95),
                        Forms\Components\TextInput::make('harga_member')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->maxValue(42949672.95),
                        Forms\Components\FileUpload::make('gambar')
                            ->image()
                            ->required()
                            ->directory('products'),
                        Forms\Components\RichEditor::make('deskripsi')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status_aktif')
                            ->options([
                                'aktif' => 'Aktif',
                                'nonaktif' => 'Non Aktif',
                            ])
                            ->default('aktif')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('gambar')
                    ->disk('public')
                    ->url(fn($record) => $record->gambar ? asset('storage/products/' . $record->gambar) : asset('images/empty.webp')),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable()->label("Nama Produk"),
                Tables\Columns\TextColumn::make('paket')
                    ->searchable(),

                Tables\Columns\TextColumn::make('harga_stokis')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('harga_member')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status_aktif')
                    ->colors([
                        'success' => 'aktif',
                        'danger' => 'nonaktif',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_aktif')
                    ->options([
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Non Aktif',
                    ]),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProduks::route('/'),
            'create' => Pages\CreateProduk::route('/create'),
            'edit' => Pages\EditProduk::route('/{record}/edit'),
        ];
    }
}
