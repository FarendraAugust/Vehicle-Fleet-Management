<?php

namespace App\Filament\Resources\VehicleUsageResource\Widgets;

use App\Models\Vehicle;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\On;
use Carbon\Carbon;

class VehicleUsageStats extends BaseWidget
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
        $query = \App\Models\Booking::query()
            ->where('status', 'approved');

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('start_date', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ]);
        }

        $baseQuery = clone $query;

        // 🚗 Active trips
        $activeTrips = (clone $baseQuery)->count();

        // 🔄 Returned today
        $returnedToday = \App\Models\VehicleUsage::query()
            ->whereDate('created_at', Carbon::today())
            ->count();

        // 📏 Total distance
        $totalDistance = \App\Models\VehicleUsage::query()
            ->selectRaw('SUM(end_odometer - start_odometer) as total')
            ->value('total') ?? 0;

        // 📊 Avg distance
        $avgDistance = \App\Models\VehicleUsage::query()
            ->selectRaw('AVG(end_odometer - start_odometer) as avg')
            ->value('avg') ?? 0;

        // 🚗 Vehicles in use
        $inUseVehicles = \App\Models\Vehicle::where('status', 'in_use')->count();

        // ⏰ Late returns (end_date sudah lewat tapi belum return)
        $lateReturns = \App\Models\Booking::query()
            ->where('status', 'approved')
            ->where('end_date', '<', now())
            ->count();

        return [

            Stat::make('Active Trips', $activeTrips)
                ->description('Ongoing vehicle usage')
                ->icon('heroicon-o-truck')
                ->color('primary'),

            Stat::make('Returned Today', $returnedToday)
                ->description('Vehicles returned today')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('success'),

            Stat::make('Total Distance', number_format($totalDistance, 0, ',', '.') . ' km')
                ->description('Total distance traveled')
                ->icon('heroicon-o-map')
                ->color('info'),

            Stat::make('Avg Distance', number_format($avgDistance, 0, ',', '.') . ' km')
                ->description('Average per trip')
                ->icon('heroicon-o-chart-bar')
                ->color('info'),

            Stat::make('Vehicles In Use', $inUseVehicles)
                ->description('Currently assigned')
                ->icon('heroicon-o-truck')
                ->color('warning'),

            Stat::make('Late Returns', $lateReturns)
                ->description('Overdue vehicles')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger'),

        ];
    }
}
