<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PrintersController;



Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::namespace('App\Http\Controllers')->middleware('auth')->group(function () {

    Route::get('AddPrintOrders', [PrintersController::class, 'index'])->name('AddPrintOrders');
    Route::post('printers/upload-image', [PrintersController::class, 'uploadImage'])->name('printers.upload.image');
    Route::post('printers/store', [PrintersController::class, 'store'])->name('printers.store');

});
