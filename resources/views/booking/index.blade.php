@extends('layouts.app')

@section('title', 'PS Rental - Booking')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h1 class="h4 mb-0">Welcome to PS Rental System</h1>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h2 class="h5">Our Services</h2>
                            <div class="row">
                                @foreach ($services as $service)
                                    <div class="col-md-12 mb-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <h3 class="h6">{{ $service->name }}</h3>
                                                <p class="mb-2">{{ $service->description }}</p>
                                                <p class="fw-bold">Rp {{ number_format($service->price, 0, ',', '.') }} per
                                                    sesi</p>
                                                <a href="{{ route('booking.create', ['service_id' => $service->id]) }}"
                                                    class="btn btn-primary">Book Now</a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h2 class="h5">Information</h2>
                            <div class="alert alert-info">
                                <h4 class="h6 mb-2">Pricing:</h4>
                                <ul>
                                    <li>PS 4: Rp 30.000 per sesi</li>
                                    <li>PS 5: Rp 40.000 per sesi</li>
                                    <li>Weekend surcharge: Rp 50.000 (Sabtu & Minggu)</li>
                                </ul>
                                <p class="mb-0 small">Silakan pilih layanan untuk melanjutkan proses booking.</p>
                            </div>
                            <div class="d-grid">
                                <a href="{{ route('booking.create') }}" class="btn btn-success">Create New Booking</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
