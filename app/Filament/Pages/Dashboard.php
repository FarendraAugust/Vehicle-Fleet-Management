<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AllStats;
use App\Filament\Widgets\DashboardFilter;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Forms\Components\Select;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    protected static ?int $navigationSort = -2;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $title = 'Dashboard';

    protected ?string $heading = 'Vehicle Monitoring Dashboard';

    protected function getFiltersFormSchema(): array
    {
        return [
            Select::make('period')
                ->label('Period')
                ->options([
                    '1' => 'Today',
                    '7' => 'Last 7 Days',
                    '30' => 'Last 30 Days',
                    '365' => 'Last 1 Year',
                ])
                ->default('7')
                ->live(),
        ];
    }

    public function getHeaderWidgets(): array
    {
        return [
            AccountWidget::class,
            FilamentInfoWidget::class,
        ];
    }

    public function getWidgets(): array
    {
        return [
            DashboardFilter::class,
            AllStats::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return [
            'default' => 1,
            'md' => 2,
        ];
    }
}
