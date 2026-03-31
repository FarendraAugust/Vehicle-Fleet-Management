<?php

namespace App\Filament\Resources\DriverResource\Widgets;

use App\Models\Driver;
use App\Models\Vehicle;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\On;
use Carbon\Carbon;

class DriverStats extends BaseWidget
{

    protected int | array $columns = 3;

    protected function getColumns(): int
    {
        return 3;
    }

    public $startDate;
    public $endDate;

    #[On('dashboard-filter')]
    public function updateFilters($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    protected function getStats(): array
    {
        $query = Driver::query();

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ]);
        }

        $baseQuery = clone $query;

        $total     = (clone $baseQuery)->count();
        $active    = (clone $baseQuery)->where('active', true)->count();
        $inactive  = (clone $baseQuery)->where('active', false)->count();

        $available = (clone $baseQuery)->where('status', 'available')->count();
        $busy      = (clone $baseQuery)->where('status', 'in_use')->count();

        $newDrivers = Driver::query()
            ->whereBetween('created_at', [
                now()->subDays(7)->startOfDay(),
                now()->endOfDay(),
            ])
            ->count();

        $lastActivity = (clone $baseQuery)->max('updated_at');

        return [

            Stat::make('Total Drivers', $total)
                ->description('All registered drivers')
                ->descriptionIcon('heroicon-m-users')
                ->icon('heroicon-o-users')
                ->color('primary'),

            Stat::make('Active Drivers', $active)
                ->description('Currently active')
                ->descriptionIcon('heroicon-m-check-circle')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Inactive Drivers', $inactive)
                ->description('Not active')
                ->descriptionIcon('heroicon-m-x-circle')
                ->icon('heroicon-o-x-circle')
                ->color('danger'),

            Stat::make('Available Drivers', $available)
                ->description('Ready for assignment')
                ->descriptionIcon('heroicon-m-user')
                ->icon('heroicon-o-user')
                ->color('success'),

            Stat::make('Busy Drivers', $busy)
                ->description('Currently on trip')
                ->descriptionIcon('heroicon-m-clock')
                ->icon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('New Drivers', $newDrivers)
                ->description('Added in last 7 days')
                ->descriptionIcon('heroicon-m-plus-circle')
                ->icon('heroicon-o-plus-circle')
                ->color('info'),

            Stat::make(
                'Last Activity',
                $lastActivity
                    ? Carbon::parse($lastActivity)->format('d M Y')
                    : '-'
            )
                ->description('Last update recorded')
                ->descriptionIcon('heroicon-m-clock')
                ->icon('heroicon-o-clock')
                ->color('gray'),

        ];
    }
}
