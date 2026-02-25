<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PrintersController;
use App\Http\Controllers\PrinterlogsController;
use Illuminate\Support\Facades\Mail;



Auth::routes();



Route::namespace('App\Http\Controllers')->middleware('auth')->group(function () {

    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->middleware(['permission:الاحصائيات'])->name('home');
    
    Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'dashboard'])->name('dashboard');

    Route::prefix('charts')->middleware(['permission:الاحصائيات'])->group(function () {
        Route::get('/meters', [App\Http\Controllers\ChartController::class, 'getMeterData'])->name('charts.meters');
        Route::get('/orders', [App\Http\Controllers\ChartController::class, 'getOrdersData'])->name('charts.orders');
        Route::get('/customers', [App\Http\Controllers\ChartController::class, 'getCustomersData'])->name('charts.customers');
        Route::get('/client-retention', [App\Http\Controllers\ChartController::class, 'getClientRetentionData'])->name('charts.client_retention');
        Route::get('/inventory', [App\Http\Controllers\ChartController::class, 'getInventoryData'])->name('charts.inventory');
        Route::get('/inventory-stock', [App\Http\Controllers\ChartController::class, 'getInventoryStockData'])->name('charts.inventory_stock');
        Route::get('/ink-consumption', [App\Http\Controllers\ChartController::class, 'getInkConsumptionData'])->name('charts.ink_consumption');
        Route::get('/stras-tarter-consumption', [App\Http\Controllers\ChartController::class, 'getStrasTarterConsumptionData'])->name('charts.stras_tarter_consumption');
        Route::get('/stras-orders', [App\Http\Controllers\ChartController::class, 'getStrasOrdersData'])->name('charts.stras_orders');
        Route::get('/tarter-orders', [App\Http\Controllers\ChartController::class, 'getTarterOrdersData'])->name('charts.tarter_orders');
    });

    Route::prefix('Rollpress')->middleware(['permission:المكبس'])->group(function () {
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

    Route::middleware(['permission:المكبس'])->group(function () {
        Route::post('/attendance/check-in', [App\Http\Controllers\AttendanceController::class, 'checkIn'])->name('attendance.checkIn');
        Route::post('/attendance/check-out', [App\Http\Controllers\AttendanceController::class, 'checkOut'])->name('attendance.checkOut');

    });
    
    Route::middleware(['permission:الطباعه'])->group(function () {
        Route::get('AddPrintOrders', [PrintersController::class, 'index'])->name('AddPrintOrders');
        Route::post('printers/upload-image', [PrintersController::class, 'uploadImage'])->name('printers.upload.image');
        Route::post('printers/store', [PrintersController::class, 'store'])->name('printers.store');
        Route::post('printers/delete/{id}', [PrintersController::class, 'destroy'])->name('printers.delete');
        Route::post('printers/update-price/{id}', [PrintersController::class, 'updatePrice'])->name('printers.update.price');
        Route::post('printers/bulk-delete', [PrintersController::class, 'bulkDelete'])->name('printers.bulk_delete');
        Route::get('print-log', [PrinterlogsController::class, 'printLog'])->name('print_log');
        Route::post('printers/duplicate/{id}', [PrinterlogsController::class, 'duplicate'])->name('printers.duplicate');
        // Trash Routes
        Route::get('trash/printers', [PrintersController::class, 'trash'])->name('printers.trash');
        Route::post('printers/restore/{id}', [PrintersController::class, 'restore'])->name('printers.restore');
        Route::delete('printers/force-delete/{id}', [PrintersController::class, 'forceDelete'])->name('printers.force_delete');
    });

    // Routes accessible by EITHER الطباعه OR المكبس
    Route::middleware(['permission:الطباعه|المكبس'])->group(function () {
        Route::get('printers/{id}', [PrintersController::class, 'show'])->name('printers.show');
        Route::put('printers/{id}', [PrintersController::class, 'update'])->name('printers.update');
        Route::post('printers/update-status/{id}', [PrintersController::class, 'updateStatus'])->name('printers.update.status');
    });

    Route::middleware(['permission:الفواتير'])->group(function () {
        // Machine Pricing Routes
        Route::get('/machines/pricing', [App\Http\Controllers\MachinesController::class, 'pricing'])->name('machines.pricing');
        Route::post('/machines/pricing/update', [App\Http\Controllers\MachinesController::class, 'updatePrice'])->name('machines.update_price');

    });





    Route::middleware(['role:super-admin'])->group(function () {

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

        // Backup Management
        Route::get('/backups', [App\Http\Controllers\BackupController::class, 'index'])->name('backups.index');
        Route::post('/backups/create', [App\Http\Controllers\BackupController::class, 'create'])->name('backups.create');
        Route::get('/backups/download/{file_name}', [App\Http\Controllers\BackupController::class, 'download'])->name('backups.download');
        Route::delete('/backups/delete/{file_name}', [App\Http\Controllers\BackupController::class, 'destroy'])->name('backups.destroy');
        Route::post('/backups/send-email/{file_name}', [App\Http\Controllers\BackupController::class, 'sendEmail'])->name('backups.send_email');

    });

    Route::middleware(['permission:المخزن'])->group(function () {

        // Inventory Routes
        Route::post('/inventory/consume-ink', [App\Http\Controllers\InventoryController::class, 'consumeInk'])->name('inventory.consumeInk');
        Route::get('/inventory', [App\Http\Controllers\InventoryController::class, 'index'])->name('inventory.index');
        Route::post('/inventory/store', [App\Http\Controllers\InventoryController::class, 'store'])->name('inventory.store');

        // Accounts
        Route::get('/accounts', [App\Http\Controllers\AccountsController::class, 'index'])->name('accounts.index');
        Route::post('/accounts/update-price', [App\Http\Controllers\AccountsController::class, 'updatePrice'])->name('accounts.update_price');

    });
    

    Route::middleware(['role:super-admin'])->group(function () {
        // Settings Routes
        Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');
    });

    Route::middleware(['permission:تقارير الاخطاء'])->group(function () {
        // Error Logs
        Route::get('/error-logs', [App\Http\Controllers\Admin\ErrorLogController::class, 'index'])->name('admin.error_logs.index');
        Route::get('/error-logs/{id}', [App\Http\Controllers\Admin\ErrorLogController::class, 'show'])->name('admin.error_logs.show');
        Route::delete('/error-logs/{id}', [App\Http\Controllers\Admin\ErrorLogController::class, 'destroy'])->name('admin.error_logs.destroy');
        Route::post('/error-logs/destroy-all', [App\Http\Controllers\Admin\ErrorLogController::class, 'destroyAll'])->name('admin.error_logs.destroy_all');
    });





    // Stras Routes
    Route::prefix('stras')->middleware(['permission:الاستراس'])->group(function () {
        Route::get('/', [App\Http\Controllers\StrasController::class, 'index'])->name('stras.index');
        Route::get('/show/{id}', [App\Http\Controllers\StrasController::class, 'show'])->name('stras.show');
        Route::post('/store', [App\Http\Controllers\StrasController::class, 'store'])->name('stras.store');
        Route::put('/update/{id}', [App\Http\Controllers\StrasController::class, 'update'])->name('stras.update');
        Route::post('/restart/{id}', [App\Http\Controllers\StrasController::class, 'restart'])->name('stras.restart');
        Route::delete('/delete/{id}', [App\Http\Controllers\StrasController::class, 'destroy'])->name('stras.delete');
        Route::post('/bulk-delete', [App\Http\Controllers\StrasController::class, 'bulkDelete'])->name('stras.bulk_delete');
        
        // Trash
        Route::get('/trash', [App\Http\Controllers\StrasController::class, 'trash'])->name('stras.trash');
        Route::post('/restore/{id}', [App\Http\Controllers\StrasController::class, 'restore'])->name('stras.restore');
        Route::delete('/force-delete/{id}', [App\Http\Controllers\StrasController::class, 'forceDelete'])->name('stras.force_delete');
        
        // Pricing
        Route::get('/pricing', [App\Http\Controllers\StrasController::class, 'pricing'])->name('stras.pricing');
        Route::post('/pricing/update', [App\Http\Controllers\StrasController::class, 'updatePrice'])->name('stras.update_price');
    });


    // Tarter Routes
    Route::prefix('tarter')->middleware(['permission:الترتر'])->group(function () {
        Route::get('/', [App\Http\Controllers\TarterController::class, 'index'])->name('tarter.index');
        Route::get('/show/{id}', [App\Http\Controllers\TarterController::class, 'show'])->name('tarter.show');
        Route::post('/store', [App\Http\Controllers\TarterController::class, 'store'])->name('tarter.store');
        Route::put('/update/{id}', [App\Http\Controllers\TarterController::class, 'update'])->name('tarter.update');
        Route::post('/restart/{id}', [App\Http\Controllers\TarterController::class, 'restart'])->name('tarter.restart');
        Route::delete('/delete/{id}', [App\Http\Controllers\TarterController::class, 'destroy'])->name('tarter.delete');
        Route::post('/bulk-delete', [App\Http\Controllers\TarterController::class, 'bulkDelete'])->name('tarter.bulk_delete');
        
        // Trash
        Route::get('/trash', [App\Http\Controllers\TarterController::class, 'trash'])->name('tarter.trash');
        Route::post('/restore/{id}', [App\Http\Controllers\TarterController::class, 'restore'])->name('tarter.restore');
        Route::delete('/force-delete/{id}', [App\Http\Controllers\TarterController::class, 'forceDelete'])->name('tarter.force_delete');
        
        // Pricing
        Route::get('/pricing', [App\Http\Controllers\TarterController::class, 'pricing'])->name('tarter.pricing');
        Route::post('/pricing/update', [App\Http\Controllers\TarterController::class, 'updatePrice'])->name('tarter.update_price');
    }); 
    

    Route::middleware(['permission:الفواتير'])->group(function () {
        // Notifications
        Route::get('/notifications/latest', [App\Http\Controllers\NotificationsController::class, 'getLatest'])->name('notifications.latest');
        Route::get('/notifications', [App\Http\Controllers\NotificationsController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationsController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    });
    
    Route::prefix('invoices')->middleware(['permission:الفواتير'])->group(function () {
        Route::get('/create', [App\Http\Controllers\InvoiceController::class, 'showCart'])->name('invoice.create');
        Route::post('/add', [App\Http\Controllers\InvoiceController::class, 'addToCart'])->name('invoice.add');
        Route::post('/mark-sent', [App\Http\Controllers\InvoiceController::class, 'markAsSent'])->name('invoice.mark_sent');
        Route::get('/remove/{id}', [App\Http\Controllers\InvoiceController::class, 'removeItem'])->name('invoice.remove');
        Route::get('/clear', [App\Http\Controllers\InvoiceController::class, 'clearCart'])->name('invoice.clear');
        Route::post('/update-customer', [App\Http\Controllers\InvoiceController::class, 'updateCustomer'])->name('invoice.update_customer');
        Route::post('/update-customer-phone', [App\Http\Controllers\InvoiceController::class, 'updateCustomerPhone'])->name('invoice.update_customer_phone');
        Route::post('/update-item', [App\Http\Controllers\InvoiceController::class, 'updateInvoiceItem'])->name('invoice.update_item');
        Route::post('/finalize', [App\Http\Controllers\InvoiceController::class, 'finalize'])->name('invoice.finalize');
        Route::get('/history', [App\Http\Controllers\InvoiceController::class, 'invoiceHistory'])->name('invoice.history');
        Route::get('/history-data', [App\Http\Controllers\InvoiceController::class, 'invoiceHistoryData'])->name('invoice.history_data');
        Route::get('/archive-details/{id}', [App\Http\Controllers\InvoiceController::class, 'getArchiveDetails'])->name('invoice.archive_details');
        Route::get('/invoice-details/{id}', [App\Http\Controllers\InvoiceController::class, 'getInvoiceDetails'])->name('invoice.invoice_details');
        Route::get('/item-details/{id}', [App\Http\Controllers\InvoiceController::class, 'getItemDetails'])->name('invoice.item_details');
        Route::post('/add-composite-item', [App\Http\Controllers\InvoiceController::class, 'addCompositeItem'])->name('invoice.add_composite_item');
    });

    // Laser Routes
    Route::prefix('laser')->middleware(['permission:الليزر'])->group(function () {
        Route::get('/', [App\Http\Controllers\LaserController::class, 'index'])->name('laser.index');
        Route::get('/show/{id}', [App\Http\Controllers\LaserController::class, 'show'])->name('laser.show');
        Route::post('/store', [App\Http\Controllers\LaserController::class, 'store'])->name('laser.store');
        Route::put('/update/{id}', [App\Http\Controllers\LaserController::class, 'update'])->name('laser.update');
        Route::post('/restart/{id}', [App\Http\Controllers\LaserController::class, 'restart'])->name('laser.restart');
        Route::delete('/delete/{id}', [App\Http\Controllers\LaserController::class, 'destroy'])->name('laser.delete');
        Route::post('/bulk-delete', [App\Http\Controllers\LaserController::class, 'bulkDelete'])->name('laser.bulk_delete');
        Route::post('/bulk-recalculate', [App\Http\Controllers\LaserController::class, 'bulkRecalculate'])->name('laser.bulk_recalculate');
        
        // Trash
        Route::get('/trash', [App\Http\Controllers\LaserController::class, 'trash'])->name('laser.trash');
        Route::post('/restore/{id}', [App\Http\Controllers\LaserController::class, 'restore'])->name('laser.restore');
        Route::delete('/force-delete/{id}', [App\Http\Controllers\LaserController::class, 'forceDelete'])->name('laser.force_delete');
        
        // Pricing
        Route::get('/pricing', [App\Http\Controllers\LaserController::class, 'pricing'])->name('laser.pricing');
        Route::post('/pricing/update', [App\Http\Controllers\LaserController::class, 'updatePrice'])->name('laser.update_price');
    });

    Route::middleware(['permission:الرواتب'])->group(function () {
        // Attendance & Payroll
        Route::get('/attendance', [App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/payroll', [App\Http\Controllers\AttendanceController::class, 'payroll'])->name('payroll.index');
    });


    // Biometric Attendance Routes
    Route::prefix('biometric')->middleware(['permission:الرواتب'])->name('biometric.')->group(function () {
        Route::get('/', [App\Http\Controllers\BiometricAttendanceController::class, 'index'])->name('index');
        Route::post('/upload', [App\Http\Controllers\BiometricAttendanceController::class, 'upload'])->name('upload');
        Route::post('/users/update/{id}', [App\Http\Controllers\BiometricAttendanceController::class, 'updateUser'])->name('users.update');
        Route::post('/generate-absences', [App\Http\Controllers\BiometricAttendanceController::class, 'generateAbsences'])->name('generate_absences');
        Route::delete('/clear', [App\Http\Controllers\BiometricAttendanceController::class, 'destroyAll'])->name('clear');
    });

    // Customer Routes
    Route::prefix('customers')->middleware(['permission:العملاء'])->group(function () {
        Route::get('/', [App\Http\Controllers\CustomerController::class, 'index'])->name('customers.index');
        Route::get('/search', [App\Http\Controllers\CustomerController::class, 'search'])->name('customers.search');
        Route::get('/{id}', [App\Http\Controllers\CustomerController::class, 'show'])->name('customers.show');
    });

    // activityLog
    Route::prefix('activity-logs')->middleware(['permission:تقارير الاخطاء'])->group(function () {
    Route::get('/system-logs', [App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-logs.index');
    });


    // Email System Routes
    Route::prefix('mail')->group(function () {
        Route::get('/', [App\Http\Controllers\MailController::class, 'index'])->name('mail.index');
        Route::get('/sent', [App\Http\Controllers\MailController::class, 'sent'])->name('mail.sent');
        Route::get('/create', [App\Http\Controllers\MailController::class, 'create'])->name('mail.create');
        Route::post('/store', [App\Http\Controllers\MailController::class, 'store'])->name('mail.store');
        Route::get('/{id}', [App\Http\Controllers\MailController::class, 'show'])->name('mail.show');
    });

    Route::get('/mail-template-preview', function () {
        return new App\Mail\GeneralMail('Test Email Subject', "This is a test email body.\n\nIt supports multi-line content.");
    });

});





