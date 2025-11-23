<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Catch-all route for React Router (SPA) - exclude API routes
Route::get('/{path?}', function () {
    return view('welcome');
})->where('path', '^(?!api).*$');
