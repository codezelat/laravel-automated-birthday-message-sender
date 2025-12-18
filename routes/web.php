<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ContactController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['admin.auth'])->group(function () {
    Route::resource('contacts', ContactController::class);
    Route::post('contacts/import', [ContactController::class, 'import'])->name('contacts.import');
    Route::get('contacts/export/csv', [ContactController::class, 'export'])->name('contacts.export');
    Route::post('contacts/send-today', [ContactController::class, 'sendToday'])->name('contacts.send_today');
    Route::post('contacts/bulk-delete', [ContactController::class, 'bulkDestroy'])->name('contacts.bulk_delete');
    Route::post('contacts/delete-all', [ContactController::class, 'deleteAll'])->name('contacts.delete_all');
    Route::post('contacts/{contact}/send-manual', [ContactController::class, 'sendManual'])->name('contacts.send_manual');
    Route::get('logs', [App\Http\Controllers\ActivityLogController::class, 'index'])->name('logs.index');
});

use App\Http\Controllers\BirthdayController;
Route::get('/birthday/{token}', [BirthdayController::class, 'show'])->name('birthday.show');
Route::get('/birthday/{token}/card', [BirthdayController::class, 'card'])->name('birthday.card');
Route::get('/birthday/{token}/download', [BirthdayController::class, 'download'])->name('birthday.download');

