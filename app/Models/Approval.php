<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

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

    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Booking {$eventName}");
    }

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
}
