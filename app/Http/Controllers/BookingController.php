<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Service;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index()
    {
        $services = Service::all();
        return view('booking.index', compact('services'));
    }

    public function create(Request $request)
    {
        $services = Service::all();
        return view('booking.create', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
        ]);

        $service = Service::findOrFail($request->service_id);

        // Start a database transaction
        return DB::transaction(function () use ($request, $service) {
            $bookingDate = now()->parse($request->booking_date);

            // Create new booking
            $booking = Booking::create([
                'service_id' => $service->id,
                'booking_date' => $bookingDate,
                'base_price' => $service->price,
                'weekend_charge' => 0,
                'total_price' => 0,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'status' => 'pending',
            ]);

            // Calculate total price
            $booking->calculateTotalPrice();
            $booking->save();

            // Redirect to payment page
            return redirect()->route('payment.checkout', $booking);
        });
    }

    public function confirmation(Booking $booking)
    {
        if ($booking->status !== 'paid') {
            return redirect()->route('booking.index')
                ->with('error', 'Booking tidak ditemukan atau pembayaran belum berhasil');
        }

        return view('booking.confirmation', compact('booking'));
    }
}