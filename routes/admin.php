<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ConsultationController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PharmacyController;
use App\Http\Controllers\Admin\ProductStockController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
// use App\Http\Controllers\Admin\CategoryController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

// Route::group([
//     'prefix' => LaravelLocalization::setLocale(),
//     'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
// ], function () {

//     Route::prefix('admin')->name('admin.')->middleware(['auth','is_admin'])->group(function () {

//                 Route::get('loginType', [AdminController::class, 'loginType'])->name('login.type');

//                 Route::get('/', [AdminController::class, 'index'])->name('index');

//                 Route::resource('categories',CategoryController::class);
//                 Route::resource('products', ProductController::class);
//                 Route::get('/delete-image/{id?}', [ProductController::class, 'delete_img'])->name('delete_img');


//         });

//     });

    Route::prefix(LaravelLocalization::setLocale())->middleware(['auth', 'is_admin'])->group(function() {
    Route::prefix('admin')->name('admin.')->group(function() {
        // Every route below is gated by the permission code its feature owns;
        // the owner passes them all (see AppServiceProvider's Gate::before).
        Route::get('/', [AdminController::class, 'index'])->middleware('can:dashboard.view')->name('index');

        // The signed-in manager's own account — no permission needed for this
        Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
        Route::put('/profile', [AdminController::class, 'profile_data'])->name('profile_data');
        Route::post('/check-password', [AdminController::class, 'check_password'])->name('check_password');

        Route::resource('categories', CategoryController::class)
            ->middlewareFor(['index', 'show'], 'can:categories.view')
            ->middlewareFor(['create', 'store'], 'can:categories.create')
            ->middlewareFor(['edit', 'update'], 'can:categories.edit')
            ->middlewareFor('destroy', 'can:categories.delete');

        // Where a medicine is held: branch-by-branch stock (manager only)
        Route::get('products/{product}/stock', [ProductStockController::class, 'edit'])
            ->middleware('can:products.stock')->name('products.stock.edit');
        Route::put('products/{product}/stock', [ProductStockController::class, 'update'])
            ->middleware('can:products.stock')->name('products.stock.update');

        Route::resource('products', ProductController::class)
            ->middlewareFor(['index', 'show'], 'can:products.view')
            ->middlewareFor(['create', 'store'], 'can:products.create')
            ->middlewareFor(['edit', 'update'], 'can:products.edit')
            ->middlewareFor('destroy', 'can:products.delete');

        Route::resource('pharmacies', PharmacyController::class)
            ->middlewareFor(['index', 'show'], 'can:pharmacies.view')
            ->middlewareFor(['create', 'store'], 'can:pharmacies.create')
            ->middlewareFor(['edit', 'update'], 'can:pharmacies.edit')
            ->middlewareFor('destroy', 'can:pharmacies.delete');

        Route::resource('articles', ArticleController::class)->except('show')
            ->middlewareFor('index', 'can:articles.view')
            ->middlewareFor(['create', 'store'], 'can:articles.create')
            ->middlewareFor(['edit', 'update'], 'can:articles.edit')
            ->middlewareFor('destroy', 'can:articles.delete');

        Route::get('/delete-image/{id?}', [ProductController::class, 'delete_img'])
            ->middleware('can:products.edit')->name('delete_img');

        // Pharmacist consultations inbox
        Route::get('/consultations', [ConsultationController::class, 'index'])
            ->middleware('can:consultations.view')->name('consultations.index');
        Route::get('/consultations/{consultation}', [ConsultationController::class, 'show'])
            ->middleware('can:consultations.view')->name('consultations.show');
        Route::put('/consultations/{consultation}', [ConsultationController::class, 'update'])
            ->middleware('can:consultations.reply')->name('consultations.update');
        Route::delete('/consultations/{consultation}', [ConsultationController::class, 'destroy'])
            ->middleware('can:consultations.delete')->name('consultations.destroy');

        // Community members (customers)
        Route::get('/customers', [MemberController::class, 'index'])
            ->middleware('can:customers.view')->name('customers');
        Route::delete('/customers/{member}', [MemberController::class, 'destroy'])
            ->middleware('can:customers.delete')->name('customers.destroy');

        // Dynamic site settings
        Route::get('/settings', [SettingController::class, 'edit'])
            ->middleware('can:settings.manage')->name('settings.edit');
        Route::put('/settings', [SettingController::class, 'update'])
            ->middleware('can:settings.manage')->name('settings.update');

        // Customer orders coming from the site cart
        Route::get('/orders', [OrderController::class, 'index'])
            ->middleware('can:orders.view')->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])
            ->middleware('can:orders.view')->name('orders.show');
        Route::put('/orders/{order}', [OrderController::class, 'update'])
            ->middleware('can:orders.update')->name('orders.update');
        Route::delete('/orders/{order}', [OrderController::class, 'destroy'])
            ->middleware('can:orders.delete')->name('orders.destroy');

        // Staff accounts and what each role may do
        Route::resource('users', UserController::class)->except('show')
            ->middlewareFor('index', 'can:users.view')
            ->middlewareFor(['create', 'store'], 'can:users.create')
            ->middlewareFor(['edit', 'update'], 'can:users.edit')
            ->middlewareFor('destroy', 'can:users.delete');
        Route::get('/roles', [RoleController::class, 'index'])
            ->middleware('can:roles.view')->name('roles.index');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
            ->middleware('can:roles.edit')->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])
            ->middleware('can:roles.edit')->name('roles.update');

        Route::get('/notifications', [AdminController::class, 'notifications'])->name('notifications');
    });
});
