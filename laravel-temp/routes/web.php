<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $screen = request()->query('screen', 'home');

    return view('foodie', [
        'screen' => in_array($screen, ['home', 'cart', 'buyer', 'payment', 'confirmation', 'status'], true)
            ? $screen
            : 'home',
    ]);
});
