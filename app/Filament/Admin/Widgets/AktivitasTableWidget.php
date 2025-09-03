<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Aktivitas;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AktivitasTableWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Aktivitas::query()
                    ->with('user')
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.nama')
                    ->label('Nama User')
                    ->searchable()

                    ->extraAttributes(['style' => 'min-width: 150px;']),

                Tables\Columns\TextColumn::make('judul')
                    ->label('Aktivitas')
                    ->getStateUsing(function ($record) {
                        return '
            <div style="line-height:1.4; max-width: 220px; word-wrap: break-word; white-space: normal;">
                <div style="font-size: 0.75rem; color: #6b7280; font-weight: 500;">' . e(format_tanggal_indonesia($record->created_at)) . '</div>
                <div style="font-weight: 600; color: #111827;">' . e($record->judul) . '</div>
                <div style="font-size: 0.85rem; color: ' . ($record->tipe === 'plus' ? '#059669' : ($record->tipe === 'minus' ? '#dc2626' : '#4b5563')) . ';">' . e($record->keterangan) . '</div>
            </div>
        ';
                    })
                    ->html()
                    ->extraAttributes(['style' => 'max-width: 280px; white-space: normal; word-wrap: break-word;']),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status & Nominal')
                    ->getStateUsing(function ($record) {
                        $nominalText = $record->nominal !== null
                        ? ($record->tipe === 'plus' ? '+' : ($record->tipe === 'minus' ? '-' : '')) . number_format($record->nominal, 0, ',', '.')
                        : '<span style=""> </span>';

                        return '
            <div style="line-height:1.4">
                <div style="font-size: 0.85rem; font-weight: 500; color: #059669;">' . e($record->status) . '</div>
                <div style="font-size: 1rem; font-weight: 700; color: #111827;">' . $nominalText . '</div>
            </div>
        ';
                    })
                    ->html()
                    ->extraAttributes(['style' => 'min-width: 120px;']),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipe')
                    ->options([
                        'plus' => 'Plus',
                        'minus' => 'Minus',
                        'neutral' => 'Neutral',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'success' => 'Success',
                        'pending' => 'Pending',
                        'failed' => 'Failed',
                    ]),
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50, 100]);
    }
}
