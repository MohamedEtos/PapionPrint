<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PrintersController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::namespace('App\Http\Controllers')->middleware('auth')->group(function () {

    Route::get('AddPrintOrders', [PrintersController::class, 'index'])->name('AddPrintOrders');

});
