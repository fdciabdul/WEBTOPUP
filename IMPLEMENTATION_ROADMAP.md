# Web Top-Up Implementation Roadmap

## Project Overview
Complete Laravel 12 web top-up system with payment gateway integration, API integration, and comprehensive admin dashboard.

## ✅ COMPLETED - Phase 1: Database Foundation

### Database Migrations Created
1. ✅ Categories table
2. ✅ Products table (with multi-level pricing)
3. ✅ Users table extension (roles, levels, balance)
4. ✅ Transactions table (complete order flow)
5. ✅ Balance histories table
6. ✅ Settings table
7. ✅ Reviews table
8. ✅ FAQs table
9. ✅ Media coverages table
10. ✅ Pages table
11. ✅ Bonus files table
12. ✅ Log activities table
13. ✅ Fees table

### Models Created
1. ✅ User (extended with balance management)
2. ✅ Category
3. ✅ Product (with multi-level pricing)
4. ✅ Transaction (complete order lifecycle)
5. ✅ BalanceHistory
6. ✅ Setting (with cache)
7. ✅ LogActivity
8. ✅ Review, FAQ, MediaCoverage, Page, BonusFile, Fee

---

## 🚀 NEXT STEPS - Implementation Phases

### Phase 2: Core Services & API Integration (Priority: CRITICAL)

#### 2.1 DigiFlazz API Service
**Files to create:**
- `app/Services/DigiFlazzService.php`
- `config/digiflazz.php`

**Features:**
- Product price list sync
- Stock check
- Order processing
- Order status checking
- Webhook handling

**Code structure:**
```php
class DigiFlazzService {
    - syncProducts(): Sync all products from API
    - checkPrice($productCode): Get current price
    - createOrder($data): Place order
    - checkOrderStatus($orderId): Check order status
    - handleCallback($data): Process callback
}
```

#### 2.2 Midtrans Payment Gateway Service
**Files to create:**
- `app/Services/MidtransService.php`
- `config/midtrans.php`
- `app/Http/Controllers/PaymentCallbackController.php`

**Features:**
- Create transaction snap
- Check transaction status
- Handle notification webhook
- Verify signature
- Process refund

**Payment methods supported:**
- Bank Transfer (BCA, BNI, BRI, Mandiri, Permata)
- E-Wallet (GoPay, ShopeePay, OVO, DANA)
- QRIS
- Convenience Store (Alfamart, Indomaret)

### Phase 3: Transaction Flow (Priority: CRITICAL)

#### 3.1 Order Processing Service
**File:** `app/Services/OrderService.php`

**Flow:**
1. Validate product & stock
2. Calculate price by user level
3. Check user balance (if using balance)
4. Create transaction record
5. Initiate payment (if using payment gateway)
6. Process order to provider API
7. Handle provider response
8. Update transaction status
9. Send notifications
10. Update user statistics

#### 3.2 Transaction Management
**Controllers needed:**
- `app/Http/Controllers/TransactionController.php`
- `app/Http/Controllers/CheckoutController.php`

**Key methods:**
```php
- createOrder(Request $request): Create new order
- processPayment(Transaction $transaction): Process payment
- processTopUp(Transaction $transaction): Send to DigiFlazz
- completeOrder(Transaction $transaction): Mark as complete
- refundOrder(Transaction $transaction): Process refund
- cancelOrder(Transaction $transaction): Cancel order
```

### Phase 4: Queue Jobs (Priority: HIGH)

**Files to create:**
- `app/Jobs/ProcessTopUpJob.php` - Process top-up to DigiFlazz
- `app/Jobs/SendWhatsAppNotificationJob.php` - Send WhatsApp notification
- `app/Jobs/SendEmailNotificationJob.php` - Send email notification
- `app/Jobs/SyncProductsJob.php` - Sync products from DigiFlazz
- `app/Jobs/AutoDeleteExpiredTransactionsJob.php` - Auto-delete unpaid
- `app/Jobs/AutoDeleteOldLogsJob.php` - Auto-delete old logs
- `app/Jobs/CheckPendingOrdersJob.php` - Check pending order status

**Schedule in:** `app/Console/Kernel.php`
```php
$schedule->job(new SyncProductsJob)->hourly();
$schedule->job(new CheckPendingOrdersJob)->everyFiveMinutes();
$schedule->job(new AutoDeleteExpiredTransactionsJob)->daily();
$schedule->job(new AutoDeleteOldLogsJob)->daily();
```

