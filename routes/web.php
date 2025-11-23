<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('app');
});

// Catch-all route for React Router (SPA)
Route::get('/{path?}', function () {
    return view('app');
})->where('path', '.*');
