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


Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function(){
    Route::post('/logout', [AuthController::class,'logout']);


});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('products', ProductController::class);
});

