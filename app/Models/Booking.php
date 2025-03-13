<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'booking_date',
        'base_price',
        'weekend_charge',
        'total_price',
        'customer_name',
        'customer_email',
        'customer_phone',
        'status',
    ];

    protected $casts = [
        'booking_date' => 'date',
    ];

    // Relationships
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // Helper methods
    public function isWeekend()
    {
        return in_array($this->booking_date->dayOfWeek, [0, 6]); // 0 = Sunday, 6 = Saturday
    }

    public function calculateTotalPrice()
    {
        $weekendCharge = $this->isWeekend() ? 50000 : 0;
        $this->weekend_charge = $weekendCharge;
        $this->total_price = $this->base_price + $this->weekend_charge;

        return $this->total_price;
    }
}
