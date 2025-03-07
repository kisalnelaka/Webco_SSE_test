<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/products', 'App\Http\Controllers\Admin\ProductController@index');
    Route::post('/products', 'App\Http\Controllers\Admin\ProductController@store');
    Route::get('/products/{product}', 'App\Http\Controllers\Admin\ProductController@show');
    Route::put('/products/{product}', 'App\Http\Controllers\Admin\ProductController@update');
    Route::delete('/products/{product}', 'App\Http\Controllers\Admin\ProductController@destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
