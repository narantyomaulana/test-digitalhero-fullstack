@extends('layouts.app')

@section('title', 'Booking Confirmation')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h1 class="h4 mb-0">Booking Confirmed!</h1>
                </div>
                <div class="card-body">
                    <div class="alert alert-success mb-4">
                        <h2 class="h5 mb-1">Thank You for Your Booking!</h2>
                        <p class="mb-0">Your payment has been successfully processed.</p>
                    </div>

                    <h3 class="h5 mb-3">Booking Details</h3>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th width="35%">Booking ID</th>
                                <td>{{ $booking->id }}</td>
                            </tr>
                            <tr>
                                <th>Service</th>
                                <td>{{ $booking->service->name }}</td>
                            </tr>
                            <tr>
                                <th>Booking Date</th>
                                <td>{{ $booking->booking_date->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <th>Customer Name</th>
                                <td>{{ $booking->customer_name }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $booking->customer_email }}</td>
                            </tr>
                            @if ($booking->customer_phone)
                                <tr>
                                    <th>Phone</th>
                                    <td>{{ $booking->customer_phone }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>

                    <h3 class="h5 mb-3 mt-4">Payment Details</h3>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th width="35%">Base Price</th>
                                <td>Rp {{ number_format($booking->base_price, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Weekend Surcharge</th>
                                <td>Rp {{ number_format($booking->weekend_charge, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="table-primary">
                                <th>Total Paid</th>
                                <td class="fw-bold">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Payment Status</th>
                                <td><span class="badge bg-success">Paid</span></td>
                            </tr>
                            <tr>
                                <th>Payment Time</th>
                                <td>{{ $booking->payment->updated_at->format('d M Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        <a href="{{ route('booking.index') }}" class="btn btn-primary">Back to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
