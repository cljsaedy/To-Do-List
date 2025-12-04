<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::get('/', function () {
    return redirect()->route('tasks.filter', ['filter' => 'todo']);
});

Route::get('tasks/{filter?}/{taskToEditId?}', [TaskController::class, 'index'])->name('tasks.filter');

Route::resource('tasks', TaskController::class)->except(['index', 'edit']);

Route::patch('tasks/{task}/restore', [TaskController::class, 'restore'])->name('tasks.restore');
Route::delete('tasks/{task}/forceDelete', [TaskController::class, 'forceDelete'])->name('tasks.forceDelete');
Route::patch('tasks/{task}/toggle', [TaskController::class, 'toggleComplete'])->name('tasks.toggle');