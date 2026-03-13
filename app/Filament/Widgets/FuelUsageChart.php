<?php

namespace App\Filament\Widgets;

use App\Models\FuelLog;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FuelUsageChart extends ChartWidget
{
    protected static ?string $heading = 'Fuel Consumption Overview';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = FuelLog::select(
                DB::raw('DATE(date) as day'),
                DB::raw('SUM(fuel_amount) as total_fuel'),
                DB::raw('SUM(cost) as total_cost')
            )
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        return [
            'datasets' => [

                [
                    'label' => 'Fuel Used (Liter)',
                    'data' => $data->pluck('total_fuel'),
                    'borderWidth' => 3,
                    'yAxisID' => 'y',
                ],

                [
                    'label' => 'Fuel Cost (Rp)',
                    'data' => $data->pluck('total_cost'),
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
                        'text' => 'Fuel (Liter)',
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
                        'text' => 'Cost (Rp)',
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