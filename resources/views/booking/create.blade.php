@extends('layouts.app')

@section('title', 'Create Booking')

@section('styles')
<style>
    #calendar {
        height: 400px;
        margin-bottom: 15px;
    }
    .selected-date {
        background-color: #28a745;
        color: white;
        border-radius: 50%;
    }
</style>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h1 class="h4 mb-0">Create New Booking</h1>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('booking.store') }}">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label">Select Service:</label>
                                <select name="service_id" id="service_id" class="form-select @error('service_id') is-invalid @enderror" required>
                                    <option value="">-- Select Service --</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" data-price="{{ $service->price }}" {{ (old('service_id') == $service->id || request('service_id') == $service->id) ? 'selected' : '' }}>
                                            {{ $service->name }} - Rp {{ number_format($service->price, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('service_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Select Date:</label>
                                <input type="hidden" name="booking_date" id="booking_date" value="{{ old('booking_date') }}" required>
                                <div id="calendar"></div>
                                @error('booking_date')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                                <div id="selected_date_display" class="alert alert-info {{ old('booking_date') ? '' : 'd-none' }}">
                                    Selected date: <span id="selected_date_text">{{ old('booking_date') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="customer_name" class="form-label">Your Name:</label>
                                <input type="text" name="customer_name" id="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name') }}" required>
                                @error('customer_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="customer_email" class="form-label">Email:</label>
                                <input type="email" name="customer_email" id="customer_email" class="form-control @error('customer_email') is-invalid @enderror" value="{{ old('customer_email') }}" required>
                                @error('customer_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="customer_phone" class="form-label">Phone (optional):</label>
                                <input type="text" name="customer_phone" id="customer_phone" class="form-control @error('customer_phone') is-invalid @enderror" value="{{ old('customer_phone') }}">
                                @error('customer_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="card mt-4">
                                <div class="card-header">
                                    <h2 class="h5 mb-0">Price Calculation</h2>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2 row">
                                        <div class="col-7">Base Price:</div>
                                        <div class="col-5 text-end" id="base_price_display">Rp 0</div>
                                    </div>
                                    <div class="mb-2 row">
                                        <div class="col-7">Weekend Surcharge:</div>
                                        <div class="col-5 text-end" id="weekend_charge_display">Rp 0</div>
                                    </div>
                                    <div class="row fw-bold">
                                        <div class="col-7">Total Price:</div>
                                        <div class="col-5 text-end" id="total_price_display">Rp 0</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-between">
                        <a href="{{ route('booking.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Continue to Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize calendar
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            selectable: true,
            select: function(info) {
                // Format date as YYYY-MM-DD
                const year = info.start.getFullYear();
                const month = String(info.start.getMonth() + 1).padStart(2, '0');
                const day = String(info.start.getDate()).padStart(2, '0');
                const formattedDate = `${year}-${month}-${day}`;

                // Update hidden input
                document.getElementById('booking_date').value = formattedDate;

                // Update display
                document.getElementById('selected_date_text').textContent = formattedDate;
                document.getElementById('selected_date_display').classList.remove('d-none');

                // Calculate price
                calculatePrice();
            },
            validRange: {
                start: new Date() // Disable past dates
            }
        });
        calendar.render();

        // Initialize price calculation
        document.getElementById('service_id').addEventListener('change', calculatePrice);

        // Initial calculation if date already selected
        if (document.getElementById('booking_date').value) {
            calculatePrice();
        }

        // Price calculation function
        function calculatePrice() {
            const serviceSelect = document.getElementById('service_id');
            const bookingDateInput = document.getElementById('booking_date');

            if (!serviceSelect.value || !bookingDateInput.value) {
                resetPriceDisplay();
                return;
            }

            const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
            const basePrice = parseFloat(selectedOption.dataset.price) || 0;
            const bookingDate = new Date(bookingDateInput.value);

            // Check if weekend (0 = Sunday, 6 = Saturday)
            const isWeekend = bookingDate.getDay() === 0 || bookingDate.getDay() === 6;
            const weekendCharge = isWeekend ? 50000 : 0;
            const totalPrice = basePrice + weekendCharge;

            // Update price displays
            document.getElementById('base_price_display').textContent = formatCurrency(basePrice);
            document.getElementById('weekend_charge_display').textContent = formatCurrency(weekendCharge);
            document.getElementById('total_price_display').textContent = formatCurrency(totalPrice);
        }

        function resetPriceDisplay() {
            document.getElementById('base_price_display').textContent = 'Rp 0';
            document.getElementById('weekend_charge_display').textContent = 'Rp 0';
            document.getElementById('total_price_display').textContent = 'Rp 0';
        }

        function formatCurrency(amount) {
            return 'Rp ' + amount.toLocaleString('id-ID');
        }
    });
</script>
@endsection

