<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PrintersController;



Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::namespace('App\Http\Controllers')->middleware('auth')->group(function () {

    Route::get('AddPrintOrders', [PrintersController::class, 'index'])->name('AddPrintOrders');

});
