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
        return $table->recordUrl(null)
            ->columns([

                Tables\Columns\TextColumn::make('tgl_dapat_bonus')
                    ->label('Detail Bonus')
                    ->html()
                    ->formatStateUsing(function ($state, $record) {
                        // Format tanggal
                        if ($state) {
                            $bulan = [
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                            ];
                            $date = date_create($state);
                            $d = date_format($date, 'j');
                            $m = (int) date_format($date, 'n');
                            $y = date_format($date, 'Y');
                            $tanggalBonus = "$d {$bulan[$m]} $y";
                        } else {
                            $tanggalBonus = '-';
                        }

                        // Format nominal bonus
                        $nominal = 'Rp ' . number_format($record->nominal_bonus, 0, ',', '.');

                        return "
            <div class='flex flex-col'>
                <span class='font-bold text-gray-800'>{$tanggalBonus}</span>
                <span class='text-sm text-gray-600'>{$record->keterangan}</span>
                <span class='text-sm text-green-600 font-semibold'>{$nominal}</span>
            </div>
        ";
                    })
                    // ->url(fn($record) => static::getUrl('detail', ['record' => $record]))
                    ->searchable(),

                Tables\Columns\TextColumn::make('kategori_bonus')
                    ->label('Kategori Bonus')
                    ->badge()
                    ->color('info')
                    // ->url(fn($record) => static::getUrl('detail', ['record' => $record]))
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
