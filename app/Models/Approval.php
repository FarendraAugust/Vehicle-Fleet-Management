<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Approval extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'approver_id',
        'level',
        'status',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    protected function afterCreate(): void
    {
        Approval::create([
            'booking_id' => $this->record->id,
            'approver_id' => 2,
            'level' => 1,
        ]);

        Approval::create([
            'booking_id' => $this->record->id,
            'approver_id' => 3,
            'level' => 2,
        ]);
    }

    public function getVehicleAttribute()
    {
        return $this->booking?->vehicle;
    }

    public function getDriverAttribute()
    {
        return $this->booking?->driver;
    }
}
