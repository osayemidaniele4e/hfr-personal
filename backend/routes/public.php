<?php

//home
Route::get('/', 'HomeController@index')->name('home');

Route::post('home/facilitiesbyLga', 'HomeController@getFacilitesByLGA')->name('getFacilitesByLGA');
Route::post('home/googlemap', 'HomeController@getFacilitesGMap')->name('getFacilitesGMap');
Route::post('home/googlemap/facilitydetails', 'HomeController@getFacilityDetails')->name('getFacilityDetails');

Route::get('about', 'HomeController@about')->name('about');

Route::get('contactus', 'ContactController@openContactForm')->name('open_contact_form');
Route::post('contactus', 'ContactController@store')->name('storecontact');

Route::get('facilities/hospitals-list', 'FacilityListingController@getHospitals')->name('list.hospitals');
Route::get('facilities/hospitals-search', 'FacilityListingController@searchHospitals')->name('search.hospitals');
Route::get('facilities/pharmacies-list', 'FacilityListingController@getPharmacy')->name('list.pharmacy');
Route::get('facilities/pharmacies-search', 'FacilityListingController@searchPharmacy')->name('search.pharmacy');
Route::get('facilities/laboratory-list', 'FacilityListingController@getLab')->name('list.laboratory');
Route::get('facilities/laboratory-search', 'FacilityListingController@searchLab')->name('search.laboratory');
Route::get('facilities/imaging-list', 'FacilityListingController@getImaging')->name('list.imaging');
Route::get('facilities/imaging-search', 'FacilityListingController@searchImaging')->name('search.imaging');

Route::get('facilities/latest-updates/view', 'FacilityListingController@getUpdates')->name('view.updates');
Route::get('facilities/latest-updates', 'FacilityListingController@updates')->name('latest.updates');



//summary tables
Route::get('statistics/tables', 'SummaryTablesController@index')->name('statistics');
Route::get('statistics/tables/filter', 'SummaryTablesController@filter')->name('filterStatistics');

//summary charts
Route::get('statistics/charts', 'SummaryChartsController@index')->name('statistics_charts');
Route::post('statistics/charts/filter', 'SummaryChartsController@filter')->name('filterStatisticsCharts');
Route::get('statistics/populationindex', 'SummaryChartsController@population_index')->name('population_index');
Route::post('statistics/populationindex/filter', 'SummaryChartsController@population_index_filter')->name('population_filter');


//download facilies
Route::get('download/facilities', 'DownloadController@openRegistrationForm')->name('openRegistrationForm');
Route::post('download/facilities', 'DownloadController@store')->name('saveDownloadUserRecords');
Route::get('download/facility-list', 'DownloadController@index')->name('downloadFacilitiesList');
Route::get('download/export-data', 'DownloadController@export')->name('download.export');
Route::get('download/validate', 'DownloadController@getValidationForm')->name('getValidateForm');
Route::post('download/validate', 'DownloadController@validateToken')->name('validateToken');


//routes to populate lgas and wards
Route::post('hosp/fetchLga', 'GeneralController@getLgaList')->name('getLgaList');
Route::post('hosp/fetctWards', 'GeneralController@getWardList')->name('getWardList');

Route::post('hosp/services', 'GeneralController@getServices')->name('getServices');


Route::post('hospitals/servicesavailable','HospitalsController@getservices')->name('hospitals.getServices');
Route::post('hospitals/servicesavailable/history','HospitalsController@getservicesHistory')->name('hospitals.getServicesHistory');


//resources
Route::get('resources/download/{file}', 'ResourceController@download')->name('downloadFile');
Route::get('resources', 'ResourceController@public_index')->name('public_resources');

//to factor token
Route::get('login/token', 'Auth\TokenController@getToken');
Route::post('login/token', 'Auth\TokenController@postToken')->name('login.token');