<?php

namespace App\Filament\Resources\BookingResource\Widgets;

use App\Models\Booking;
use App\Models\FuelLog;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\On;

class BookingStats extends BaseWidget
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

        // Date filter (created_at)
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ]);
        }

        $baseQuery = clone $query;

        $total     = (clone $baseQuery)->count();
        $pending   = (clone $baseQuery)->where('status', 'pending')->count();
        $approved  = (clone $baseQuery)->where('status', 'approved')->count();
        $rejected  = (clone $baseQuery)->where('status', 'rejected')->count();
        $completed = (clone $baseQuery)->where('status', 'completed')->count();

        // Starts today
        $today = (clone $baseQuery)
            ->whereDate('start_date', Carbon::today())
            ->count();

        // Ongoing trips
        $ongoing = (clone $baseQuery)
            ->whereDate('start_date', '<=', Carbon::today())
            ->whereDate('end_date', '>=', Carbon::today())
            ->where('status', 'approved')
            ->count();

        // Upcoming trips
        $upcoming = (clone $baseQuery)
            ->whereDate('start_date', '>', Carbon::today())
            ->where('status', 'approved')
            ->count();

        $lastActivity = (clone $baseQuery)->max('updated_at');

        return [

            Stat::make('Total Bookings', $total)
                ->description('All booking requests')
                ->icon('heroicon-o-document-text')
                ->color('primary'),

            Stat::make('Pending', $pending)
                ->description('Awaiting approval')
                ->icon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Approved', $approved)
                ->description('Approved bookings')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Rejected', $rejected)
                ->description('Rejected bookings')
                ->icon('heroicon-o-x-circle')
                ->color('danger'),

            Stat::make('Completed', $completed)
                ->description('Completed trips')
                ->icon('heroicon-o-check-badge')
                ->color('info'),

            Stat::make('Starting Today', $today)
                ->description('Trips starting today')
                ->icon('heroicon-o-calendar-days')
                ->color('primary'),

            Stat::make('Ongoing Trips', $ongoing)
                ->description('Currently in progress')
                ->icon('heroicon-o-truck')
                ->color('info'),

            Stat::make('Upcoming Trips', $upcoming)
                ->description('Scheduled for future')
                ->icon('heroicon-o-arrow-right-circle')
                ->color('success'),

            Stat::make(
                'Last Activity',
                $lastActivity
                    ? Carbon::parse($lastActivity)->format('d M Y')
                    : '-'
            )
                ->description('Last update recorded')
                ->icon('heroicon-o-clock')
                ->color('gray'),
        ];
    }
}
