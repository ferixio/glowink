<?php

namespace App\Filament\Admin\Widgets;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Widgets\Widget;

class DateRangeFilterWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.admin.widgets.date-range-filter-widget';
    protected int|string|array $columnSpan = 'full';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('start')->label('Tanggal Mulai'),
                DatePicker::make('end')->label('Tanggal Akhir'),
            ])
          
            ->columns(2)
            ->statePath('data');
    }

    public function filter(): void
    {
        $this->dispatch('filterTanggalDeviden',
            start: $this->data['start'] ?? null,
            end: $this->data['end'] ?? null
        );
    }
}
