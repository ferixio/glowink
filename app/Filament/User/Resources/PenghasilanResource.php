<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\PenghasilanResource\Pages;
use App\Models\Penghasilan;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PenghasilanResource extends Resource
{
    protected static ?string $model = Penghasilan::class;
    protected static ?string $navigationGroup = 'Laporan - laporan';
    protected static ?string $navigationLabel = 'Laporan Penghasilan';

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\TextInput::make('tgl_dapat_bonus')
                //     ->required(),
                // Forms\Components\TextInput::make('kategori_bonus')
                //     ->required(),
                // Forms\Components\TextInput::make('status_qr')
                //     ->required(),
                // Forms\Components\TextInput::make('keterangan')
                //     ->required(),
                // Forms\Components\TextInput::make('nominal_bonus')
                //     ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('tgl_dapat_bonus')
                    ->searchable()
                    ->label('Tanggal Bonus')
                    ->formatStateUsing(function ($state) {
                        if (!$state) {
                            return '-';
                        }

                        $bulan = [
                            1 => 'Januari',
                            2 => 'Februari',
                            3 => 'Maret',
                            4 => 'April',
                            5 => 'Mei',
                            6 => 'Juni',
                            7 => 'Juli',
                            8 => 'Agustus',
                            9 => 'September',
                            10 => 'Oktober',
                            11 => 'November',
                            12 => 'Desember',
                        ];
                        $date = date_create($state);
                        $d = date_format($date, 'j');
                        $m = (int) date_format($date, 'n');
                        $y = date_format($date, 'Y');
                        return "$d {$bulan[$m]} $y";
                    }),
                // Tables\Columns\TextColumn::make('status_qr')->searchable(),
                Tables\Columns\TextColumn::make('kategori_bonus')->searchable(),

                Tables\Columns\TextColumn::make('keterangan')->searchable(),
                Tables\Columns\TextColumn::make('nominal_bonus')
                    ->searchable()
                    ->label('Nominal Bonus')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),

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
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->with('user');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenghasilans::route('/'),
            'create' => Pages\CreatePenghasilan::route('/create'),
            'edit' => Pages\EditPenghasilan::route('/{record}/edit'),
        ];
    }
}
