<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\On;
use Carbon\Carbon;

class AllStats extends BaseWidget
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
        $query = Booking::query();

        if ($this->startDate && $this->endDate) {
            $start = Carbon::parse($this->startDate)->startOfDay();
            $end   = Carbon::parse($this->endDate)->endOfDay();

            $query->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                  ->orWhereBetween('end_date', [$start, $end]);
            });
        }

        $total = (clone $query)->count();
        $pending = (clone $query)->where('status', 'pending')->count();
        $rejected = (clone $query)->where('status', 'rejected')->count();
        $approved = (clone $query)->where('status', 'approved')->count();
        $completed = (clone $query)->where('status', 'completed')->count();

        return [

            Stat::make('Total Bookings', $total)
                ->description('All booking requests')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('primary'),

            Stat::make('Pending', $pending)
                ->description('Waiting approval')
                ->descriptionIcon('heroicon-m-clock')
                ->icon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Approved', $approved)
                ->description('Approved bookings')
                ->descriptionIcon('heroicon-m-check-circle')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Rejected', $rejected)
                ->description('Rejected requests')
                ->descriptionIcon('heroicon-m-x-circle')
                ->icon('heroicon-o-x-circle')
                ->color('danger'),

            Stat::make('Completed', $completed)
                ->description('Vehicle usage finished')
                ->descriptionIcon('heroicon-m-flag')
                ->icon('heroicon-o-flag')
                ->color('gray'),

        ];
    }
}