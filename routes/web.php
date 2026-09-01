<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::get('/login', function () {
    return view('auth.login');
})->name('auth.login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
