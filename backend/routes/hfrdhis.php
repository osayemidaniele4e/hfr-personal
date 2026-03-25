<?php

Route::post('admin/hfr-dhis-exchange/store', 'HfrDhisController@store')->name('dhis.store');
Route::post('admin/hfr-dhis-exchange/update', 'HfrDhisController@update')->name('dhis.update');
Route::post('admin/hfr-dhis-exchange/delete', 'HfrDhisController@delete')->name('dhis.delete');
Route::get('admin/hfr-dhis-exchange/logs', 'HfrDhisController@logs')->name('dhis.logs');

Route::get('admin/hfr-dhis-exchange/lookup', 'HfrDhisController@lookupIndex')->name('dhis.lookup');
Route::post('admin/hfr-dhis-exchange/lookup/store', 'HfrDhisController@lookupStore')->name('dhis.lookupStore');
Route::put('admin/hfr-dhis-exchange/lookup/update', 'HfrDhisController@lookupUpdate')->name('dhis.lookupUpdate');
Route::get('admin/hfr-dhis-exchange/lookup/search', 'HfrDhisController@lookupSearch')->name('dhis.lookupSearch');

Route::post('admin/hfr-dhis-exchange/resend', 'HfrDhisController@resend')->name('dhis.resend');



Route::get('admin/hfr-dhis-exchange/test', 'HfrDhisController@store2');