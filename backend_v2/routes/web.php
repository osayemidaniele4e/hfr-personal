<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminHomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HospitalsController;
use App\Http\Controllers\Approval\MyRequestController;
use App\Http\Controllers\Approval\VerifyController;
use App\Http\Controllers\Approval\ValidateController;
use App\Http\Controllers\Approval\PublishController;
use App\Http\Controllers\Approval\ApprovalController;
use App\Http\Controllers\Approval\UpdatedRecordsController;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\LabController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\ImagingController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\ImagingServiceController;
use App\Http\Controllers\HospitalServiceController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\CertificationController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\LgaController;
use App\Http\Controllers\WardController;
use App\Http\Controllers\FacilityReportController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\AboutUsController;
use App\Http\Controllers\AuditTrail;
use App\Http\Controllers\ValidateDownloadController;
use App\Http\Controllers\ApiClientController;
use App\Http\Controllers\HospitalImportController;

// use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Load public routes
require base_path('routes/public.php');


// Authenticated routes

Route::middleware(["auth"])->group(function () {

    Route::middleware(["newuser"])->group(function () {

        Route::get('administrator', [AdminHomeController::class, 'index'])->name('admin_home');
        Route::post('administrator/completeness', [AdminHomeController::class, 'completeness'])->name('state.completeness');
        Route::post('administrator/facilitystatus', [AdminHomeController::class, 'facilityStatus'])->name('state.facilitystatus');



        require base_path('routes/hfrdhis.php');

        //api clients management
        Route::get('admin/api-clients', [ApiClientController::class, 'index'])->name('api-clients.index');
        Route::get('admin/api-clients/pending', [ApiClientController::class, 'pending'])->name('api-clients.pending');
        Route::post('admin/api-clients/approve', [ApiClientController::class, 'approve'])->name('api-clients.approve');
        Route::post('admin/api-clients/reject', [ApiClientController::class, 'reject'])->name('api-clients.reject');
        Route::get('admin/api-clients/create', [ApiClientController::class, 'create'])->name('api-clients.create');
        Route::post('admin/api-clients', [ApiClientController::class, 'store'])->name('api-clients.store');
        Route::post('admin/api-clients/toggle', [ApiClientController::class, 'toggleStatus'])->name('api-clients.toggle');
        Route::post('admin/api-clients/update', [ApiClientController::class, 'update'])->name('api-clients.update');
        Route::post('admin/api-clients/regenerate', [ApiClientController::class, 'regenerate'])->name('api-clients.regenerate');
        Route::post('admin/api-clients/delete', [ApiClientController::class, 'destroy'])->name('api-clients.destroy');
        Route::get('admin/api-clients/{id}/logs', [ApiClientController::class, 'logs'])->name('api-clients.logs');

        //roles
        Route::get('admin/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('admin/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('admin/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('admin/roles/{id}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::post('admin/roles/update', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('admin/roles/delete', [RoleController::class, 'destroy'])->name("roles.destroy");


        //users
        Route::get('admin/users', [UserController::class, 'index'])->name('users.index');
        Route::get('admin/users/register', [UserController::class, 'create']);
        Route::post('admin/users/register', [UserController::class, 'store'])->name("registeruser");
        Route::post('admin/users/update', [UserController::class, 'update'])->name("updateuser");
        Route::put('admin/users/delete', [UserController::class, 'delete'])->name("deleteUser");
        Route::put('admin/users/block', [UserController::class, 'block'])->name("blockUser");
        Route::post('admin/changepassword', [UserController::class, 'changePassword'])->name('changePassword');
        Route::get('admin/profile', [UserController::class, 'profile'])->name('profile');
        Route::post('admin/profile/update', [UserController::class, 'updateProfile'])->name("updateProfile");
        Route::get('admin/users/search', [UserController::class, 'search'])->name("users.search");
        Route::post('admin/users/role', [UserController::class, 'getRoleID'])->name('getRoleID');


        //hospitals
        Route::get('admin/hospitals/search', [HospitalsController::class, 'search'])->name('searchHospitalsAdmin');
        Route::post('admin/hospitals/delete', [HospitalsController::class, 'InitiateDelete'])->name('hospitals.InitiateDelete');
        Route::post('admin/hospitals/export', [HospitalsController::class, 'export'])->name('hospitals.export');

        //hospital import (must be before resource route)
        Route::get('admin/hospitals/import', [HospitalImportController::class, 'index'])->name('hospitals.import.index');
        Route::get('admin/hospitals/import/template', [HospitalImportController::class, 'downloadTemplate'])->name('hospitals.import.template');
        Route::post('admin/hospitals/import/upload', [HospitalImportController::class, 'upload'])->name('hospitals.import.upload');
        Route::get('admin/hospitals/import/{batchId}/preview', [HospitalImportController::class, 'preview'])->name('hospitals.import.preview');
        Route::get('admin/hospitals/import/{batchId}/{id}/edit', [HospitalImportController::class, 'edit'])->name('hospitals.import.edit');
        Route::put('admin/hospitals/import/{batchId}/{id}', [HospitalImportController::class, 'update'])->name('hospitals.import.update');
        Route::delete('admin/hospitals/import/{batchId}/{id}', [HospitalImportController::class, 'destroy'])->name('hospitals.import.destroy');
        Route::delete('admin/hospitals/import/{batchId}', [HospitalImportController::class, 'destroyBatch'])->name('hospitals.import.destroyBatch');
        Route::post('admin/hospitals/import/{batchId}/submit', [HospitalImportController::class, 'submit'])->name('hospitals.import.submit');

        Route::resource('admin/hospitals', HospitalsController::class)->except(['show', 'destroy']);

        Route::post('admin/hospitals/admin-update', [HospitalsController::class, 'adminUpdate'])->name('hospitals.adminupdate');


        //my requests
        Route::get('admin/hospitals/myrequest/pending', [MyRequestController::class, 'myPendingRequest'])->name('myrequest.pending');
        Route::get('admin/hospitals/myrequest/update/{id}', [MyRequestController::class, 'editRequest'])->name('myrequest.edit');
        Route::put('admin/hospitals/myrequest/updates', [MyRequestController::class, 'updateRequest'])->name('myrequest.update');
        Route::post('admin/hospitals/myrequest/delete', [MyRequestController::class, 'deleteRequest'])->name('myrequest.delete');
        Route::post('admin/hospitals/myrequest/delete-resubmit', [MyRequestController::class, 'resubmit'])->name('myrequest.resubmit');
        Route::get('admin/hospitals/myrequest/show', [MyRequestController::class, 'search'])->name('myrequest.search');

        //approvals-verify
        Route::get('admin/hospitals/approvals/verify', [VerifyController::class, 'index'])->name('verify.pending');
        Route::post('admin/hospitals/approvals/verify', [VerifyController::class, 'store'])->name('verify.store');
        Route::post('admin/hospitals/approvals/verify-recall', [VerifyController::class, 'recall'])->name('verify.recall');
        Route::get('admin/hospitals/approvals/verify/show', [VerifyController::class, 'search'])->name('verify.search');

        //approvals-validation
        Route::get('admin/hospitals/approvals/validation', [ValidateController::class, 'index'])->name('validate.pending');
        Route::post('admin/hospitals/approvals/validation', [ValidateController::class, 'store'])->name('validate.store');
        Route::post('admin/hospitals/approvals/validation-recall', [ValidateController::class, 'recall'])->name('validate.recall');
        Route::get('admin/hospitals/approvals/validation/show', [ValidateController::class, 'search'])->name('validate.search');
        //approvals-publication
        Route::get('admin/hospitals/approvals/publish', [PublishController::class, 'index'])->name('publish.pending');
        Route::post('admin/hospitals/approvals/publish', [PublishController::class, 'store'])->name('publish.store');
        Route::get('admin/hospitals/approvals/publish/show', [PublishController::class, 'search'])->name('publish.search');
        //approvals- tracking
        Route::get('admin/hospitals/approvals/approval-tracking', [ApprovalController::class, 'tracking'])->name('approval.tracking');
        Route::get('admin/hospitals/approvals/approval-tracking/show', [ApprovalController::class, 'tracking_search'])->name('approval.trackingsearch');


        Route::get('admin/hospitals/approvals/updated/{id}/{stage}', [UpdatedRecordsController::class, 'updatedRecords'])->name('view.updated_records');

        //general
        Route::post('admin/facilities/ownership', [GeneralController::class, 'getOwnershipType'])->name('getOwnershipType');
        Route::post('admin/facilities/facility-level-option', [GeneralController::class, 'getFacilityLevelOption'])->name('getFacilityLevelOption');
        Route::post('admin/facilities/facilitys-pecialized-option', [GeneralController::class, 'getSpecializedOptions'])->name('getSpecializedOptions');

        //laboratory
        Route::get('admin/laboratory/search', [LabController::class, 'search'])->name('laboratory.search');
        Route::resource('admin/laboratory', LabController::class)->except(['show']);
        Route::get('admin/laboratory/show/{id}', [LabController::class, 'show'])->name('laboratory.show');

        //Pharmacy
        Route::get('admin/pharmacies/search', [PharmacyController::class, 'search'])->name('pharmacy.search');
        Route::resource('admin/pharmacies', PharmacyController::class)->except(['show']);
        Route::get('admin/pharmacies/show/{id}', [PharmacyController::class, 'show'])->name('pharmacy.show');

        //Imaging and Radiology premises
        Route::get('admin/imaging/search', [ImagingController::class, 'search'])->name('imaging.search');
        Route::resource('admin/imaging', ImagingController::class)->except(['show']);
        Route::get('admin/imaging/show/{id}', [ImagingController::class, 'show'])->name('imaging.show');

        //resources
        Route::get('admin/resources', [ResourceController::class, 'index'])->name('resources');
        Route::get('admin/resources/upload', [ResourceController::class, 'upload'])->name('upload');
        Route::post('admin/resources/uploads', [ResourceController::class, 'store'])->name('savefile');
        Route::post('admin/resources/delete', [ResourceController::class, 'destroy'])->name('deleteFile');
        Route::post('admin/resources/edit', [ResourceController::class, 'update'])->name('updateResource');

        //messages
        Route::get('admin/messages', [ContactController::class, 'index'])->name('getMessages');

        //download
        Route::get('admin/download/list', [DownloadController::class, 'DownloadRequests'])->name('download.requests');

        //** ***** masters routes ******** */
        Route::resource('admin/masters/imaging-services', ImagingServiceController::class);
        Route::resource('admin/masters/hospital-services', HospitalServiceController::class);
        Route::resource('admin/masters/equipments', EquipmentController::class);
        Route::resource('admin/masters/certifications', CertificationController::class);
        Route::resource('admin/masters/states', StateController::class);
        Route::resource('admin/masters/lgas', LgaController::class);
        Route::get('admin/masters/wards/search', [WardController::class, 'search'])->name('wards.search');
        Route::get('admin/masters/lga/search', [LgaController::class, 'search'])->name('lga.search');
        Route::resource('admin/masters/wards', WardController::class);


        //reports
        Route::get('admin/reports/facility-list/updates-select', [FacilityReportController::class, 'updateSelection'])->name('updates.selection');
        Route::get('admin/reports/facility-list/updates', [FacilityReportController::class, 'getUpdatesReport'])->name('updates.report');
        Route::post('admin/reports/facility-list/updates-download', [FacilityReportController::class, 'updatesDownload'])->name('updates.download');
        Route::get('admin/reports/facility-services/index', [FacilityReportController::class, 'servicesIndex'])->name('services.index');
        Route::get('admin/reports/facility-services/report', [FacilityReportController::class, 'getServicesReport'])->name('services.report');
        Route::post('admin/reports/facility-services/download', [FacilityReportController::class, 'servicesDownload'])->name('services.download');
        Route::get('admin/reports/facility-status/index', [FacilityReportController::class, 'statusIndex'])->name('status.index');
        Route::get('admin/reports/facility-status/report', [FacilityReportController::class, 'getStatusReport'])->name('status.report');
        Route::post('admin/reports/facility-status/download', [FacilityReportController::class, 'statusDownload'])->name('status.download');
        Route::get('admin/reports/approvers-summary', [FacilityReportController::class, 'approversIndex'])->name('approvers.index');
        Route::get('admin/reports/approvers-summary/report', [FacilityReportController::class, 'approversSummary'])->name('approvers.report');
        Route::get('admin/reports/facility-status-details/index', [FacilityReportController::class, 'statusDetailsIndex'])->name('status.detailsIndex');
        Route::get('admin/reports/facility-status-details/report', [FacilityReportController::class, 'statusDetailsReport'])->name('status.detailsReport');
        Route::post('admin/reports/facility-status-details/download', [FacilityReportController::class, 'statusDetailsDownload'])->name('status.detailsDownload');
    });

    Route::get('admin/new-user/change-password', [UserController::class, 'newUserChangePasswordForm'])->name('newuser.PasswordForm');
    Route::post('admin/new-user/change-password', [UserController::class, 'newUserChangePassword'])->name('newuser.ChangePassword');



    Route::get('slider', [SliderController::class, 'index'])->name('slider.index');
    Route::post('slider', [SliderController::class, 'store'])->name('slider.store');
    Route::put('slider', [SliderController::class, 'update'])->name('slider.update');
    Route::delete('slider-delete/{id}', [SliderController::class, 'destroy'])->name('slider.destroy');


    Route::get('origin', [AboutUsController::class, 'origin'])->name('about-us.index');
    Route::put('about-us', [AboutUsController::class, 'updateOrigin'])->name('about-us.update');


    Route::get('process', [AboutUsController::class, 'process'])->name('process.index');
    Route::put('process', [AboutUsController::class, 'updateProcess'])->name('process.update');


    // Route::get('process-item',AboutUsController::class,'index'])->name('process-item.index');
    Route::put('process-item', [AboutUsController::class, 'update'])->name('process-item.update');
    Route::post('process-item', [AboutUsController::class, 'store'])->name('process-item.store');
    Route::delete('process-item-delete/{id}', [AboutUsController::class, 'destroy'])->name('process-item.destroy');

    Route::get('audit-trail', [AuditTrail::class, 'index'])->name('audit.trail');
});


Route::get('validate-token', [ValidateDownloadController::class, 'showValidate'])->name('showValidate');
Route::post('validate-download', [ValidateDownloadController::class, 'validateDownload'])->name('verifyToken');




Auth::routes();


// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
