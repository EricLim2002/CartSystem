<?php

use App\Http\Controllers\ProfileController;

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::controller(CatalogController::class)->group(function () {
    Route::get('/', 'index')->name('catalog.index');
    Route::get('/products/{product}', 'show')->name('catalog.show');
});

// Cart Routes
Route::prefix('cart')->name('cart.')->controller(CartController::class)->group(function () {

    Route::get('/cart', function () {
        return Inertia::render('CartPage');
    })->name('index');

    Route::post('/add/{id}', 'add')->name('add');
    Route::post('/update/{id}', 'update')->name('update');
    Route::post('/remove/{id}', 'remove')->name('remove');
    Route::post('/place-order', 'placeOrder')->name('placeOrder');

    Route::get('/count', function () {
        $cart = session()->get('cart', []);
        return response()->json(['count' => collect($cart)->sum('qty')]);
    });

    Route::get('/data', function () {
        $cart = session()->get('cart', []);
        return response()->json([
            'cart' => array_map(function ($item) {
                $item['subtotal'] = $item['price'] * $item['qty'];
                return $item;
            }, $cart),
        ]);
    });


});

// Order Routes
Route::prefix('orders')->name('orders.')->controller(OrderController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{order}', 'show')->name('show');
    Route::post('/{order}/status', 'updateStatus')->name('updateStatus');
});


require __DIR__ . '/auth.php';
