@extends('layouts.app')

@section('title', 'Payment Checkout')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h1 class="h4 mb-0">Payment Checkout</h1>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h3 class="h5 mb-3">Booking Details</h3>
                            <div class="mb-2"><strong>Service:</strong> {{ $booking->service->name }}</div>
                            <div class="mb-2"><strong>Date:</strong> {{ $booking->booking_date->format('d M Y') }}</div>
                            <div class="mb-2"><strong>Customer:</strong> {{ $booking->customer_name }}</div>
                        </div>
                        <div class="col-md-6">
                            <h3 class="h5 mb-3">Payment Summary</h3>
                            <div class="mb-2"><strong>Base Price:</strong> Rp
                                {{ number_format($booking->base_price, 0, ',', '.') }}</div>
                            <div class="mb-2"><strong>Weekend Charge:</strong> Rp
                                {{ number_format($booking->weekend_charge, 0, ',', '.') }}</div>
                            <div class="mb-2"><strong>Total Amount:</strong> <span class="fw-bold">Rp
                                    {{ number_format($booking->total_price, 0, ',', '.') }}</span></div>
                        </div>
                    </div>

                    <div class="alert alert-info mb-4">
                        <p class="mb-0">Please complete your payment to confirm your booking. You will be redirected to
                            Midtrans payment page.</p>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('booking.index') }}" class="btn btn-secondary">Cancel</a>
                        <button id="pay-button" class="btn btn-primary">Pay Now</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ $clientKey }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const payButton = document.getElementById('pay-button');

            payButton.addEventListener('click', function() {
                // Disable the button to prevent multiple clicks
                payButton.disabled = true;
                payButton.textContent = 'Processing...';

                // Open Snap payment popup
                snap.pay('{{ $snapToken }}', {
                    onSuccess: function(result) {
                        // Handle success, redirect to success page
                        window.location.href = '{{ route('payment.success') }}?order_id=' +
                            result.order_id;
                    },
                    onPending: function(result) {
                        // Handle pending, update UI or redirect
                        alert('Payment is pending. Please complete your payment.');
                        payButton.disabled = false;
                        payButton.textContent = 'Pay Now';
                    },
                    onError: function(result) {
                        // Handle error, show error message
                        alert('Payment failed. Please try again.');
                        payButton.disabled = false;
                        payButton.textContent = 'Pay Now';
                    },
                    onClose: function() {
                        // Handle popup closed without completing payment
                        payButton.disabled = false;
                        payButton.textContent = 'Pay Now';
                    }
                });
            });
        });
    </script>
@endsection
