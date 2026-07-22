<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () { return view('welcome'); });
Route::get('/users', [UserController::class, 'index'])->name('users.index')->middleware('auth');
Route::post('/users', [UserController::class, 'store']);
