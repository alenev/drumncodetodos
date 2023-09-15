<?php

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

Route::get('todos/statuses/get', '\App\Http\Controllers\Api\ToDosStatusesController@getToDosStatuses');
Route::post('todos/create', '\App\Http\Controllers\Api\ToDosController@createToDo');
Route::get('todos/get', '\App\Http\Controllers\Api\ToDosController@getToDos');
Route::post('todos/update', '\App\Http\Controllers\Api\ToDosController@updateToDo');