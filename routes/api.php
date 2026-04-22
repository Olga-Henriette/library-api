<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\BorrowController;


Route::prefix('v1')->group(function () {
    
    // Auth publique
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // --- ROUTES AUTEURS ---
    // Lecture (Public)
    Route::get('/authors', [AuthorController::class, 'index']);
    Route::get('/authors/{id}', [AuthorController::class, 'show']);
    Route::get('/books', [BookController::class, 'index']);
    Route::get('/books/{id}', [BookController::class, 'show']);

    // Écriture (Protégé par Token)
    Route::middleware('auth:sanctum')->group(function () {
        // Auteurs
        Route::post('/authors', [AuthorController::class, 'store']);
        Route::put('/authors/{id}', [AuthorController::class, 'update']);
        Route::delete('/authors/{id}', [AuthorController::class, 'destroy']);
        
        // Livres
        Route::post('/books', [BookController::class, 'store']);
        Route::put('/books/{id}', [BookController::class, 'update']);
        Route::delete('/books/{id}', [BookController::class, 'destroy']);

        // Emprunts
        Route::get('/borrows', [BorrowController::class, 'index']);
        Route::post('/borrows', [BorrowController::class, 'store']);
        Route::patch('/borrows/{id}/return', [BorrowController::class, 'returnBook']);

        // Route de test user
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
    });
});