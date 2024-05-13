<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

//Home
Route::get('/', [ContactController::class, 'index']);

//Contacts
Route::group(['prefix' => '/contacts'], function () {
    Route::get('/', [ContactController::class, 'index']);
    Route::get('/create', [ContactController::class, 'create']);
    Route::get('/edit/{id}', [ContactController::class, 'edit']);
    Route::get('/update', [ContactController::class, 'update']);
    Route::get('/delete/{id}', [ContactController::class, 'delete']);
});
