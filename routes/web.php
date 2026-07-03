<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/password-reset/{token}', function (string $token) {
    $frontendUrl = config('app.frontend_url');

    if (!$frontendUrl || !str_starts_with($frontendUrl, 'https://')) {
        abort(400, 'Invalid frontend URL configuration.');
    }

    return redirect()->to($frontendUrl . '/password-reset/' . $token);
})->name('password.reset');
