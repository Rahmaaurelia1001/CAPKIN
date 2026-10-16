<?php

use Illuminate\Support\Facades\Route;

// Redirect root ke /app portal
Route::get('/', function () {
    return redirect('/app');
});

// Redirect URL lama /admin ke portal /app agar tidak 404
Route::any('/admin/{any?}', function () {
    return redirect('/app');
})->where('any', '.*');
