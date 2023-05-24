<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\SearchController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('welcome');

Route::get('/contact', function () {
    return Inertia::render('Contact');
})->name('contact');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::post('/search', [SearchController::class, 'search'])->name('search.submit');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/profile/trips', [ProfileController::class, 'trips'])->name('profile.trips');
    Route::get('/profile/bookings', [ProfileController::class, 'bookings'])->name('profile.bookings');

    Route::get('/trip', [TripController::class, 'index'])->name('trip.create');
    Route::post('/trip', [TripController::class, 'store'])->name('trip.store');
    Route::get('/trip/{trip}', [TripController::class, 'show'])->name('trip.show');
    Route::post('/trip/{trip}/book', [TripController::class, 'book'])->name('trip.book');
    Route::post('/trip/{trip}/approve', [TripController::class, 'approve'])->name('trip.approve_booking');
    Route::post('/trip/{trip}/user_cancel_booking', [TripController::class, 'userCancelBooking'])->name('trip.user_cancel_booking');
    Route::post('/trip/{trip}/cancel_booking', [TripController::class, 'CancelBooking'])->name('trip.cancel_booking');
    Route::post('/trip/{trip}/message', [TripController::class, 'sendMessage'])->name('trip.sendMessage');

    // Route::patch('/trip', [ProfileController::class, 'update'])->name('trip.update');
    // Route::delete('/trip', [ProfileController::class, 'destroy'])->name('trip.destroy');
});

require __DIR__.'/auth.php';
