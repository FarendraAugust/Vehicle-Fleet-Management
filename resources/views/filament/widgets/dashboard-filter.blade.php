<x-filament-widgets::widget>
    <x-filament::section>

        <div class="flex flex-col gap-4">

            <div class="flex items-start justify-between gap-4">

                <div class="flex items-start gap-3">
                    <x-heroicon-o-calendar-days class="w-6 h-6 text-primary-500"/>

                    <div>
                        <h2 class="text-lg font-semibold">
                            Dashboard Filters
                        </h2>

                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Filter vehicle monitoring data by date range.
                        </p>
                    </div>
                </div>

                <x-filament::actions
                    :actions="$this->getActions()"
                    alignment="end"
                />

            </div>

            <div class="pt-2">
                {{ $this->form }}
            </div>

        </div>

    </x-filament::section>
</x-filament-widgets::widget>