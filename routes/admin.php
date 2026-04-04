<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
// use App\Http\Controllers\Admin\CategoryController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
], function () {

    Route::prefix('admin')->name('admin.')->group(function () {


                Route::get('/', [AdminController::class, 'index'])->name('index');

                Route::resource('categories',CategoryController::class);
                Route::resource('products', ProductController::class);
                Route::get('/delete-image/{id?}', [ProductController::class, 'delete_img'])->name('delete_img');


        });

    });
