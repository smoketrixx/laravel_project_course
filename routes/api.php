<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


#user routes realated
Route::post('/login', [UserController::class, 'loginApi']);

#post routes related
Route::post('/create-post', [PostController::class, 'createPostApi'])->middleware('auth:sanctum');
Route::delete('/post/{post}', [PostController::class, 'deleteApi'])->middleware('auth:sanctum', 'can:delete,post');
Route::put('/post/{post}', [PostController::class, 'updateApi'])->middleware('auth:sanctum', 'can:update,post');