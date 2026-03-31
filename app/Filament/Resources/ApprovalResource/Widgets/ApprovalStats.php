<?php

namespace App\Filament\Resources\ApprovalResource\Widgets;

use App\Models\Booking;
use App\Models\FuelLog;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\On;

class ApprovalStats extends BaseWidget
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
        $query = Booking::query()->with('approvals');

        // Filter tanggal (optional kalau ada)
        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [
                Carbon::parse($this->startDate)->startOfDay(),
                Carbon::parse($this->endDate)->endOfDay(),
            ]);
        }

        // Clone query
        $total        = (clone $query)->count();
        $pending      = (clone $query)->where('status', 'pending')->count();
        $approved     = (clone $query)->where('status', 'approved')->count();
        $rejected     = (clone $query)->where('status', 'rejected')->count();
        $completed    = (clone $query)->where('status', 'completed')->count();

        $lastApproval = (clone $query)->max('updated_at');

        return [

            Stat::make('Total Requests', $total)
                ->description('All booking requests')
                ->icon('heroicon-o-document-text')
                ->color('primary'),

            Stat::make('Pending', $pending)
                ->description('Waiting for approval')
                ->icon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Approved', $approved)
                ->description('Approved requests')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Rejected', $rejected)
                ->description('Rejected requests')
                ->icon('heroicon-o-x-circle')
                ->color('danger'),

            Stat::make('Completed', $completed)
                ->description('Finished trips')
                ->icon('heroicon-o-check-badge')
                ->color('info'),

            Stat::make(
                'Last Activity',
                $lastApproval
                    ? Carbon::parse($lastApproval)->format('d M Y')
                    : '-'
            )
                ->description('Last update / approval')
                ->icon('heroicon-o-clock')
                ->color('gray'),

        ];
    }
}
