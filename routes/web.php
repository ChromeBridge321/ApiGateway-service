<?php

use Illuminate\Support\Facades\Auth;
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


Route::get('/', function () {
    $error = 0;
    return view('auth.login')->with('error', $error);
});

Route::get('/index', function () {

    return view('index');
})->middleware(['jwt.verify']);

Route::get('/register', function () {
    $error = 0;
    return view('auth.register')->with('error', $error);
});

Route::get('/perfil', function () {
    $error = 0;
    return view('perfil')->with('error', $error);
});