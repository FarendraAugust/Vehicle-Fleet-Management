<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\File;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'plate_number',
        'brand',
        'model',
        'year',
        'vehicle_type',
        'ownership',
        'region_id',
        'current_odometer',
        'status',
    ];

    protected $casts = [
        'year' => 'integer',
        'image' => 'string',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function fuelLogs()
    {
        return $this->hasMany(FuelLog::class);
    }

    public function serviceLogs()
    {
        return $this->hasMany(ServiceLog::class);
    }

    public function usages()
    {
        return $this->hasMany(VehicleUsage::class);
    }
}
