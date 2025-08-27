<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\EvacueeController;
use App\Http\Controllers\API\RescuerAuthController;
use App\Http\Controllers\API\AdminAuthController;
use App\Http\Controllers\API\AuthController;

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
// User Auth Routes
Route::prefix('user')->group(function () { 
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
    
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::get('/my-requests', [EvacueeController::class, 'myRequests']);

    });
});



Route::middleware('auth:sanctum')->get('/user/profile', [AuthController::class, 'profile']);

// Protected route
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/evacuees', [EvacueeController::class, 'store']);
// Rescuer Routes
Route::prefix('rescuer')->group(function () {
    Route::post('/register', [RescuerAuthController::class, 'register']);
    Route::post('/login', [RescuerAuthController::class, 'login']);

    Route::post('/forgot-password', [RescuerAuthController::class, 'sendResetLinkEmail']);
    Route::post('/reset-password', [RescuerAuthController::class, 'resetPassword']);
    
    Route::middleware('auth:sanctum')->post('/logout', [RescuerAuthController::class, 'logout']);
    Route::middleware('auth:sanctum')->get('/assigned-rescues', [RescuerAuthController::class, 'assignedRescues']);
    Route::middleware('auth:sanctum')->get('/completed-rescues', [RescuerAuthController::class, 'completedRescues']);
    Route::middleware('auth:sanctum')->get('/cancelled-rescues', [RescuerAuthController::class, 'cancelledRescues']);
    Route::middleware('auth:sanctum')->get('/available-rescues', [RescuerAuthController::class, 'availableRescues']);
    Route::middleware('auth:sanctum')->post('/assign/{id}', [RescuerAuthController::class, 'assignRescue']);
    Route::middleware('auth:sanctum')->post('/cancel/{id}', [RescuerAuthController::class, 'cancelRescue']);
    Route::middleware('auth:sanctum')->post('/complete/{id}', [RescuerAuthController::class, 'completeRescue']);
    Route::middleware('auth:sanctum')->post('/update-password', [RescuerAuthController::class, 'updatePassword']);
    Route::middleware('auth:sanctum')->get('/all-evacuees', [RescuerAuthController::class, 'allEvacuees']);
    Route::middleware('auth:sanctum')->get('/profile', [RescuerAuthController::class, 'profile']);
    Route::middleware('auth:sanctum')->put('/profile', [RescuerAuthController::class, 'updateProfile']);
    Route::middleware('auth:sanctum')->get('/dashboard-stats', [RescuerAuthController::class, 'dashboardStats']);
    Route::middleware('auth:sanctum')->get('/nearby-rescuees', [RescuerAuthController::class, 'nearbyRescuees']);
});
// Admin Routes
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/register', [AdminAuthController::class, 'register']);

    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout']);
        Route::get('/dashboard-stats', [AdminAuthController::class, 'dashboardStats']);
        Route::get('/rescuers', [AdminAuthController::class, 'allRescuers']);
        Route::put('/rescuers/{id}/approve', [AdminAuthController::class, 'approveRescuer']);
        Route::put('/rescuers/{id}/reject', [AdminAuthController::class, 'rejectRescuer']);
        Route::put('/rescuer/{id}/deactivate', [AdminAuthController::class, 'deactivateRescuer']);
        Route::delete('/rescuer/{id}', [AdminAuthController::class, 'deleteRescuer']);
        Route::get('/rescues', [AdminAuthController::class, 'rescueRequests']);
        Route::get('/settings', [AdminAuthController::class, 'settings']);
        Route::post('/settings', [AdminAuthController::class, 'update_settings']);
    });
});