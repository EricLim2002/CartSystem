<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
//
// Public routes (no auth required)
//


// Product catalogue
Route::get('/', [ProductController::class, 'index'])->name('catalogue');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// Cart endpoints (AJAX-friendly, Inertia page handled in Vue)
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'show'])->name('show');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::post('/update', [CartController::class, 'update'])->name('update');
    Route::post('/clear', [CartController::class, 'clear'])->name('clear');
});

// Checkout
Route::post('/checkout', [OrderController::class, 'store'])->name('checkout');

// Login page (Inertia Vue page instead of placeholder)
Route::get('/login', function () {
    return Inertia::render('Auth/Login'); // resources/js/Pages/Auth/Login.vue
})->name('login');


Route::get('/register', function () {
    return Inertia::render('Auth/Register'); // resources/js/Pages/Auth/Register.vue
})->name('register');


Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Authenticated user routes
//
Route::middleware('auth')->name('user.')->group(function () {
    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // Dashboard

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    

});
