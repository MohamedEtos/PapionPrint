<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PrintersController;



Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::namespace('App\Http\Controllers')->middleware('auth')->group(function () {

    Route::get('AddPrintOrders', [PrintersController::class, 'index'])->name('AddPrintOrders');
    Route::post('printers/upload-image', [PrintersController::class, 'uploadImage'])->name('printers.upload.image');
    Route::post('printers/store', [PrintersController::class, 'store'])->name('printers.store');
    Route::post('printers/delete/{id}', [PrintersController::class, 'destroy'])->name('printers.delete');
    Route::post('printers/update-status/{id}', [PrintersController::class, 'updateStatus'])->name('printers.update.status');
    Route::post('printers/bulk-delete', [PrintersController::class, 'bulkDelete'])->name('printers.bulk_delete');
    Route::get('printers/{id}', [PrintersController::class, 'show'])->name('printers.show');
    Route::put('printers/{id}', [PrintersController::class, 'update'])->name('printers.update');


    // Roles & Permissions
    Route::get('/roles', [App\Http\Controllers\RolesController::class, 'index'])->name('roles.index');
    Route::post('/roles/store', [App\Http\Controllers\RolesController::class, 'store'])->name('roles.store');
    Route::post('/roles/update/{id}', [App\Http\Controllers\RolesController::class, 'update'])->name('roles.update');
    Route::post('/roles/delete/{id}', [App\Http\Controllers\RolesController::class, 'destroy'])->name('roles.delete');

    // Users Management
    Route::get('/users', [App\Http\Controllers\UsersController::class, 'index'])->name('users.index');
    Route::post('/users/store', [App\Http\Controllers\UsersController::class, 'store'])->name('users.store');
    Route::post('/users/update/{id}', [App\Http\Controllers\UsersController::class, 'update'])->name('users.update');
    Route::post('/users/delete/{id}', [App\Http\Controllers\UsersController::class, 'destroy'])->name('users.delete');

});
