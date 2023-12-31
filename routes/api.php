<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\EmailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::prefix('v1')->group(function () {
    Route::post('/register', [RegisterController::class, 'UserRegister']);
    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/revoke', [LoginController::class, 'revoke'])->name('revoke');
    Route::post('/{user_name}/send', [EmailController::class, 'send'])->middleware('auth:sanctum');
});
