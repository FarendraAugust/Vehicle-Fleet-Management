<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use App\Models\Approval;
use App\Models\Vehicle;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\Booking;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $vehicleId = $data['vehicle_id'];
        $start = $data['start_date'];
        $end = $data['end_date'];

        $exists = Booking::where('vehicle_id', $vehicleId)
            ->whereIn('status', ['pending', 'approved']) // 🔥 penting
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere(function ($q2) use ($start, $end) {
                        $q2->where('start_date', '<=', $start)
                            ->where('end_date', '>=', $end);
                    });
            })
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'vehicle_id' => 'Vehicle already booked at this time.',
            ]);
        }

        return $data;
    }


    protected function afterCreate(): void
    {
        $booking = $this->record;

        // 🔥 create approvals
        Approval::insert([
            [
                'booking_id' => $booking->id,
                'approver_id' => $this->data['approver_level_1'],
                'level' => 1,
                'status' => 'pending',
            ],
            [
                'booking_id' => $booking->id,
                'approver_id' => $this->data['approver_level_2'],
                'level' => 2,
                'status' => 'pending',
            ],
        ]);

        // 🔥 set vehicle jadi reserved
        $booking->vehicle()->update([
            'status' => 'reserved',
        ]);

        // 🔥 set driver jadi reserved (kalau ada)
        if ($booking->driver) {
            $booking->driver()->update([
                'status' => 'reserved',
            ]);
        }
    }
}
