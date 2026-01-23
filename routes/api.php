<?php
// Auth
// POST /register
// POST /login

// Category
// GET /categories

// Product
// GET /products
// GET /products/{id}

// Order
// POST /order
// GET /orders

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\OrderController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Verified;



Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function(){
    Route::post('/logout', [AuthController::class,'logout']);


});

Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/cart/add', [CartController::class, 'add']);
    Route::get('/cart', [CartController::class, 'index']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);
});
Route::middleware('auth:sanctum')->post('/order/place', [OrderController::class, 'placeOrder']);
Route::middleware('auth:sanctum')->get('/orders/history', [OrderController::class, 'history']);

//payment route
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/payment/init', [App\Http\Controllers\PaymentController::class, 'initPayment']);

});

Route::post('/payment/success', [App\Http\Controllers\PaymentController::class, 'success']);
Route::post('/payment/fail', [App\Http\Controllers\PaymentController::class, 'fail']);
Route::post('/payment/cancel', [App\Http\Controllers\PaymentController::class, 'cancel']);

// Email Verification Route
Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    // Find the user by ID
    $user = User::findOrFail($id);

    // Check if the hash matches the user's email
    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        return response()->json(['message' => 'Invalid verification link.'], 403);
    }

    // Check if already verified
    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email already verified.']);
    }

    // Mark as verified
    if ($user->markEmailAsVerified()) {
        event(new Verified($user));
    }

    return view('verified');
})->middleware(['signed'])->name('verification.verify');

// Resend Verification Email
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return response()->json(['message' => 'Verification link sent!']);
})->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');
