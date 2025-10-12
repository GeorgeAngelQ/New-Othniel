<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;

Route::view('/login', 'auth.login')->name('login.form');
Route::view('/register', 'auth.register')->name('register.form');
Route::get('/', [HomeController::class, 'index'])->name('home');
