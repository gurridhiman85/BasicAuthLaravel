<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/sendinvitation', [App\Http\Controllers\UserController::class, 'index'])->name('sendinvitation');
    Route::post('/sendinvitation', [App\Http\Controllers\UserController::class, 'sendInvitation']);
    Route::get('/invitation/{email}', [App\Http\Controllers\UserController::class, 'invitation']);
    Route::post('/signup', [App\Http\Controllers\UserController::class, 'signup'])->name('signup');
    Route::get('/verification/{email}', [App\Http\Controllers\UserController::class, 'verificationView']);
    Route::post('/verification', [App\Http\Controllers\UserController::class, 'verification'])->name('verification');
    Route::get('/profile', [App\Http\Controllers\UserController::class, 'profileView'])->name('profile');
    Route::post('/profile', [App\Http\Controllers\UserController::class, 'profileUpdate'])->name('profileupdate');
});
