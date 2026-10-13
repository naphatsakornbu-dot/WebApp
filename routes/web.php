<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CourtController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PlayerMatchController;
use App\Http\Controllers\ReviewController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\CourtController as AdminCourtController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\EquipmentController as AdminEquipmentController;
use App\Http\Controllers\Admin\MaintenanceController as AdminMaintenanceController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/courts', [CourtController::class, 'index'])->name('courts.index');
Route::get('/courts/{court}', [CourtController::class, 'show'])->name('courts.show');
Route::get('/matches', [PlayerMatchController::class, 'index'])->name('matches.index');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Booking Engine
    Route::get('/book', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/book', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/my-bookings', [BookingController::class, 'myBookings'])->name('bookings.my');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

    // Payments & Slips
    Route::get('/bookings/{booking}/payment', [PaymentController::class, 'show'])->name('payments.show');
    Route::post('/bookings/{booking}/payment', [PaymentController::class, 'uploadSlip'])->name('payments.upload');

    // Player Matchmaking (LFG)
    Route::get('/bookings/{booking}/matches/create', [PlayerMatchController::class, 'create'])->name('matches.create');
    Route::post('/bookings/{booking}/matches', [PlayerMatchController::class, 'store'])->name('matches.store');
    Route::post('/matches/{match}/join', [PlayerMatchController::class, 'join'])->name('matches.join');
    Route::post('/matches/members/{member}/respond', [PlayerMatchController::class, 'respond'])->name('matches.respond');

    // Verified Reviews
    Route::post('/bookings/{booking}/review', [ReviewController::class, 'store'])->name('reviews.store');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Role Protected via AdminMiddleware)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard & Admin Home
    Route::get('/', [AdminDashboardController::class, 'index'])->name('index');
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Court Management
    Route::resource('courts', AdminCourtController::class);

    // Booking Management
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
    Route::put('/bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.status');

    // Payment & Slip Approvals
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/{payment}/approve', [AdminPaymentController::class, 'approve'])->name('payments.approve');
    Route::post('/payments/{payment}/reject', [AdminPaymentController::class, 'reject'])->name('payments.reject');

    // Equipment Management
    Route::resource('equipments', AdminEquipmentController::class);

    // Court Maintenance
    Route::get('/maintenances', [AdminMaintenanceController::class, 'index'])->name('maintenances.index');
    Route::get('/maintenances/create', [AdminMaintenanceController::class, 'create'])->name('maintenances.create');
    Route::post('/maintenances', [AdminMaintenanceController::class, 'store'])->name('maintenances.store');
    Route::delete('/maintenances/{maintenance}', [AdminMaintenanceController::class, 'destroy'])->name('maintenances.destroy');

    // Reviews Management
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

    // User Management
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users/{user}/toggle-role', [AdminUserController::class, 'toggleRole'])->name('users.toggle-role');

    // Multi-table Analytics & Reports
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
});
