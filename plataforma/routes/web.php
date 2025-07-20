<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Rutas SPA para Vue
Route::get('/app', function () {
    return view('app');
})->name('app');

// Capturar todas las rutas del SPA para Vue Router
Route::get('/profesor/{path?}', function () {
    return view('app');
})->where('path', '.*')->name('profesor');

Route::get('/estudiante/{path?}', function () {
    return view('app');
})->where('path', '.*')->name('estudiante');

Route::get('/login', function () {
    return view('app');
})->name('login');

Route::get('/dashboard', function () {
    return view('app');
})->name('dashboard');
