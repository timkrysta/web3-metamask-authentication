<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Http\Request;
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

Route::get('/',     [HomeController::class, 'login'])->name('login');
Route::get('/home', [HomeController::class, 'home'])->name('home');

Route::post('/authenticate', [AuthController::class, 'authenticate'])->name('authenticate');
Route::get('/logout',        [AuthController::class, 'logout'])->name('logout');
