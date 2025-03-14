<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\MidtransService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function checkout(Booking $booking)
    {
        if ($booking->status === 'paid') {
            return redirect()->route('booking.confirmation', $booking);
        }

        $midtransResponse = $this->midtransService->createTransaction($booking);

        if (!$midtransResponse['success']) {
            return redirect()->route('booking.index')
                ->with('error', 'Gagal membuat transaksi pembayaran: ' . $midtransResponse['message']);
        }

        $snapToken = $midtransResponse['snap_token'];
        $clientKey = config('midtrans.client_key');

        return view('payment.checkout', compact('booking', 'snapToken', 'clientKey'));
    }

    public function notification(Request $request)
    {
        $notificationData = $request->all();

        $response = $this->midtransService->handleNotification($notificationData);

        if (!$response['success']) {
            return response()->json(['status' => 'error', 'message' => $response['message']], 400);
        }

        return response()->json(['status' => 'success']);
    }

    public function success(Request $request)
    {
        $orderId = $request->order_id;

        // Get booking ID from order ID (format: BOOK-{id}-{timestamp})
        $explodedOrderId = explode('-', $orderId);
        $bookingId = $explodedOrderId[1] ?? null;

        if (!$bookingId) {
            return redirect()->route('booking.index')
                ->with('error', 'Invalid order ID format');
        }

        $booking = Booking::find($bookingId);

        if (!$booking) {
            return redirect()->route('booking.index')
                ->with('error', 'Booking tidak ditemukan');
        }

        if ($booking->status !== 'paid') {
            // Update status manually if needed
            $booking->status = 'paid';
            $booking->payment->status = 'success';
            $booking->payment->save();
            $booking->save();
        }

        return redirect()->route('booking.confirmation', $booking);
    }
}