### Phase 5: Authentication & Security (Priority: CRITICAL)

#### 5.1 Authentication System
**Controllers:**
- `app/Http/Controllers/Auth/LoginController.php`
- `app/Http/Controllers/Auth/RegisterController.php`
- `app/Http/Controllers/Auth/ForgotPasswordController.php`

#### 5.2 Rate Limiting Middleware
**File:** `app/Http/Middleware/CustomRateLimiter.php`

**Limits (from requirements):**
- Login: 5x per minute
- Register: 1x per hour
- Order: 3x per minute
- Forgot Password: 1x per 10 minutes

**Implementation:**
```php
RateLimiter::for('login', function (Request $request) {
    return Limit::perMinute(5)->by($request->ip());
});
```

#### 5.3 Security Middleware
**Files to create:**
- `app/Http/Middleware/LogActivity.php` - Auto-log all activities
- `app/Http/Middleware/CheckUserStatus.php` - Check if user is active
- `app/Http/Middleware/ValidateSignature.php` - Validate payment signatures

### Phase 6: Notification System (Priority: HIGH)

#### 6.1 WhatsApp Notification Service
**File:** `app/Services/WhatsAppService.php`

**Integration options:**
- Fonnte API
- Wablas API

**Templates needed:**
- Order confirmation
- Payment received
- Top-up processing
- Top-up success (with credentials)
- Order failed
- Refund processed

#### 6.2 Email Notification Service
**Files:**
- `app/Services/EmailService.php`
- `resources/views/emails/order-confirmation.blade.php`
- `resources/views/emails/payment-success.blade.php`
- `resources/views/emails/topup-success.blade.php`
- `resources/views/emails/order-failed.blade.php`

### Phase 7: Admin Dashboard (Priority: HIGH)

#### 7.1 Dashboard Overview
**Controller:** `app/Http/Controllers/Admin/DashboardController.php`

**Statistics to show:**
- Total items sold (with date filters)
- Total orders
- Total products
- Total members
- Total revenue
- Top categories
- Top products (best sellers)
- Recent transactions
- Recently registered users

#### 7.2 Admin CRUD Controllers
**Files to create:**
1. `app/Http/Controllers/Admin/CategoryController.php`
2. `app/Http/Controllers/Admin/ProductController.php`
3. `app/Http/Controllers/Admin/TransactionController.php`
4. `app/Http/Controllers/Admin/MemberController.php`
5. `app/Http/Controllers/Admin/ReviewController.php`
6. `app/Http/Controllers/Admin/FaqController.php`
7. `app/Http/Controllers/Admin/MediaCoverageController.php`
8. `app/Http/Controllers/Admin/PageController.php`
9. `app/Http/Controllers/Admin/BonusFileController.php`
10. `app/Http/Controllers/Admin/FeeController.php`
11. `app/Http/Controllers/Admin/SettingController.php`
12. `app/Http/Controllers/Admin/LogActivityController.php`

**Each controller needs:**
- index() - List with filters, search, sort
- create() - Show create form
- store() - Save new record
- show() - Show detail
- edit() - Show edit form
- update() - Update record
- destroy() - Delete record

#### 7.3 Special Admin Features
**Transaction Management:**
- Bulk status update
- Manual order processing
- Admin notes
- Resend notification
- Manual refund

**Member Management:**
- Add balance manually
- Adjust member level
- View transaction history
- Block/unblock user

### Phase 8: Blade Views & Frontend (Priority: HIGH)

#### 8.1 Convert HTML Templates to Blade
**Template files in folder:**
- Homepage.html → `resources/views/homepage.blade.php`
- Detail Produk.html → `resources/views/product-detail.blade.php`
- Pembayaran.html → `resources/views/checkout.blade.php`
- Register.html → `resources/views/auth/register.blade.php`
- Cek Pesanan.html → `resources/views/track-order.blade.php`
- Dashboard/Dashboard Utama.html → `resources/views/dashboard/index.blade.php`

