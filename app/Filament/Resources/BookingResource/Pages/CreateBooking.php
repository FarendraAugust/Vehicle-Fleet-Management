<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use App\Models\Approval;
use App\Models\Vehicle;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;

    protected function afterCreate(): void
    {
        $booking = $this->record;

        Approval::create([
            'booking_id' => $booking->id,
            'approver_id' => $this->data['approver_level_1'],
            'level' => 1,
            'status' => 'pending',
        ]);

        Approval::create([
            'booking_id' => $booking->id,
            'approver_id' => $this->data['approver_level_2'],
            'level' => 2,
            'status' => 'pending',
        ]);

        Vehicle::where('id', $booking->vehicle_id)->update([
            'status' => 'in_use',
        ]);
    }
}
