<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PrintersController;
use App\Http\Controllers\PrinterlogsController;



Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::namespace('App\Http\Controllers')->middleware('auth')->group(function () {

    Route::prefix('charts')->group(function () {
        Route::get('/meters', [App\Http\Controllers\ChartController::class, 'getMeterData'])->name('charts.meters');
        Route::get('/orders', [App\Http\Controllers\ChartController::class, 'getOrdersData'])->name('charts.orders');
        Route::get('/customers', [App\Http\Controllers\ChartController::class, 'getCustomersData'])->name('charts.customers');
        Route::get('/client-retention', [App\Http\Controllers\ChartController::class, 'getClientRetentionData'])->name('charts.client_retention');
    });

    Route::prefix('Rollpress')->group(function () {
        Route::get('/addpressorder', [App\Http\Controllers\RollpressController::class, 'index'])->name('AddRollpress');
        Route::get('/presslist', [App\Http\Controllers\RollpressController::class, 'presslist'])->name('presslist');
        Route::post('/store', [App\Http\Controllers\RollpressController::class, 'store'])->name('rollpress.store');
        Route::get('/archive', [App\Http\Controllers\RollpressController::class, 'archive'])->name('rollpress.archive');
        Route::post('/bulk-delete', [App\Http\Controllers\RollpressController::class, 'bulkDelete'])->name('rollpress.bulk_delete');
        Route::put('/update/{id}', [App\Http\Controllers\RollpressController::class, 'update'])->name('rollpress.update');
        Route::get('/trash', [App\Http\Controllers\RollpressController::class, 'trash'])->name('rollpress.trash');
        Route::post('/restore/{id}', [App\Http\Controllers\RollpressController::class, 'restore'])->name('rollpress.restore');
        Route::delete('/force-delete/{id}', [App\Http\Controllers\RollpressController::class, 'forceDelete'])->name('rollpress.force_delete');
    });

    Route::get('AddPrintOrders', [PrintersController::class, 'index'])->name('AddPrintOrders');
    Route::post('printers/upload-image', [PrintersController::class, 'uploadImage'])->name('printers.upload.image');
    Route::post('printers/store', [PrintersController::class, 'store'])->name('printers.store');
    Route::post('printers/delete/{id}', [PrintersController::class, 'destroy'])->name('printers.delete');
    Route::post('printers/update-status/{id}', [PrintersController::class, 'updateStatus'])->name('printers.update.status');
    Route::post('printers/update-price/{id}', [PrintersController::class, 'updatePrice'])->name('printers.update.price');
    Route::post('printers/bulk-delete', [PrintersController::class, 'bulkDelete'])->name('printers.bulk_delete');
    Route::get('printers/{id}', [PrintersController::class, 'show'])->name('printers.show');
    Route::put('printers/{id}', [PrintersController::class, 'update'])->name('printers.update');



    Route::get('print-log', [PrinterlogsController::class, 'printLog'])->name('print_log');
    Route::post('printers/duplicate/{id}', [PrinterlogsController::class, 'duplicate'])->name('printers.duplicate');





    // Trash Routes
    Route::get('trash/printers', [PrintersController::class, 'trash'])->name('printers.trash');
    Route::post('printers/restore/{id}', [PrintersController::class, 'restore'])->name('printers.restore');
    Route::delete('printers/force-delete/{id}', [PrintersController::class, 'forceDelete'])->name('printers.force_delete');


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

    // Accounts
    Route::get('/accounts', [App\Http\Controllers\AccountsController::class, 'index'])->name('accounts.index');
    Route::post('/accounts/update-price', [App\Http\Controllers\AccountsController::class, 'updatePrice'])->name('accounts.update_price');

    // Settings Routes
    Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');
});