#### 8.2 Layout Structure
**Create:**
- `resources/views/layouts/app.blade.php` - Main layout
- `resources/views/layouts/admin.blade.php` - Admin layout
- `resources/views/layouts/dashboard.blade.php` - Member dashboard layout
- `resources/views/components/header.blade.php`
- `resources/views/components/footer.blade.php`
- `resources/views/components/navbar.blade.php`

#### 8.3 Admin Dashboard Views
**Directory:** `resources/views/admin/`

**Views needed:**
- dashboard.blade.php
- categories/index.blade.php, create.blade.php, edit.blade.php
- products/index.blade.php, create.blade.php, edit.blade.php
- transactions/index.blade.php, show.blade.php
- members/index.blade.php, create.blade.php, edit.blade.php
- settings/general.blade.php, payment.blade.php, api.blade.php, notification.blade.php, security.blade.php
- logs/index.blade.php

### Phase 9: Routes Configuration (Priority: MEDIUM)

**File:** `routes/web.php`

**Route groups needed:**
```php
// Public routes
Route::get('/', 'HomeController@index')->name('home');
Route::get('/product/{slug}', 'ProductController@show')->name('product.show');
Route::get('/track-order', 'OrderTrackingController@index')->name('track.order');
Route::post('/check-order', 'OrderTrackingController@check')->name('check.order');

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('/login', 'Auth\LoginController@login');
    Route::get('/register', 'Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('/register', 'Auth\RegisterController@register');
});

// Member dashboard
Route::middleware(['auth', 'member'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', 'DashboardController@index')->name('index');
    Route::get('/transactions', 'DashboardController@transactions')->name('transactions');
    Route::get('/balance', 'DashboardController@balance')->name('balance');
});

// Checkout & payment
Route::middleware('auth')->group(function () {
    Route::post('/checkout', 'CheckoutController@process')->name('checkout.process');
    Route::get('/payment/{order_id}', 'CheckoutController@payment')->name('payment');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', 'Admin\DashboardController@index')->name('dashboard');
    Route::resource('categories', 'Admin\CategoryController');
    Route::resource('products', 'Admin\ProductController');
    Route::resource('transactions', 'Admin\TransactionController');
    Route::resource('members', 'Admin\MemberController');
    // ... more admin resources
});

// Payment callbacks
Route::post('/callback/midtrans', 'PaymentCallbackController@midtrans')->name('callback.midtrans');
Route::post('/callback/digiflazz', 'DigiFlazzCallbackController@handle')->name('callback.digiflazz');
```

### Phase 10: Additional Features (Priority: MEDIUM)

#### 10.1 Product Features
- Auto-sync products from DigiFlazz
- Bulk import/export products
- Product stock alerts
- Featured products management

#### 10.2 Member Features
- Deposit balance via payment gateway
- Download transaction invoices (PDF)
- Download bonus files
- Referral system (optional)

#### 10.3 SEO Features
**File:** `app/Http/Controllers/SitemapController.php`
- Dynamic sitemap.xml generation
- Robots.txt configuration
- Meta tags management
- Open Graph tags

### Phase 11: Caching Strategy (Priority: MEDIUM)

**Implementation:**
```php
// Cache settings
Cache::remember('settings', 3600, function () {
    return Setting::all()->pluck('value', 'key');
});

// Cache active products
Cache::remember('active_products', 1800, function () {
    return Product::active()->inStock()->get();
});

// Cache categories
Cache::remember('categories', 3600, function () {
    return Category::active()->with('products')->get();
});
```

**Clear cache on:**
- Product update
- Category update
- Settings update
- After sync from API

### Phase 12: Environment Configuration (Priority: CRITICAL)

**File:** `.env`

**Required variables:**
```env
# App
APP_NAME="Web Top Up"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://webtopup.imtaqin.id

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# DigiFlazz API
DIGIFLAZZ_USERNAME=your_username
DIGIFLAZZ_API_KEY=your_api_key
DIGIFLAZZ_WEBHOOK_SECRET=your_webhook_secret
DIGIFLAZZ_ENVIRONMENT=production

# Midtrans
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_CLIENT_KEY=your_client_key
MIDTRANS_ENVIRONMENT=production
MIDTRANS_SANITIZED=true
MIDTRANS_3DS=true

# WhatsApp (Fonnte)
WHATSAPP_API_KEY=your_api_key
WHATSAPP_SENDER=your_phone_number

# Email
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@webtopup.imtaqin.id
MAIL_FROM_NAME="${APP_NAME}"

# Queue
QUEUE_CONNECTION=database

# Cache
CACHE_DRIVER=file
SESSION_DRIVER=file
```

