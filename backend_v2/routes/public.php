<?php

//home

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FacilityListingController;
use App\Http\Controllers\SummaryTablesController;
use App\Http\Controllers\SummaryChartsController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\HospitalsController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\Auth\TokenController;
use App\Http\Controllers\ValidateDownloadController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::post('home/facilitiesbyLga', [HomeController::class, 'getFacilitesByLGA'])->name('getFacilitesByLGA');
Route::post('home/googlemap', [HomeController::class, 'getFacilitesGMap'])->name('getFacilitesGMap');
Route::post('home/googlemap/facilitydetails', [HomeController::class, 'getFacilityDetails'])->name('getFacilityDetails');

Route::get('about', [HomeController::class, 'about'])->name('about');

Route::get('contactus', [ContactController::class, 'openContactForm'])->name('open_contact_form');
Route::post('contactus', [ContactController::class, 'store'])->name('storecontact');

Route::get('facilities/hospitals-list', [FacilityListingController::class, 'getHospitals'])->name('list.hospitals');
Route::get('facilities/hospitals-search', [FacilityListingController::class, 'searchHospitals'])->name('search.hospitals');
Route::get('facilities/pharmacies-list', [FacilityListingController::class, 'getPharmacy'])->name('list.pharmacy');
Route::get('facilities/pharmacies-search', [FacilityListingController::class, 'searchPharmacy'])->name('search.pharmacy');
Route::get('facilities/laboratory-list', [FacilityListingController::class, 'getLab'])->name('list.laboratory');
Route::get('facilities/laboratory-search', [FacilityListingController::class, 'searchLab'])->name('search.laboratory');
Route::get('facilities/imaging-list', [FacilityListingController::class, 'getImaging'])->name('list.imaging');
Route::get('facilities/imaging-search', [FacilityListingController::class, 'searchImaging'])->name('search.imaging');

Route::get('facilities/latest-updates/view', [FacilityListingController::class, 'getUpdates'])->name('view.updates');
Route::get('facilities/latest-updates', [FacilityListingController::class, 'updates'])->name('latest.updates');

// Summary Tables
Route::get('statistics/tables', [SummaryTablesController::class, 'index'])->name('statistics');
Route::get('statistics/tables/filter', [SummaryTablesController::class, 'filter'])->name('filterStatistics');

// Summary Charts
Route::get('statistics/charts', [SummaryChartsController::class, 'index'])->name('statistics_charts');
Route::post('statistics/charts/filter', [SummaryChartsController::class, 'filter'])->name('filterStatisticsCharts');
Route::get('statistics/populationindex', [SummaryChartsController::class, 'population_index'])->name('population_index');
Route::post('statistics/populationindex/filter', [SummaryChartsController::class, 'population_index_filter'])->name('population_filter');

// Download Facilities
Route::get('download/facilities', [DownloadController::class, 'openRegistrationForm'])->name('openRegistrationForm');
Route::post('download/facilities', [DownloadController::class, 'store'])->name('saveDownloadUserRecords');
Route::get('download/facility-list', [DownloadController::class, 'index'])->name('downloadFacilitiesList');
Route::get('download/export-data', [DownloadController::class, 'export'])->name('download.export');
Route::get('download/validate', [DownloadController::class, 'getValidationForm'])->name('getValidateForm');
Route::post('download/validate', [DownloadController::class, 'validateToken'])->name('validateToken');

// LGA & Wards
Route::post('hosp/fetchLga', [GeneralController::class, 'getLgaList'])->name('getLgaList');
Route::post('hosp/fetctWards', [GeneralController::class, 'getWardList'])->name('getWardList');
Route::post('hosp/services', [GeneralController::class, 'getServices'])->name('getServices');

// Hospital Services
Route::post('hospitals/servicesavailable', [HospitalsController::class, 'getservices'])->name('hospitals.getServices');
Route::post('hospitals/servicesavailable/history', [HospitalsController::class, 'getservicesHistory'])->name('hospitals.getServicesHistory');

// Resources
Route::get('resources/download/{file}', [ResourceController::class, 'download'])->name('downloadFile');
Route::get('resources', [ResourceController::class, 'public_index'])->name('public_resources');

// Token Auth
Route::get('login/token', [TokenController::class, 'getToken']);
Route::post('login/token', [TokenController::class, 'postToken'])->name('login.token');


