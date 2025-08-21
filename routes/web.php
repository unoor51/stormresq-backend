<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RescuerAuthController;
use App\Http\Controllers\API\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/rescuer/verify/{token}', [RescuerAuthController::class, 'verifyEmail']);
Route::get('/user/verify/{token}', [AuthController::class, 'verifyEmail']);