<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\PaymentCallbackController;
use App\Http\Controllers\DigiFlazzCallbackController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Admin;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/category/{slug}', [HomeController::class, 'category'])->name('category');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');
Route::get('/search', [ProductController::class, 'search'])->name('search');

// Order Tracking
Route::get('/track-order', [OrderTrackingController::class, 'index'])->name('track.order');
Route::post('/check-order', [OrderTrackingController::class, 'check'])->name('check.order');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Checkout & Payment
Route::middleware('auth')->group(function () {
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/payment/{order_id}', [CheckoutController::class, 'payment'])->name('payment');
});

// Member Dashboard
Route::middleware(['auth', App\Http\Middleware\IsMember::class, App\Http\Middleware\CheckUserStatus::class])
    ->prefix('dashboard')
    ->name('dashboard.')
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/transactions', [DashboardController::class, 'transactions'])->name('transactions');
        Route::get('/transaction/{order_id}', [DashboardController::class, 'transactionDetail'])->name('transaction.detail');
        Route::get('/balance', [DashboardController::class, 'balance'])->name('balance');
        Route::get('/bonus-files', [DashboardController::class, 'bonusFiles'])->name('bonus-files');
        Route::get('/bonus-file/{bonusFile}/download', [DashboardController::class, 'downloadBonusFile'])->name('bonus-file.download');
        Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
    });

// Admin Routes
Route::middleware(['auth', App\Http\Middleware\IsAdmin::class, App\Http\Middleware\CheckUserStatus::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Dashboard
        Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

        // Categories
        Route::resource('categories', Admin\CategoryController::class);

        // Products
        Route::resource('products', Admin\ProductController::class);
        Route::post('products/sync', [Admin\ProductController::class, 'sync'])->name('products.sync');

        // Transactions
        Route::resource('transactions', Admin\TransactionController::class)->only(['index', 'show', 'update']);
        Route::post('transactions/{transaction}/process', [Admin\TransactionController::class, 'process'])->name('transactions.process');
        Route::post('transactions/{transaction}/refund', [Admin\TransactionController::class, 'refund'])->name('transactions.refund');
        Route::post('transactions/{transaction}/cancel', [Admin\TransactionController::class, 'cancel'])->name('transactions.cancel');
        Route::post('transactions/{transaction}/resend-notification', [Admin\TransactionController::class, 'resendNotification'])->name('transactions.resend-notification');

        // Members
        Route::resource('members', Admin\MemberController::class);
        Route::post('members/{member}/add-balance', [Admin\MemberController::class, 'addBalance'])->name('members.add-balance');
        Route::post('members/{member}/deduct-balance', [Admin\MemberController::class, 'deductBalance'])->name('members.deduct-balance');
    });

// Payment Callbacks (No middleware - external webhooks)
Route::post('/callback/midtrans', [PaymentCallbackController::class, 'midtrans'])->name('callback.midtrans');
Route::post('/callback/digiflazz', [DigiFlazzCallbackController::class, 'handle'])->name('callback.digiflazz');
