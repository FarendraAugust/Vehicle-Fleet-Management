<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FuelLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'date',
        'fuel_amount',
        'cost',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}