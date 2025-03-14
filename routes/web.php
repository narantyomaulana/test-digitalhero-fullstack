<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;


// Homepage/Booking routes
Route::get('/', [BookingController::class, 'index'])->name('booking.index');
Route::get('/booking/create', [BookingController::class, 'create'])->name('booking.create');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/{booking}/confirmation', [BookingController::class, 'confirmation'])->name('booking.confirmation');

// Payment routes
Route::get('/payment/{booking}/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
Route::post('/payment/notification', [PaymentController::class, 'notification'])->name('payment.notification');
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');