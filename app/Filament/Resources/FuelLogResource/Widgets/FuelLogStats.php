<?php

namespace App\Filament\Resources\FuelLogResource\Widgets;

use App\Models\FuelLog;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\On;

class FuelLogStats extends BaseWidget
{
    public $startDate;
    public $endDate;

    public function mount(): void
    {
        $this->startDate = now()->subDays(7)->toDateString();
        $this->endDate = now()->toDateString();
    }

    #[On('dashboard-filter')]
    public function updateFilters($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    protected function getStats(): array
    {
        $query = FuelLog::query();

        // Filter tanggal
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('date', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ]);
        }

        // Clone biar query tidak bentrok
        $total_log   = (clone $query)->count();
        $total_fuel  = (clone $query)->sum('fuel_amount');
        $total_cost  = (clone $query)->sum('cost');

        $avg_fuel    = $total_log > 0 ? $total_fuel / $total_log : 0;
        $avg_cost    = $total_log > 0 ? $total_cost / $total_log : 0;

        $last_refuel = (clone $query)->max('date');

        return [

            Stat::make('Total Refuels', $total_log)
                ->description('Total number of fuel transactions')
                ->descriptionIcon('heroicon-m-document-text')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('primary'),

            Stat::make('Total Fuel (Liters)', number_format($total_fuel, 2))
                ->description('Total accumulated fuel volume')
                ->descriptionIcon('heroicon-m-beaker')
                ->icon('heroicon-o-beaker')
                ->color('success'),

            Stat::make('Total Cost', 'Rp ' . number_format($total_cost, 0, ',', '.'))
                ->description('Total fuel expenditure')
                ->descriptionIcon('heroicon-m-banknotes')
                ->icon('heroicon-o-banknotes')
                ->color('danger'),

            Stat::make('Average Fuel / Transaction', number_format($avg_fuel, 2) . ' L')
                ->description('Average fuel per refuel')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->icon('heroicon-o-chart-bar')
                ->color('warning'),

            Stat::make('Average Cost / Transaction', 'Rp ' . number_format($avg_cost, 0, ',', '.'))
                ->description('Average spending per refuel')
                ->descriptionIcon('heroicon-m-calculator')
                ->icon('heroicon-o-calculator')
                ->color('info'),

            Stat::make('Last Refuel', $last_refuel ? \Carbon\Carbon::parse($last_refuel)->format('d M Y') : '-')
                ->description('Most recent refuel date')
                ->descriptionIcon('heroicon-m-clock')
                ->icon('heroicon-o-clock')
                ->color('gray'),

        ];
    }
}
