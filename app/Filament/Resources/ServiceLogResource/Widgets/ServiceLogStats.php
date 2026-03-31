<?php

namespace App\Filament\Resources\ServiceLogResource\Widgets;

use App\Models\ServiceLog;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\On;

class ServiceLogStats extends BaseWidget
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
        $query = ServiceLog::query();

        // Filter tanggal
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('service_date', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ]);
        }

        $total_service = (clone $query)->count();
        $total_cost    = (clone $query)->sum('cost');

        $avg_cost      = $total_service > 0 ? $total_cost / $total_service : 0;

        $last_service  = (clone $query)->max('service_date');

        // Optional: service hari ini
        $today_service = (clone $query)
            ->whereDate('service_date', now())
            ->count();

        return [

            Stat::make('Total Services', $total_service)
                ->description('Total service transactions')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->icon('heroicon-o-wrench-screwdriver')
                ->color('primary'),

            Stat::make('Total Cost', 'Rp ' . number_format($total_cost, 0, ',', '.'))
                ->description('Total maintenance cost')
                ->descriptionIcon('heroicon-m-banknotes')
                ->icon('heroicon-o-banknotes')
                ->color('danger'),

            Stat::make('Average Cost / Service', 'Rp ' . number_format($avg_cost, 0, ',', '.'))
                ->description('Average cost per service')
                ->descriptionIcon('heroicon-m-calculator')
                ->icon('heroicon-o-calculator')
                ->color('warning'),

            Stat::make('Services Today', $today_service)
                ->description('Number of services today')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->icon('heroicon-o-calendar-days')
                ->color('success'),

            Stat::make('Last Service', $last_service ? Carbon::parse($last_service)->format('d M Y') : '-')
                ->description('Most recent service date')
                ->descriptionIcon('heroicon-m-clock')
                ->icon('heroicon-o-clock')
                ->color('info'),

            Stat::make('Avg Daily Cost',
                'Rp ' . number_format(
                    $this->calculateDailyAverage($query),
                    0,
                    ',',
                    '.'
                )
            )
                ->description('Average spending per day')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->icon('heroicon-o-chart-bar')
                ->color('gray'),

        ];
    }

    private function calculateDailyAverage($query)
    {
        $days = Carbon::parse($this->startDate)->diffInDays(Carbon::parse($this->endDate)) + 1;

        if ($days <= 0) return 0;

        $total_cost = (clone $query)->sum('cost');

        return $total_cost / $days;
    }
}