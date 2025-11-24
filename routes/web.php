<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function (){
    return view('layout');
});

Route::get('/about', function(){
    return view('main.about');
});

Route::get('/contact', function(){
    $array = [
        'name' => 'Moscow Polytech',
        'address' => 'B. Semenovskaya h.38',
        'email' => '..@mospolytech.ru',
        'phone' => '8(499)784-9396'
    ];
    return view('main.contact', ['contact' => $array]); 
});