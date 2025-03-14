<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use Midtrans\Config;
use Midtrans\Snap;
use Exception;

class MidtransService
{
    public function __construct()
    {
        // Set your Merchant Server Key
        Config::$serverKey = config('midtrans.server_key');
        // Set your Merchant Client Key
        Config::$isProduction = config('midtrans.is_production');
        // Set sanitization on (default)
        Config::$isSanitized = config('midtrans.is_sanitized');
        // Set 3DS transaction for credit card to true
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function createTransaction(Booking $booking)
    {
        $payment = $booking->payment;

        if (!$payment) {
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'amount' => $booking->total_price,
                'status' => 'pending',
            ]);
        }

        $params = [
            'transaction_details' => [
                'order_id' => 'BOOK-' . $booking->id . '-' . time(),
                'gross_amount' => (int) $booking->total_price,
            ],
            'customer_details' => [
                'first_name' => $booking->customer_name,
                'email' => $booking->customer_email,
                'phone' => $booking->customer_phone,
            ],
            'item_details' => [
                [
                    'id' => $booking->service->id,
                    'price' => (int) $booking->service->price,
                    'quantity' => 1,
                    'name' => $booking->service->name,
                ]
            ],
        ];

        // If booking has weekend charge, add it as an item
        if ($booking->weekend_charge > 0) {
            $params['item_details'][] = [
                'id' => 'weekend-charge',
                'price' => (int) $booking->weekend_charge,
                'quantity' => 1,
                'name' => 'Weekend Surcharge',
            ];
        }

        try {
            // Get Snap Payment Page URL
            $snapToken = Snap::getSnapToken($params);

            // Update payment with token
            $payment->update([
                'payment_id' => $params['transaction_details']['order_id'],
                'payment_data' => ['snap_token' => $snapToken],
            ]);

            return [
                'success' => true,
                'snap_token' => $snapToken,
                'payment' => $payment,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function handleNotification(array $notificationData)
    {
        $transaction = $notificationData['transaction_status'];
        $type = $notificationData['payment_type'];
        $orderId = $notificationData['order_id'];
        $fraud = $notificationData['fraud_status'] ?? null;

        // Get booking ID from order ID (format: BOOK-{id}-{timestamp})
        $explodedOrderId = explode('-', $orderId);
        $bookingId = $explodedOrderId[1] ?? null;

        if (!$bookingId) {
            return [
                'success' => false,
                'message' => 'Invalid order ID format',
            ];
        }

        $booking = Booking::find($bookingId);
        if (!$booking) {
            return [
                'success' => false,
                'message' => 'Booking not found',
            ];
        }

        $payment = $booking->payment;
        if (!$payment) {
            return [
                'success' => false,
                'message' => 'Payment not found',
            ];
        }

        // Update payment data
        $payment->payment_type = $type;
        $payment->payment_data = array_merge($payment->payment_data ?? [], $notificationData);

        // Handle status
        if ($transaction == 'capture') {
            // For credit card transaction, we need to check whether transaction is challenge by FDS or not
            if ($type == 'credit_card') {
                if ($fraud == 'challenge') {
                    // TODO set payment status challenge
                    $payment->status = 'pending';
                } else {
                    $payment->status = 'success';
                    $booking->status = 'paid';
                }
            }
        } else if ($transaction == 'settlement') {
            // Payment success
            $payment->status = 'success';
            $booking->status = 'paid';
        } else if ($transaction == 'pending') {
            // Payment pending
            $payment->status = 'pending';
        } else if ($transaction == 'deny') {
            // Payment denied
            $payment->status = 'failed';
        } else if ($transaction == 'expire') {
            // Payment expired
            $payment->status = 'expired';
        } else if ($transaction == 'cancel') {
            // Payment canceled
            $payment->status = 'failed';
        }

        $payment->save();
        $booking->save();

        return [
            'success' => true,
            'booking' => $booking,
            'payment' => $payment,
        ];
    }
}
