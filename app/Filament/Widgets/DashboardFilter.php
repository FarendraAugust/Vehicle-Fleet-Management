<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Carbon\Carbon;

class DashboardFilter extends Widget implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    protected static string $view = 'filament.widgets.dashboard-filter';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return true;
    }

    public array $data = [];

    public function mount(): void
    {
        $startDate = now()->subDays(7)->toDateString();
        $endDate = now()->toDateString();

        $this->form->fill([
            'data' => [
                'start_date' => $startDate,
                'end_date'   => $endDate,
            ],
        ]);

        $this->dispatch('dashboard-filter', startDate: $startDate, endDate: $endDate);
    }

    public function updated(string $property): void
    {
        if (str_starts_with($property, 'data')) {
            $this->dispatch('dashboard-filter',
                startDate: $this->data['start_date'] ?? null,
                endDate:   $this->data['end_date'] ?? null,
            );
        }
    }

    protected function getActions(): array
    {
        return [
            Action::make('lifetime')
                ->label('Lifetime')
                ->icon('heroicon-o-clock')
                ->action('setLifetime'),

            Action::make('month')
                ->label('1 Month')
                ->icon('heroicon-o-calendar')
                ->action('setMonth'),

            Action::make('week')
                ->label('1 Week')
                ->icon('heroicon-o-calendar-days')
                ->action('setWeek'),
        ];
    }

    public function setLifetime()
    {
        $this->form->fill([
            'data' => [
                'start_date' => null,
                'end_date' => null,
            ],
        ]);

        $this->dispatch('dashboard-filter', startDate: null, endDate: null);
    }

    public function setMonth()
    {
        $start = now()->subMonth()->toDateString();
        $end = now()->toDateString();

        $this->form->fill([
            'data' => [
                'start_date' => $start,
                'end_date' => $end,
            ],
        ]);

        $this->dispatch('dashboard-filter', startDate: $start, endDate: $end);
    }

    public function setWeek()
    {
        $start = now()->subWeek()->toDateString();
        $end = now()->toDateString();

        $this->form->fill([
            'data' => [
                'start_date' => $start,
                'end_date' => $end,
            ],
        ]);

        $this->dispatch('dashboard-filter', startDate: $start, endDate: $end);
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make([
                'default' => 1,
                'md' => 2,
            ])
                ->schema([

                    DatePicker::make('data.start_date')
                        ->label('Start Date')
                        ->maxDate(now())
                        ->live(),

                    DatePicker::make('data.end_date')
                        ->label('End Date')
                        ->live()
                        ->minDate(fn($get) => $get('data.start_date'))
                        ->maxDate(now()),

                ]),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema($this->getFormSchema());
    }
}
