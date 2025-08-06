<?php

namespace App\Filament\Admin\Resources\LaporanDevidenHarianResource\Pages;

use App\Filament\Admin\Resources\LaporanDevidenHarianResource;
use App\Filament\Admin\Widgets\DateRangeFilterWidget;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;

class ListLaporanDevidenHarians extends ListRecords
{
    protected static string $resource = LaporanDevidenHarianResource::class;

    public ?string $filterStart = null;
    public ?string $filterEnd = null;

    #[On('filterTanggalDeviden')]
    public function setDateRangeFilter($start, $end)
    {
        $this->filterStart = $start;
        $this->filterEnd = $end;
    }

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();
        if ($this->filterStart && $this->filterEnd) {
            $query->whereBetween('tanggal_deviden', [$this->filterStart, $this->filterEnd]);
        } elseif ($this->filterStart) {
            $query->whereDate('tanggal_deviden', '>=', $this->filterStart);
        } elseif ($this->filterEnd) {
            $query->whereDate('tanggal_deviden', '<=', $this->filterEnd);
        }
        return $query;
    }

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DateRangeFilterWidget::class,
        ];
    }
}
