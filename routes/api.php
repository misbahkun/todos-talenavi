<?php

use App\Http\Controllers\TodoController;
use App\Http\Controllers\ChartController;
use Illuminate\Support\Facades\Route;

Route::post('/todos', [TodoController::class, 'store']);      
Route::get('/todos/export', [TodoController::class, 'exportAndSave']);
Route::get('/chart', [ChartController::class, 'index']);