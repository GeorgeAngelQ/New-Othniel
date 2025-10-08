<?php

use Illuminate\Support\Facades\Route;

Route::view('/login', 'auth.login')->name('login.form');
Route::view('/register', 'auth.register')->name('register.form');
