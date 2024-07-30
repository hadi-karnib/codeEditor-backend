<?php

use App\Http\Controllers\auth\AuthUserController;
use App\Http\Controllers\code\CodeFilesController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\user\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login', [AuthUserController::class, 'login'])->name('login');
Route::post('create', [AuthUserController::class, 'register']);
Route::middleware('auth:api')->group(function () {
    Route::middleware('admin')->group(function () {
        Route::delete('user/delete', [UserController::class, 'deleteUser']);
        Route::post('/upload', [UserController::class, 'uploadUsersCsv']);
    });
    Route::middleware('user')->group(function () {
        Route::get('users', [UserController::class, 'getAllUsers']);
        Route::get('userCodes', [CodeFilesController::class, 'getUserCodes']);
        Route::post('getCode', [CodeFilesController::class, 'getCodeFileById']);
        Route::post('createCode', [CodeFilesController::class, 'createCodeFile']);
        Route::patch('updateCode', [CodeFilesController::class, 'updateCode']);
        Route::delete('deleteCode', [CodeFilesController::class, 'deleteCodeFile']);
    });
});
