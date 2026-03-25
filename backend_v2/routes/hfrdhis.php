<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HfrDhisController;

Route::post('admin/hfr-dhis-exchange/store', [HfrDhisController::class, 'store'])->name('dhis.store');
Route::post('admin/hfr-dhis-exchange/update', [HfrDhisController::class, 'update'])->name('dhis.update');
Route::post('admin/hfr-dhis-exchange/delete', [HfrDhisController::class, 'delete'])->name('dhis.delete');
Route::get('admin/hfr-dhis-exchange/logs', [HfrDhisController::class, 'logs'])->name('dhis.logs');

Route::get('admin/hfr-dhis-exchange/lookup', [HfrDhisController::class, 'lookupIndex'])->name('dhis.lookup');
Route::post('admin/hfr-dhis-exchange/lookup/store', [HfrDhisController::class, 'lookupStore'])->name('dhis.lookupStore');
Route::put('admin/hfr-dhis-exchange/lookup/update', [HfrDhisController::class, 'lookupUpdate'])->name('dhis.lookupUpdate');
Route::get('admin/hfr-dhis-exchange/lookup/search', [HfrDhisController::class, 'lookupSearch'])->name('dhis.lookupSearch');

Route::post('admin/hfr-dhis-exchange/resend', [HfrDhisController::class, 'resend'])->name('dhis.resend');

Route::get('admin/hfr-dhis-exchange/test', [HfrDhisController::class, 'store2']);
