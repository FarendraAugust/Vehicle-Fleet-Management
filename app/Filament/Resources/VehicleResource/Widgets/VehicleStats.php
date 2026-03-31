<?php

namespace App\Filament\Resources\VehicleResource\Widgets;

use App\Models\Vehicle;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\On;
use Carbon\Carbon;

class VehicleStats extends BaseWidget
{
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
        $query = Vehicle::query();

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ]);
        }

        $total      = (clone $query)->count();
        $available  = (clone $query)->where('status', 'available')->count();
        $in_use     = (clone $query)->where('status', 'in_use')->count();

        // Tambahan stats
        $avg_odometer = (clone $query)->avg('current_odometer') ?? 0;

        $owned  = (clone $query)->where('ownership', 'company')->count(); // asumsi
        $rented = (clone $query)->where('ownership', 'rental')->count();  // asumsi

        $new_vehicle = Vehicle::query()
            ->whereBetween('created_at', [
                now()->subDays(7)->startOfDay(),
                now()->endOfDay(),
            ])
            ->count();

        return [

            Stat::make('Total Vehicles', $total)
                ->description('Total registered vehicles')
                ->descriptionIcon('heroicon-m-truck')
                ->icon('heroicon-o-truck')
                ->color('primary'),

            Stat::make('Available Vehicles', $available)
                ->description('Ready for booking')
                ->descriptionIcon('heroicon-m-check-circle')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('In Use Vehicles', $in_use)
                ->description('Currently in operation')
                ->descriptionIcon('heroicon-m-clock')
                ->icon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Avg Odometer', number_format($avg_odometer, 0, ',', '.') . ' km')
                ->description('Average vehicle mileage')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->icon('heroicon-o-chart-bar')
                ->color('info'),

            Stat::make('Owned vs Rental', "{$owned} / {$rented}")
                ->description('Company vs rented vehicles')
                ->descriptionIcon('heroicon-m-building-office')
                ->icon('heroicon-o-building-office')
                ->color('gray'),

            Stat::make('New Vehicles', $new_vehicle)
                ->description('Added in last 7 days')
                ->descriptionIcon('heroicon-m-plus-circle')
                ->icon('heroicon-o-plus-circle')
                ->color('success'),

        ];
    }
}
