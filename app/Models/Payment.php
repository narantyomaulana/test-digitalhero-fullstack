<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'payment_id',
        'payment_type',
        'amount',
        'status',
        'payment_data',
    ];

    protected $casts = [
        'payment_data' => 'array',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
