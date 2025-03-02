<?php

use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\API\BookSearchController;

Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);

// Add these new routes for books
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/books', [BookController::class, 'index']);
    Route::post('/books', [BookController::class, 'store']);
    Route::put('/books/{book}', [BookController::class, 'update']);
    Route::delete('/books/{book}', [BookController::class, 'destroy']);
    Route::get('/books/search', [BookSearchController::class, 'search']);
    Route::post('/books/add-to-library', [BookSearchController::class, 'addToLibrary']);
    Route::patch('/books/{id}/progress', [BookController::class, 'updateProgress']);
    Route::patch('/books/{book}/progress', [BookController::class, 'updateProgress']);
    Route::patch('/books/{book}/status', [BookController::class, 'updateStatus']);
    Route::patch('/books/{book}/time', [BookController::class, 'updateReadingTime']);
    Route::get('/books', [BookController::class, 'index']);
    Route::post('/logout', [LoginController::class, 'logout']);
});