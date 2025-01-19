<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\studentController;
use App\Http\Controllers\DBViewerController;

Route::get('dbviewer', [DBViewerController::class, 'ListTables'])->name('dbviewer.ListTables');

Route::get('students', [studentController::class, 'index'])->name('students.index');
Route::get('students/create', [studentController::class, 'create'])->name('students.create');
Route::post('students', [studentController::class, 'store'])->name('students.store');
Route::get('students/{student}/edit', [studentController::class, 'edit'])->name('students.edit');
Route::put('students/{student}', [studentController::class, 'update'])->name('students.update');
Route::delete('students/{student}', [studentController::class, 'destroy'])->name('students.destroy');

Route::post('students/insert', [studentController::class, 'insert'])->name('students.insert');
Route::get('students/selectwhere', [studentController::class, 'selectwhere'])->name('students.selectwhere');
Route::get('students/showAggregates', [studentController::class, 'showAggregates'])->name('students.showAggregates');
Route::get('students/showAggregates2', [studentController::class, 'showAggregates2'])->name('students.showAggregates2');
Route::get('students/groupby', [studentController::class, 'groupby'])->name('students.groupby');
Route::get('students/relationships', [studentController::class, 'relationships'])->name('students.relationships');
Route::get('students/relationships/1to1', [studentController::class, 'relationships1to1'])->name('students.relationships1to1');
Route::get('students/relationships/1tomany', [studentController::class, 'relationships1tomany'])->name('students.relationships1tomany');
Route::get('students/relationships/manytomany', [studentController::class, 'relationshipsmanytomany'])->name('students.relationshipsmanytomany');