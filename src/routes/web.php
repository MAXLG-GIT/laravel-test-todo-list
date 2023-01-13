<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});

Auth::routes();

Route::get('/', [App\Http\Controllers\TaskController::class, 'listPage'])->middleware('auth');

Route::get('/edit/{id?}', [App\Http\Controllers\TaskController::class, 'editPage'])->where('id', '[0-9]+')->middleware('auth');
Route::post('/edit', [App\Http\Controllers\TaskController::class, 'saveTask'])->middleware('auth');
Route::post('/edit/upload-image', [App\Http\Controllers\TaskController::class, 'uploadImage'])->middleware('auth');
Route::post('/edit/delete-image', [App\Http\Controllers\TaskController::class, 'deleteImage'])->middleware('auth');
Route::post('/delete-task', [App\Http\Controllers\TaskController::class, 'deleteTask'])->middleware('auth');
Route::post('/tags-filter', [App\Http\Controllers\TaskController::class, 'tagsFilter'])->middleware('auth');
Route::post('/search', [App\Http\Controllers\TaskController::class, 'search'])->middleware('auth');

