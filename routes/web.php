<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\studentController;

Route::get('students', [studentController::class, 'index'])->name('students.index');
Route::get('students/create', [studentController::class, 'create'])->name('students.create');
Route::post('students', [studentController::class, 'store'])->name('students.store');
Route::get('students/{student}/edit', [studentController::class, 'edit'])->name('students.edit');
Route::put('students/{student}', [studentController::class, 'update'])->name('students.update');
Route::delete('students/{student}', [studentController::class, 'destroy'])->name('students.destroy');

Route::post('students/insert', [studentController::class, 'insert'])->name('students.insert');

