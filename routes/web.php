<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Models\Product;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\VariationController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\RestockController;


// Customer routes (public)
Route::get('/', [\App\Http\Controllers\Customer\ProductController::class, 'index'])->name('home');
Route::get('/shop', [\App\Http\Controllers\Customer\ProductController::class, 'index'])->name('shop.index');
Route::get('/shop/{product}', [\App\Http\Controllers\Customer\ProductController::class, 'show'])->name('shop.show');

// Cart routes (public)
Route::get('/cart', [\App\Http\Controllers\Customer\CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [\App\Http\Controllers\Customer\CartController::class, 'add'])->name('cart.add');
Route::post('/cart/{key}/update', [\App\Http\Controllers\Customer\CartController::class, 'update'])->name('cart.update');
Route::post('/cart/{key}/remove', [\App\Http\Controllers\Customer\CartController::class, 'remove'])->name('cart.remove');

// Checkout routes
Route::get('/checkout', [\App\Http\Controllers\Customer\CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [\App\Http\Controllers\Customer\CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/confirmation/{order}', [\App\Http\Controllers\Customer\CheckoutController::class, 'confirmation'])->name('checkout.confirmation');
Route::post('/checkout/payment/{order}', [\App\Http\Controllers\Customer\CheckoutController::class, 'processPayment'])->name('checkout.payment');

// Order routes (requires auth for viewing orders)
Route::get('/orders', [\App\Http\Controllers\Customer\OrderController::class, 'index'])->middleware('auth')->name('orders.index');
Route::get('/orders/{order}', [\App\Http\Controllers\Customer\OrderController::class, 'show'])->name('orders.show');
Route::post('/orders/{order}/cancel', [\App\Http\Controllers\Customer\OrderController::class, 'cancel'])->middleware('auth')->name('orders.cancel');

// Wishlist routes (requires auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/wishlist', [\App\Http\Controllers\Customer\WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist', [\App\Http\Controllers\Customer\WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/wishlist/{wishlist}', [\App\Http\Controllers\Customer\WishlistController::class, 'destroy'])->name('wishlist.destroy');
});

// Review routes (requires auth)
Route::middleware(['auth'])->group(function () {
    Route::post('/products/{product}/reviews', [\App\Http\Controllers\Customer\ReviewController::class, 'store'])->name('reviews.store');
});

// Notification routes (requires auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('/notifications/{notification}/open', [\App\Http\Controllers\NotificationController::class, 'open'])->name('notifications.open');
    Route::get('/api/notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::get('/api/notifications/latest', [\App\Http\Controllers\NotificationController::class, 'getLatest'])->name('notifications.latest');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/sign-in', [AuthController::class, 'showLoginForm'])->name('sign-in');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Seller routes (requires auth)
Route::resource('categories', CategoryController::class)->middleware('auth');
Route::resource('products', ProductController::class)->middleware('auth');
Route::resource('suppliers', SupplierController::class)->middleware('auth');
Route::prefix('products/{product}')->middleware('auth')->group(function () {
    Route::post('variations', [VariationController::class, 'store'])->name('variations.store');
    Route::get('variations/{variation}/edit', [VariationController::class, 'edit'])->name('variations.edit');
    Route::put('variations/{variation}', [VariationController::class, 'update'])->name('variations.update');
    Route::delete('variations/{variation}', [VariationController::class, 'destroy'])->name('variations.destroy');
});
Route::post('variations/{variation}/restock', [RestockController::class, 'storeVariation'])->middleware('auth')->name('variations.restock');
Route::middleware(['auth'])->group(function () {
    Route::get('/restocks', [RestockController::class, 'index'])->name('restocks.index');
    Route::get('/restocks/create', [RestockController::class, 'create'])->name('restocks.create');
    Route::post('/restocks', [RestockController::class, 'store'])->name('restocks.store');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

// Seller Order Management routes (requires auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/seller/orders', [\App\Http\Controllers\OrderController::class, 'index'])->name('seller.orders.index');
    Route::get('/seller/orders/{order}', [\App\Http\Controllers\OrderController::class, 'show'])->name('seller.orders.show');
    Route::post('/seller/orders/{order}/status', [\App\Http\Controllers\OrderController::class, 'updateStatus'])->name('seller.orders.update-status');
    Route::post('/seller/orders/{order}/payment-status', [\App\Http\Controllers\OrderController::class, 'updatePaymentStatus'])->name('seller.orders.update-payment-status');
});

// Analytics & Reports routes (requires auth - seller only)
Route::middleware(['auth'])->group(function () {
    Route::get('/analytics', [\App\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::post('/analytics/generate-report', [\App\Http\Controllers\AnalyticsController::class, 'generateReport'])->name('analytics.generate-report');
});
