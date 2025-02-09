<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = Auth::user();
    switch ($user->role) {
        case 'admin':
            return redirect()->route('admin.dashboard');
        case 'hotel':
            return redirect()->route('hotel.dashboard');
        default:
            return redirect()->route('user.dashboard');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
        ->middleware('role:admin')
        ->name('admin.dashboard');

    // User management
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/admin/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
    Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::get('/admin/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');


    // Hotel management
    Route::get('/admin/hotels', [AdminController::class, 'hotels'])->name('admin.hotels');
    Route::get('/admin/hotels/create', [AdminController::class, 'createHotel'])->name('admin.hotels.create');
    Route::post('/admin/hotels', [AdminController::class, 'storeHotel'])->name('admin.hotels.store');
    Route::get('/admin/hotels/{hotel}/edit', [AdminController::class, 'editHotel'])->name('admin.hotels.edit');
    Route::put('/admin/hotels/{hotel}', [AdminController::class, 'updateHotel'])->name('admin.hotels.update');

    // Hotel owner routes
    Route::middleware('role:hotel')->group(function () {
        Route::get('/hotel/dashboard', [HotelController::class, 'dashboard'])->name('hotel.dashboard');
        Route::get('/hotel/create', [HotelController::class, 'create'])->name('hotel.create');
        Route::post('/hotel', [HotelController::class, 'store'])->name('hotel.store');
        Route::get('/hotel/{hotel}/edit', [HotelController::class, 'edit'])->name('hotel.edit');
        Route::put('/hotel/{hotel}', [HotelController::class, 'update'])->name('hotel.update');
        Route::delete('/hotel/{hotel}', [HotelController::class, 'destroy'])->name('hotel.destroy');
        Route::get('/hotel/bookings', [HotelController::class, 'bookings'])->name('hotel.bookings');
        Route::patch('/hotel/booking/{booking}/confirm', [HotelController::class, 'confirmBooking'])->name('hotel.booking.confirm');
        Route::patch('/hotel/booking/{booking}/reject', [HotelController::class, 'rejectBooking'])->name('hotel.booking.reject');
        Route::get('/hotel/reviews', [HotelController::class, 'reviews'])->name('hotel.reviews');
        Route::post('/hotel/review/{review}/reply', [HotelController::class, 'replyToReview'])->name('hotel.review.reply');
    });

    // User routes
    Route::middleware('role:user')->group(function () {
        Route::get('/user/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
        Route::get('/user/help', [UserController::class, 'help'])->name('user.help');
        Route::get('/user/contact', [UserController::class, 'contact'])->name('user.contact');
        Route::get('/user/booking', [UserController::class, 'booking'])->name('user.booking');
        Route::get('/user/booking/{hotel}', [UserController::class, 'bookingCreate'])->name('user.booking.create');
        Route::post('/user/booking/{hotel}', [UserController::class, 'bookingStore'])->name('user.booking.store');
        Route::get('/user/edit', [UserController::class, 'edit'])->name('user.edit');
        Route::patch('/user/update', [UserController::class, 'update'])->name('user.update');
        Route::patch('/user/booking/{booking}/cancel', [UserController::class, 'cancelBooking'])->name('user.booking.cancel');
        Route::get('/user/booking/{booking}/review', [UserController::class, 'reviewCreate'])->name('user.review.create');
        Route::post('/user/booking/{booking}/review', [UserController::class, 'reviewStore'])->name('user.review.store');
        Route::get('/user/hotel/{hotel}', [UserController::class, 'showHotel'])->name('user.hotel.show');
        Route::get('/payment/{booking}', [UserController::class, 'showPayment'])->name('user.payment.show');
        Route::post('/payment/{booking}/process', [UserController::class, 'processPayment'])->name('user.payment.process');
        Route::get('/booking/{booking}/invoice', [UserController::class, 'showInvoice'])->name('user.booking.invoice');
    });

    // Recommendation route
    Route::get('/recommendations/{userId}', [RecommendationController::class, 'showRecommendations'])->name('recommendations');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Add this with other admin routes, after the hotel management routes
    Route::get('/admin/send-seasonal-email', [AdminController::class, 'sendSeasonalEmail'])
        ->middleware('role:admin')
        ->name('admin.send-seasonal-email');

    // Test email route
    Route::get('/test-email', function () {
    $details = "Test Email from Laravel 8! END NAMA";

    Mail::to(['edlinaeliz122@gmail.com'])->send(new TestEmail($details));
    });
});

require __DIR__ . '/auth.php';
