<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'service_date',
        'description',
        'cost',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}