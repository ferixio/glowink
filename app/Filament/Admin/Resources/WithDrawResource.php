<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\WithDrawResource\Pages;
use App\Models\Aktivitas;
use App\Models\Withdraw;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WithDrawResource extends Resource
{
    protected static ?string $model = Withdraw::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
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
        return $table->recordUrl(null)
            ->columns([
                Tables\Columns\TextColumn::make('user.id_mitra')

                    ->searchable(),
                Tables\Columns\TextColumn::make('user.nama')
                    ->label('User')

                    ->searchable(),
                Tables\Columns\TextColumn::make('nominal')
                    ->label('Nominal Withdraw')

                    ->searchable()
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->alignment('right'),

                Tables\Columns\TextColumn::make('user.saldo_penghasilan')
                    ->label('Saldo Penghasilan')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->alignment('right'),

                Tables\Columns\BadgeColumn::make('status')
                    ->sortable()
                    ->searchable()

                    ->colors([
                        'warning' => 'pending',
                        'success' => 'selesai',
                        'danger' => 'ditolak',
                    ]),
                Tables\Columns\TextColumn::make('tgl_withdraw')

                    ->searchable(),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'selesai' => 'Selesai',
                        'ditolak' => 'Ditolak',
                    ]),
            ])
            ->actions([

                Tables\Actions\Action::make('Selesai')

                    ->color('success')
                    ->visible(fn($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        $user = $record->user;

                        if ((float) $user->saldo_penghasilan < (float) $record->nominal) {
                            Notification::make()
                                ->title('Gagal')
                                ->body('Saldo penghasilan tidak mencukupi untuk menyelesaikan withdraw ini.')
                                ->danger()
                                ->send();
                            return;
                        }

                        Aktivitas::create([
                            'user_id' => $user->id,
                            'judul' => 'Withdraw Selesai',
                            'keterangan' => 'Penarikan dana berhasil diproses',
                            'status' => 'selesai',
                            'tipe' => 'minus',
                            'nominal' => $record->nominal,
                        ]);

                        $user->update([
                            'saldo_withdraw' => $user->saldo_withdraw + $record->nominal,
                            'saldo_penghasilan' => $user->saldo_penghasilan - $record->nominal,
                        ]);

                        $record->status = 'selesai';
                        $record->save();

                        Notification::make()
                            ->title('Berhasil')
                            ->body('Withdraw diselesaikan dan saldo telah diperbarui.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('Tolak')
                    ->visible(fn($record) => $record->status === 'pending')
                    ->color('danger')
                    ->action(function ($record) {
                        $record->status = 'ditolak';
                        $record->save();

                        Notification::make()
                            ->title('Berhasil')
                            ->body('Permintaan withdraw ditolak.')
                            ->success()
                            ->send();
                    }),

                // Tables\Actions\EditAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('Selesaikan Terpilih')
                        ->color('success')
                        ->action(function ($records) {
                            $processed = 0;
                            $skipped = 0;
                            foreach ($records as $record) {
                                if ($record->status !== 'pending') {
                                    $skipped++;
                                    continue;
                                }

                                $user = $record->user;
                                if ((float) $user->saldo_penghasilan < (float) $record->nominal) {
                                    $skipped++;
                                    continue;
                                }

                                Aktivitas::create([
                                    'user_id' => $user->id,
                                    'judul' => 'Withdraw Selesai',
                                    'keterangan' => 'Penarikan dana berhasil diproses (bulk)',
                                    'status' => 'selesai',
                                    'tipe' => 'minus',
                                    'nominal' => $record->nominal,
                                ]);

                                $user->update([
                                    'saldo_withdraw' => $user->saldo_withdraw + $record->nominal,
                                    'saldo_penghasilan' => $user->saldo_penghasilan - $record->nominal,
                                ]);

                                $record->status = 'selesai';
                                $record->save();
                                $processed++;
                            }

                            Notification::make()
                                ->title('Bulk Selesai Withdraw')
                                ->body("Diproses: {$processed}, Dilewati: {$skipped}")
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('Tolak Terpilih')
                        ->color('danger')
                        ->action(function ($records) {
                            $processed = 0;
                            $skipped = 0;
                            foreach ($records as $record) {
                                if ($record->status !== 'pending') {
                                    $skipped++;
                                    continue;
                                }

                                $record->status = 'ditolak';
                                $record->save();
                                $processed++;
                            }

                            Notification::make()
                                ->title('Bulk Tolak Withdraw')
                                ->body("Diproses: {$processed}, Dilewati: {$skipped}")
                                ->success()
                                ->send();
                        }),
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

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->orderByDesc('created_at');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWithDraws::route('/'),
            'create' => Pages\CreateWithDraw::route('/create'),
            'edit' => Pages\EditWithDraw::route('/{record}/edit'),
        ];
    }
}
