<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\EvacueeController;
use App\Http\Controllers\API\RescuerAuthController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/evacuees', [EvacueeController::class, 'store']);

Route::prefix('rescuer')->group(function () {
    Route::post('/register', [RescuerAuthController::class, 'register']);
    Route::post('/login', [RescuerAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->post('/logout', [RescuerAuthController::class, 'logout']);
    Route::middleware('auth:sanctum')->get('/assigned-rescues', [RescuerAuthController::class, 'assignedRescues']);
    Route::middleware('auth:sanctum')->get('/completed-rescues', [RescuerAuthController::class, 'completedRescues']);
    Route::middleware('auth:sanctum')->get('/cancelled-rescues', [RescuerAuthController::class, 'cancelledRescues']);
    Route::middleware('auth:sanctum')->get('/available-rescues', [RescuerAuthController::class, 'availableRescues']);
    Route::middleware('auth:sanctum')->post('/assign/{id}', [RescuerAuthController::class, 'assignRescue']);
    Route::middleware('auth:sanctum')->post('/assign/{id}', [RescuerAuthController::class, 'assignRescue']);
    Route::middleware('auth:sanctum')->post('/cancel/{id}', [RescuerAuthController::class, 'cancelRescue']);
    Route::middleware('auth:sanctum')->post('/complete/{id}', [RescuerAuthController::class, 'completeRescue']);
    Route::middleware('auth:sanctum')->get('/all-evacuees', [RescuerAuthController::class, 'allEvacuees']);

});