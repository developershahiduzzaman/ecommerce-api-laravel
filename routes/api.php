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
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\OrderController;



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

