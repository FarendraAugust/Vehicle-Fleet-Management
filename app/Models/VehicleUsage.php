<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VehicleUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'booking_id',
        'start_odometer',
        'end_odometer',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}