<?php

namespace App\Filament\Widgets;

use App\Models\VehicleUsage;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VehicleUsageChart extends ChartWidget
{
    protected static ?string $heading = 'Vehicle Usage Overview';

    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $data = VehicleUsage::select(
                DB::raw('DATE(created_at) as day'),
                DB::raw('COUNT(id) as total_trips'),
                DB::raw('SUM(end_odometer - start_odometer) as total_distance')
            )
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        return [
            'datasets' => [

                [
                    'label' => 'Distance (km)',
                    'data' => $data->pluck('total_distance'),
                    'borderWidth' => 3,
                    'yAxisID' => 'y',
                ],

                [
                    'label' => 'Trips',
                    'data' => $data->pluck('total_trips'),
                    'borderWidth' => 3,
                    'yAxisID' => 'y1',
                ],

            ],

            'labels' => $data->pluck('day')->map(
                fn ($date) => Carbon::parse($date)->format('d M')
            ),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [

                'y' => [
                    'type' => 'linear',
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => 'Distance (km)',
                    ],
                ],

                'y1' => [
                    'type' => 'linear',
                    'position' => 'right',
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Trips',
                    ],
                ],

            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}