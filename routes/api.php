<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\StyleController;
use App\Http\Controllers\Api\SubcategoryController;
use Illuminate\Support\Facades\Route;

// Auth (Sanctum SPA cookie-session)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);
});

// Services — read endpoints are public (the public site + admin panel
// both list every service, active or coming-soon); writes are admin-only.
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{service}', [ServiceController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/services', [ServiceController::class, 'store']);
    Route::put('/services/{service}', [ServiceController::class, 'update']);
    Route::patch('/services/{service}', [ServiceController::class, 'update']);
    Route::delete('/services/{service}', [ServiceController::class, 'destroy']);
});

// Product taxonomy: Service -> Category (niche) -> Subcategory (micro-niche).
// Read endpoints are public; writes are admin-only — same split as Services.
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);
Route::get('/subcategories', [SubcategoryController::class, 'index']);
Route::get('/subcategories/{subcategory}', [SubcategoryController::class, 'show']);
Route::get('/styles', [StyleController::class, 'index']);
Route::get('/styles/{style}', [StyleController::class, 'show']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::patch('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

    Route::post('/subcategories', [SubcategoryController::class, 'store']);
    Route::put('/subcategories/{subcategory}', [SubcategoryController::class, 'update']);
    Route::patch('/subcategories/{subcategory}', [SubcategoryController::class, 'update']);
    Route::delete('/subcategories/{subcategory}', [SubcategoryController::class, 'destroy']);

    Route::post('/styles', [StyleController::class, 'store']);
    Route::put('/styles/{style}', [StyleController::class, 'update']);
    Route::patch('/styles/{style}', [StyleController::class, 'update']);
    Route::delete('/styles/{style}', [StyleController::class, 'destroy']);

    // POST doubles as the update route (in addition to PUT/PATCH) so a
    // multipart image replacement can use Laravel's standard method-spoofing
    // (`_method=PUT` in the form body) — browsers/PHP can't parse a
    // multipart body on a real PUT/PATCH request.
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::patch('/products/{product}', [ProductController::class, 'update']);
    Route::post('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);
});
