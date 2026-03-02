<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentCallbackController;
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
Route::get('/search', [HomeController::class, 'search'])->name('search');

// Guest Order Routes (MVStore API)
Route::post('/order/validate-account', [OrderController::class, 'validateAccount'])->name('order.validate');
Route::post('/order/create', [OrderController::class, 'create'])->name('order.create');
Route::get('/order/payment', [OrderController::class, 'payment'])->name('order.payment');
Route::post('/order/submit', [OrderController::class, 'submit'])->name('order.submit');
Route::get('/order/status', [OrderController::class, 'status'])->name('order.status');
Route::post('/order/track', [OrderController::class, 'track'])->name('order.track');
Route::post('/order/refresh-status', [OrderController::class, 'refreshStatus'])->name('order.refresh-status');

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
        Route::post('/balance/topup', [DashboardController::class, 'topup'])->name('balance.topup');
        Route::get('/bonus-files', [DashboardController::class, 'bonusFiles'])->name('bonus-files');
        Route::get('/bonus-file/{bonusFile}/download', [DashboardController::class, 'downloadBonusFile'])->name('bonus-file.download');
        Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
        Route::put('/profile/password', [DashboardController::class, 'changePassword'])->name('profile.change-password');
        Route::delete('/profile/delete', [DashboardController::class, 'deleteAccount'])->name('profile.delete');
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
        Route::patch('categories/{category}/toggle-status', [Admin\CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
        Route::patch('categories/{category}/inline-update', [Admin\CategoryController::class, 'updateInline'])->name('categories.inline-update');

        // Products
        Route::resource('products', Admin\ProductController::class);
        Route::post('products/sync', [Admin\ProductController::class, 'sync'])->name('products.sync');
        Route::patch('products/{product}/toggle-status', [Admin\ProductController::class, 'toggleStatus'])->name('products.toggle-status');

        // Transactions
        Route::resource('transactions', Admin\TransactionController::class)->only(['index', 'show', 'update', 'destroy']);
        Route::post('transactions/{transaction}/process', [Admin\TransactionController::class, 'process'])->name('transactions.process');
        Route::post('transactions/{transaction}/refund', [Admin\TransactionController::class, 'refund'])->name('transactions.refund');
        Route::post('transactions/{transaction}/cancel', [Admin\TransactionController::class, 'cancel'])->name('transactions.cancel');
        Route::post('transactions/{transaction}/resend-notification', [Admin\TransactionController::class, 'resendNotification'])->name('transactions.resend-notification');
        Route::patch('transactions/{transaction}/status', [Admin\TransactionController::class, 'updateStatus'])->name('transactions.update-status');
        Route::patch('transactions/{transaction}/note', [Admin\TransactionController::class, 'updateNote'])->name('transactions.update-note');
        Route::post('transactions/{transaction}/send-order', [Admin\TransactionController::class, 'sendOrder'])->name('transactions.send-order');
        Route::post('transactions/process-all', [Admin\TransactionController::class, 'processAll'])->name('transactions.process-all');
        Route::get('transactions-export', [Admin\TransactionController::class, 'export'])->name('transactions.export');
        Route::post('transactions-import', [Admin\TransactionController::class, 'import'])->name('transactions.import');
        Route::post('transactions/{id}/restore', [Admin\TransactionController::class, 'restore'])->name('transactions.restore');
        Route::delete('transactions/{id}/force-delete', [Admin\TransactionController::class, 'forceDelete'])->name('transactions.force-delete');

        // Members
        Route::resource('members', Admin\MemberController::class);
        Route::post('members/{member}/add-balance', [Admin\MemberController::class, 'addBalance'])->name('members.add-balance');
        Route::post('members/{member}/deduct-balance', [Admin\MemberController::class, 'deductBalance'])->name('members.deduct-balance');

        // Settings
        Route::get('settings', [Admin\SettingsController::class, 'index'])->name('settings.index');
        Route::put('settings', [Admin\SettingsController::class, 'update'])->name('settings.update');
        Route::put('settings/provider/{id}', [Admin\SettingsController::class, 'updateProvider'])->name('settings.provider.update');
        Route::post('settings/provider/{id}/test', [Admin\SettingsController::class, 'testConnection'])->name('settings.provider.test');
        Route::post('settings/sync-products', [Admin\SettingsController::class, 'syncProducts'])->name('settings.sync-products');

        // Content Management
        Route::get('content', [Admin\ContentController::class, 'index'])->name('content.index');
        // Reviews
        Route::post('content/reviews', [Admin\ContentController::class, 'storeReview'])->name('content.reviews.store');
        Route::put('content/reviews/{review}', [Admin\ContentController::class, 'updateReview'])->name('content.reviews.update');
        Route::delete('content/reviews/{review}', [Admin\ContentController::class, 'destroyReview'])->name('content.reviews.destroy');
        Route::patch('content/reviews/{review}/toggle', [Admin\ContentController::class, 'toggleReviewStatus'])->name('content.reviews.toggle');
        // FAQ
        Route::post('content/faqs', [Admin\ContentController::class, 'storeFaq'])->name('content.faqs.store');
        Route::put('content/faqs/{faq}', [Admin\ContentController::class, 'updateFaq'])->name('content.faqs.update');
        Route::delete('content/faqs/{faq}', [Admin\ContentController::class, 'destroyFaq'])->name('content.faqs.destroy');
        Route::patch('content/faqs/{faq}/toggle', [Admin\ContentController::class, 'toggleFaqStatus'])->name('content.faqs.toggle');
        // Media Coverage
        Route::post('content/medias', [Admin\ContentController::class, 'storeMedia'])->name('content.medias.store');
        Route::put('content/medias/{media}', [Admin\ContentController::class, 'updateMedia'])->name('content.medias.update');
        Route::delete('content/medias/{media}', [Admin\ContentController::class, 'destroyMedia'])->name('content.medias.destroy');
        // Bonus Files
        Route::post('content/files', [Admin\ContentController::class, 'storeFile'])->name('content.files.store');
        Route::put('content/files/{file}', [Admin\ContentController::class, 'updateFile'])->name('content.files.update');
        Route::delete('content/files/{file}', [Admin\ContentController::class, 'destroyFile'])->name('content.files.destroy');
        Route::patch('content/files/{file}/toggle', [Admin\ContentController::class, 'toggleFileStatus'])->name('content.files.toggle');
        // Pages (Informasi)
        Route::post('content/pages', [Admin\ContentController::class, 'storePage'])->name('content.pages.store');
        Route::put('content/pages/{page}', [Admin\ContentController::class, 'updatePage'])->name('content.pages.update');
        Route::delete('content/pages/{page}', [Admin\ContentController::class, 'destroyPage'])->name('content.pages.destroy');
        Route::patch('content/pages/{page}/toggle', [Admin\ContentController::class, 'togglePageStatus'])->name('content.pages.toggle');

        // Notifications
        Route::get('notifications', [Admin\NotificationController::class, 'index'])->name('notifications.index');
        Route::put('notifications', [Admin\NotificationController::class, 'update'])->name('notifications.update');
        Route::post('notifications/{notification}/test', [Admin\NotificationController::class, 'test'])->name('notifications.test');
        Route::post('notifications/test-all', [Admin\NotificationController::class, 'testAll'])->name('notifications.testAll');

        // Security
        Route::get('security', [Admin\SecurityController::class, 'index'])->name('security.index');
        Route::put('security', [Admin\SecurityController::class, 'update'])->name('security.update');

        // Auto Delete
        Route::get('auto-delete', [Admin\AutoDeleteController::class, 'index'])->name('auto-delete.index');
        Route::put('auto-delete', [Admin\AutoDeleteController::class, 'update'])->name('auto-delete.update');
        Route::post('auto-delete/{setting}/run', [Admin\AutoDeleteController::class, 'runNow'])->name('auto-delete.run');

        // Activity Logs
        Route::get('activity-logs', [Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('activity-logs/{log}', [Admin\ActivityLogController::class, 'show'])->name('activity-logs.show');
        Route::delete('activity-logs/{log}', [Admin\ActivityLogController::class, 'destroy'])->name('activity-logs.destroy');
        Route::delete('activity-logs', [Admin\ActivityLogController::class, 'clear'])->name('activity-logs.clear');
    });

// Payment Callbacks (No middleware - external webhooks)
Route::post('/callback/midtrans', [PaymentCallbackController::class, 'midtrans'])->name('callback.midtrans');
