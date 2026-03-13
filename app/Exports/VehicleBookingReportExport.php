<?php

namespace App\Exports;

use App\Models\Booking;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class VehicleBookingReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return Booking::with([
            'vehicle',
            'vehicle.region',
            'driver',
            'requester'
        ])
        ->whereIn('status', ['approved','completed'])
        ->get()
        ->map(function ($booking) {

            $start = Carbon::parse($booking->start_date);
            $end = Carbon::parse($booking->end_date);

            return [
                'Booking ID' => $booking->id,
                'Plate Number' => $booking->vehicle->plate_number ?? '-',
                'Brand' => $booking->vehicle->brand ?? '-',
                'Model' => $booking->vehicle->model ?? '-',
                'Region' => $booking->vehicle->region->name ?? '-',
                'Driver' => $booking->driver->name ?? '-',
                'Driver Phone' => $booking->driver->phone ?? '-',
                'Requested By' => $booking->requester->name ?? '-',
                'Destination' => $booking->destination,
                'Purpose' => $booking->purpose,
                'Start Date' => $booking->start_date,
                'End Date' => $booking->end_date,
                'Duration (Hours)' => $start->diffInHours($end),
                'Status' => ucfirst($booking->status),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Booking ID',
            'Plate Number',
            'Brand',
            'Model',
            'Region',
            'Driver',
            'Driver Phone',
            'Requested By',
            'Destination',
            'Purpose',
            'Start Date',
            'End Date',
            'Duration (Hours)',
            'Status',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();

        return [

            // Header style
            1 => [
                'font' => [
                    'bold' => true,
                ],
            ],

            // Border seluruh table
            "A1:{$lastColumn}{$lastRow}" => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => 'thin',
                    ],
                ],
            ],

        ];
    }
}