### Phase 13: Security Hardening (Priority: CRITICAL)

**Checklist:**
- [x] CSRF protection enabled
- [ ] XSS protection (use `{{ }}` in Blade, never `{!! !!}` for user input)
- [ ] SQL injection protection (use Eloquent ORM, avoid raw queries)
- [ ] Rate limiting on all critical endpoints
- [ ] Signature validation for payment callbacks
- [ ] IP whitelist for API callbacks
- [ ] Secure session configuration
- [ ] HTTPS enforcement
- [ ] Secure headers middleware

**Add to `.htaccess`:**
```apache
# Force HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Security Headers
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-XSS-Protection "1; mode=block"
Header set Referrer-Policy "strict-origin-when-cross-origin"
```

### Phase 14: Testing & Quality Assurance (Priority: HIGH)

**Test cases:**
1. User registration & login
2. Product browsing & filtering
3. Order placement flow
4. Payment gateway integration
5. Top-up processing
6. Notification delivery
7. Refund process
8. Admin CRUD operations
9. Rate limiting
10. Error handling

### Phase 15: Deployment Checklist (Priority: CRITICAL)

**Before going live:**
- [ ] Run `php artisan migrate` on production database
- [ ] Run `php artisan db:seed` for initial data
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Set proper file permissions (storage/, bootstrap/cache/)
- [ ] Configure queue worker: `php artisan queue:work --daemon`
- [ ] Set up cron job: `* * * * * cd /path && php artisan schedule:run >> /dev/null 2>&1`
- [ ] Configure SSL certificate
- [ ] Set up daily database backups
- [ ] Configure error logging & monitoring
- [ ] Test all payment methods
- [ ] Test WhatsApp & email notifications
- [ ] Test DigiFlazz API integration

---

## 📦 Required Packages

Add to `composer.json`:
```json
{
    "require": {
        "midtrans/midtrans-php": "^2.5",
        "guzzlehttp/guzzle": "^7.8",
        "barryvdh/laravel-dompdf": "^3.0",
        "spatie/laravel-permission": "^6.0"
    }
}
```

Run: `composer install`

---

## 🎯 Priority Implementation Order

1. **Week 1-2: Core Foundation**
   - DigiFlazz API Service
   - Midtrans Payment Service
   - Transaction flow
   - Queue jobs

2. **Week 3: Security & Auth**
   - Authentication system
   - Rate limiting
   - Security middleware
   - Log activity tracking

3. **Week 4-5: Frontend & Views**
   - Convert HTML templates to Blade
   - Build admin dashboard views
   - Build member dashboard views
   - Responsive design fixes

4. **Week 6-7: Admin Panel**
   - Admin controllers
   - CRUD operations
   - Statistics dashboard
   - Settings management

5. **Week 8: Notifications & Features**
   - WhatsApp integration
   - Email integration
   - Notification templates
   - Additional features

6. **Week 9: Testing & Optimization**
   - Full system testing
   - Performance optimization
   - Security audit
   - Bug fixes

7. **Week 10: Deployment**
   - Production deployment
   - Final testing
   - Documentation
   - Training

---

## 📞 Support & Documentation

**Laravel 12 Documentation:** https://laravel.com/docs/12.x
**Midtrans Documentation:** https://docs.midtrans.com
**DigiFlazz API Documentation:** Check your DigiFlazz dashboard

---

## ⚠️ CRITICAL NOTES

1. **Never commit `.env` file to git**
2. **Always validate and sanitize user input**
3. **Test payment callbacks in sandbox before production**
4. **Implement database transactions for critical operations**
5. **Log all important events**
6. **Set up monitoring and alerts**
7. **Regular database backups**
8. **Keep Laravel and packages updated**

---

## 🔄 Next Immediate Steps

Would you like me to:
1. **Create the DigiFlazz Service** - Core API integration
2. **Create the Midtrans Service** - Payment gateway
3. **Build the Order Service** - Complete transaction flow
4. **Create Authentication Controllers** - Login/Register with rate limiting
5. **Start building Admin Dashboard** - Controllers and views

Let me know which component you want me to build first!
