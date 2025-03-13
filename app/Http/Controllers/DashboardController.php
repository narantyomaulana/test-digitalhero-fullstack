<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['service', 'payment'])
            ->latest()
            ->paginate(10);

        return view('dashboard.index', compact('bookings'));
    }
}