<x-filament-widgets::widget>
    <x-filament::section>
        <form wire:submit="filter" class="flex w-full items-end gap-4">
            <div class="flex w-full gap-6  items-end">
                {{ $this->form }}
                <x-filament::button type="submit" size="xl" class=" h-10 w-40 px-2 py-0">
                    Filter
                </x-filament::button>
            </div>
        </form>
    </x-filament::section>
</x-filament-widgets::widget>
