<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\ArticleController as PublicArticleController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::prefix(LaravelLocalization::setLocale())->middleware([
    'localeSessionRedirect', 'localizationRedirect', 'localeViewPath'
])->group(function () {

    // Public pharmacy site
    Route::get('/', [MedicineController::class, 'home'])->name('home');
    Route::get('/medicines', [MedicineController::class, 'index'])->name('medicines.index');
    Route::get('/medicines/{medicine}', [MedicineController::class, 'show'])->name('medicines.show');

    // Pharmacist consultation
    Route::get('/consultation', [ConsultationController::class, 'create'])->name('consultation.create');
    Route::post('/consultation', [ConsultationController::class, 'store'])->name('consultation.store');

    // Health articles
    Route::get('/articles', [PublicArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/{article}', [PublicArticleController::class, 'show'])->name('articles.show');

    // Join community
    Route::post('/join', [MemberController::class, 'store'])->name('members.store');

    // Shopping cart (guest, session based)
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add/{medicine}', [CartController::class, 'add'])->name('add');
        Route::post('/update/{medicine}', [CartController::class, 'update'])->name('update');
        Route::post('/remove/{medicine}', [CartController::class, 'remove'])->name('remove');
        Route::post('/clear', [CartController::class, 'clear'])->name('clear');
        Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');
        Route::post('/checkout', [CartController::class, 'store'])->name('store');
        Route::get('/success/{order_number}', [CartController::class, 'success'])->name('success');
    });
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
