<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

// -----------------------------------------------------------------------------
// Default route

Route::get('/', function() {
  return Redirect::to('admin');
});

// -----------------------------------------------------------------------------
// Command route

Route::get('command/db/seed', function() {
  $user = Sentry::findUserById(20);
  Sentry::login($user);
  Artisan::call('db:seed');
});

// -----------------------------------------------------------------------------
// Command update artisan
Route::get('/updateapp', function() {
    Artisan::call('dump-autoload');
    echo 'dump-autoload complete';
});

// -----------------------------------------------------------------------------
// Admin routes

Route::group(array('prefix' => 'admin'), function() {

  // ---------------------------------------------------------------------------
  // Unprotected routes

  Route::group(array(), function() {

    // -------------------------------------------------------------------------
    // Default admin route

    Route::get('/', function() {
      return Redirect::to('admin/process-log');
    });

    // -------------------------------------------------------------------------
    // Denie routes

    Route::get('denie', function() {
      return View::make('admins.misc.denie');
    });

    // -------------------------------------------------------------------------
    // User routes

    Route::get('user/logout', 'AdminUserController@getLogout');
    Route::get('user/login', 'AdminUserController@getLogin')->before('auth.login');

    Route::post('user/login', 'AdminUserController@postLogin');

  });

  // -------------------------------------------------------------------------
  // User group routes

  Route::group(array('before' => 'auth.has_access:manage_user_groups'), function() {

    Route::get('group', 'AdminGroupController@getIndex');
    Route::get('group/create', 'AdminGroupController@getCreate');
    Route::get('group/{id}/update', 'AdminGroupController@getUpdate');
    Route::get('group/{id}/delete', 'AdminGroupController@getDelete');

    Route::post('group/create', 'AdminGroupController@postCreate');
    Route::post('group/{id}/update', 'AdminGroupController@postUpdate');
    Route::post('group/{id}/delete', 'AdminGroupController@postDelete');

    Route::post('group/data-table', 'AdminGroupController@getDataTable');

  });

  // -------------------------------------------------------------------------
  // Permission routes

  Route::group(array('before' => 'auth.has_access:manage_user_groups'), function() {

    Route::get('permission', 'AdminPermissionController@getIndex');
    Route::post('permission', 'AdminPermissionController@postIndex');

  });

  // -------------------------------------------------------------------------
  // User routes

  Route::group(array('before' => 'auth.has_access:manage_users'), function() {

    Route::get('user', 'AdminUserController@getIndex');
    Route::get('user/create', 'AdminUserController@getCreate');
    Route::get('user/{id}/update', 'AdminUserController@getUpdate');
    Route::get('user/{id}/delete', 'AdminUserController@getDelete');

    Route::post('user/create', 'AdminUserController@postCreate');
    Route::post('user/{id}/update', 'AdminUserController@postUpdate');
    Route::post('user/{id}/delete', 'AdminUserController@postDelete');

    Route::post('user/data-table', 'AdminUserController@getDataTable');

  });

  // -------------------------------------------------------------------------
  // Line routes

  Route::group(array('before' => 'auth.has_access:manage_production_lines'), function() {

    Route::get('line', 'AdminLineController@getIndex');
    Route::get('line/create', 'AdminLineController@getCreate');
    Route::get('line/{id}/update', 'AdminLineController@getUpdate');
    Route::get('line/{id}/delete', 'AdminLineController@getDelete');
    Route::get('line/{id}/access', 'AdminLineController@getAccess');

    //Route::get('line/{id}/export', 'AdminLineController@getExport');
    Route::get('line/{id}/import', 'AdminLineController@getImport');

    Route::post('line/create', 'AdminLineController@postCreate');
    Route::post('line/{id}/update', 'AdminLineController@postUpdate');
    Route::post('line/{id}/delete', 'AdminLineController@postDelete');

    Route::post('line/data-table', 'AdminLineController@getDataTable');

    Route::post('line/{id}/import', 'AdminLineController@postImport');

  });

  // -------------------------------------------------------------------------
  // Model routes

  Route::group(array('before' => 'auth.has_access:manage_models'), function() {

    Route::get('model', 'AdminProductController@getIndex');
    Route::get('model/create', 'AdminProductController@getCreate');
    Route::get('model/{id}/update', 'AdminProductController@getUpdate');
    Route::get('model/{id}/delete', 'AdminProductController@getDelete');

    Route::get('model/create-form', 'AdminProductController@getCreateForm');

    Route::post('model/create', 'AdminProductController@postCreate');
    Route::post('model/{id}/update', 'AdminProductController@postUpdate');
    Route::post('model/{id}/delete', 'AdminProductController@postDelete');
    Route::post('model/create-form/validate', 'AdminProductController@postCreateFormValidate');

    Route::post('model/data-table', 'AdminProductController@getDataTable');

  });

  // -------------------------------------------------------------------------
  // Process routes

  Route::group(array('before' => 'auth.has_access:manage_processes'), function() {

    Route::get('process', 'AdminProcessController@getIndex');
    Route::get('process/create', 'AdminProcessController@getCreate');
    Route::get('process/{id}/update', 'AdminProcessController@getUpdate');
    Route::get('process/{id}/delete', 'AdminProcessController@getDelete');

    Route::get('process/create-form', 'AdminProcessController@getCreateForm');

    Route::post('process/create', 'AdminProcessController@postCreate');
    Route::post('process/{id}/update', 'AdminProcessController@postUpdate');
    Route::post('process/{id}/delete', 'AdminProcessController@postDelete');
    Route::post('process/create-form/validate', 'AdminProcessController@postCreateFormValidate');

    Route::post('process/data-table', 'AdminProcessController@getDataTable');

  });

  //Part and Lot
  Route::group(array('before' => 'auth.has_access:manage_part'), function() {
    Route::get('part', 'AdminPartController@getIndex');
    Route::get('part/create', 'AdminPartController@getCreate');
    Route::get('part/{id}/update', 'AdminPartController@getUpdate');
    Route::get('part/{id}/delete', 'AdminPartController@getDelete');
    Route::get('part/process-select', 'AdminPartController@getProcessSelect');//Select process
    Route::get('part/import', 'AdminPartController@getImport');
    Route::get('part/export', 'AdminPartController@getExport');
    Route::get('part/delete-multi', 'AdminPartController@getDeleteMulti');
    Route::get('part/delete-multi-confirmed', 'AdminPartController@getDeleteMultiConfirmed');

    Route::get('part/check', 'AdminPartController@partCheck');//Test
    Route::get('part/test', 'AdminPartController@getTest');//Test

    Route::post('part/create', 'AdminPartController@postCreate');
    Route::post('part/{id}/update', 'AdminPartController@postUpdate');
    Route::post('part/{id}/delete', 'AdminPartController@postDelete');
    Route::post('part/import', 'AdminPartController@postImport');

    Route::post('part/data-table', 'AdminPartController@getDataTable');
    Route::post('part/process-select', 'AdminPartController@getProcessSelect');//Select process
  });
  Route::group(array('before' => 'auth.has_access:manage_iqc'), function() {
    Route::get('iqc-lot', 'AdminIqcLotController@getIndex');
    Route::get('iqc-lot/create', 'AdminIqcLotController@getCreate');
    Route::get('iqc-lot/{id}/update', 'AdminIqcLotController@getUpdate');
    Route::get('iqc-lot/{id}/delete', 'AdminIqcLotController@getDelete');
    Route::get('iqc-lot/test', 'AdminIqcLotController@getTest');//Test
    Route::get('iqc-lot/product-select', 'AdminIqcLotController@getProductSelect');//Select product
    Route::get('iqc-lot/process-select', 'AdminIqcLotController@getProcessSelect');//Select process
    Route::get('iqc-lot/import', 'AdminIqcLotController@getImport');
    Route::get('iqc-lot/export', 'AdminIqcLotController@getExport');
    Route::get('iqc-lot/import-custom', 'AdminIqcLotController@getImportCustom');
    Route::get('iqc-lot/delete-multi', 'AdminIqcLotController@getDeleteMulti');
    Route::get('iqc-lot/delete-multi-confirmed', 'AdminIqcLotController@getDeleteMultiConfirmed');

    Route::post('iqc-lot/create', 'AdminIqcLotController@postCreate');
    Route::post('iqc-lot/{id}/update', 'AdminIqcLotController@postUpdate');
    Route::post('iqc-lot/{id}/delete', 'AdminIqcLotController@postDelete');
    Route::post('iqc-lot/import', 'AdminIqcLotController@postImport');
    Route::post('iqc-lot/import-custom', 'AdminIqcLotController@postImportCustom');

    Route::post('iqc-lot/data-table', 'AdminIqcLotController@getDataTable');
  });
  Route::group(array('before' => 'auth.has_access:manage_lot'), function() {
    Route::get('lot', 'AdminLotController@getIndex');
    Route::get('lot/{id}/detail', 'AdminLotController@getDetail');
    Route::get('lot/{id}/delete', 'AdminLotController@getDelete');
    Route::get('lot/{id}/delete-confirmed', 'AdminLotController@getDeleteConfirmed');
    Route::get('lot/test', 'AdminLotController@getTest');//Test

    Route::post('lot/data-table', 'AdminLotController@getDataTable');
  });
  Route::group(array('before' => 'auth.has_access:manage_condition'), function() {
    Route::get('wip', 'AdminWipController@getIndex');
    Route::get('wip/create', 'AdminWipController@getCreate');
    Route::get('wip/{id}/update', 'AdminWipController@getUpdate');
    Route::get('wip/{id}/delete', 'AdminWipController@getDelete');
    Route::get('wip/test', 'AdminWipController@getTest');//Test
    Route::get('wip/product-select', 'AdminWipController@getProductSelect');//Select product
    Route::get('wip/{id}/processes', 'AdminWipController@getProcesses');
    Route::get('wip/{id}/processes-detach/{process_id}', 'AdminWipController@detachProcess');

    Route::post('wip/create', 'AdminWipController@postCreate');
    Route::post('wip/{id}/update', 'AdminWipController@postUpdate');
    Route::post('wip/{id}/delete', 'AdminWipController@postDelete');
    Route::post('wip/{id}/processes', 'AdminWipController@postProcesses');

    Route::post('wip/data-table', 'AdminWipController@getDataTable');
  });
  Route::group(array('before' => 'auth.has_access:manage_ng'), function() {
    Route::get('ng-detail', 'AdminNgDetailController@getIndex');
    Route::get('ng-detail/create', 'AdminNgDetailController@getCreate');
    Route::get('ng-detail/{id}/update', 'AdminNgDetailController@getUpdate');
    Route::get('ng-detail/{id}/delete', 'AdminNgDetailController@getDelete');
    Route::get('ng-detail/import', 'AdminNgDetailController@getImport');
    Route::get('ng-detail/export', 'AdminNgDetailController@getExport');
    Route::get('ng-detail/delete-multi', 'AdminNgDetailController@getDeleteMulti');
    Route::get('ng-detail/delete-multi-confirmed', 'AdminNgDetailController@getDeleteMultiConfirmed');

    Route::post('ng-detail/create', 'AdminNgDetailController@postCreate');
    Route::post('ng-detail/{id}/update', 'AdminNgDetailController@postUpdate');
    Route::post('ng-detail/{id}/delete', 'AdminNgDetailController@postDelete');
    Route::post('ng-detail/import', 'AdminNgDetailController@postImport');

    Route::post('ng-detail/data-table', 'AdminNgDetailController@getDataTable');
  });
  Route::group(array('before' => 'auth.has_access:manage_break'), function() {
    Route::get('break', 'AdminBreakReasonController@getIndex');
    Route::get('break/create', 'AdminBreakReasonController@getCreate');
    Route::get('break/{id}/update', 'AdminBreakReasonController@getUpdate');
    Route::get('break/{id}/delete', 'AdminBreakReasonController@getDelete');
    Route::get('break/import', 'AdminBreakReasonController@getImport');
    Route::get('break/export', 'AdminBreakReasonController@getExport');
    Route::get('break/delete-multi', 'AdminBreakReasonController@getDeleteMulti');
    Route::get('break/delete-multi-confirmed', 'AdminBreakReasonController@getDeleteMultiConfirmed');

    Route::post('break/create', 'AdminBreakReasonController@postCreate');
    Route::post('break/{id}/update', 'AdminBreakReasonController@postUpdate');
    Route::post('break/{id}/delete', 'AdminBreakReasonController@postDelete');
    Route::post('break/import', 'AdminBreakReasonController@postImport');

    Route::post('break/data-table', 'AdminBreakReasonController@getDataTable');
  });
  Route::group(array('before' => 'auth.has_access:manage_process_log'), function() {
    Route::get('process-log', 'AdminProcessLogController@getIndex');
    Route::get('process-log/{id}/detail', 'AdminProcessLogController@getDetail');
    Route::get('process-log/export', 'AdminProcessLogController@getExport');
    Route::get('process-log/export-selected', 'AdminProcessLogController@getExportSelected');
    Route::get('process-log/export-break', 'AdminProcessLogController@getExportBreak');
    Route::get('process-log/export-ng', 'AdminProcessLogController@getExportNg');
    Route::get('process-log/export-input', 'AdminProcessLogController@getExportInput');
    Route::get('process-log/test', 'AdminProcessLogController@getTest');

    Route::post('process-log/data-table', 'AdminProcessLogController@getDataTable');
    //phrase 2.1
    Route::get('import-price', 'AdminPhrase2Controller@importPrice');
    Route::post('import-price', 'AdminPhrase2Controller@postImportPrice');
    Route::post('import-price-table', 'AdminPhrase2Controller@getDataPrice');

    Route::get('import-target', 'AdminPhrase2Controller@importTarget');
    Route::post('import-target', 'AdminPhrase2Controller@postImportTarget');
    Route::post('import-target-table', 'AdminPhrase2Controller@getDataTarget');

    Route::get('report-daily', 'AdminReportController@reportDaily');
    Route::get('report-ajax-select', 'AdminReportController@reportAjax');
  });
  Route::group(array('before' => 'auth.has_access:manage_process_work'), function() {
    Route::get('process-working', 'AdminProcessWorking@getIndex');
    Route::get('process-working/{id}/clear', 'AdminProcessWorking@getClearProcess');
    Route::get('process-working/{id}/normal-clear', 'AdminProcessWorking@getClearProcessNormal');
    Route::get('process-working/{id}/force-clear', 'AdminProcessWorking@getClearProcessForce');
    Route::post('process-working/data-table', 'AdminProcessWorking@getDataTable');
  });

  // -------------------------------------------------------------------------
  // Docutment category routes

  Route::group(array('before' => 'auth.has_access:manage_documents'), function() {

    Route::get('document-category', 'AdminDocumentCategoryController@getIndex');
    Route::get('document-category/create', 'AdminDocumentCategoryController@getCreate');
    Route::get('document-category/{id}/update', 'AdminDocumentCategoryController@getUpdate');
    Route::get('document-category/{id}/delete', 'AdminDocumentCategoryController@getDelete');
    Route::get('document-category/{id}/download', 'AdminDocumentCategoryController@getDownload');

    Route::post('document-category/create', 'AdminDocumentCategoryController@postCreate');
    Route::post('document-category/{id}/update', 'AdminDocumentCategoryController@postUpdate');
    Route::post('document-category/{id}/delete', 'AdminDocumentCategoryController@postDelete');

    Route::post('document-category/data-table', 'AdminDocumentCategoryController@getDataTable');

  });

  // -------------------------------------------------------------------------
  // Docutment routes

  Route::group(array('before' => 'auth.has_access:manage_documents'), function() {

    Route::get('document', 'AdminDocumentController@getIndex');
    Route::get('document/create', 'AdminDocumentController@getCreate');
    Route::get('document/{id}/update', 'AdminDocumentController@getUpdate');
    Route::get('document/{id}/delete', 'AdminDocumentController@getDelete');
    Route::get('document/{id}/download', 'AdminDocumentController@getDownload');

    Route::post('document/create', 'AdminDocumentController@postCreate');
    Route::post('document/{id}/update', 'AdminDocumentController@postUpdate');
    Route::post('document/{id}/delete', 'AdminDocumentController@postDelete');

    Route::post('document/data-table', 'AdminDocumentController@getDataTable');

    Route::post('document/get-model-processes', 'AdminDocumentController@getProductProcesses');
    Route::post('document/get-model-processes-pair', 'AdminDocumentController@getProductProcessesPair');

  });

  // -------------------------------------------------------------------------
  // Activity routes

  Route::group(array('before' => 'auth.has_access:manage_activities'), function() {

    Route::get('activity', 'AdminActivityController@getIndex');
    Route::get('activity/lab', 'AdminActivityController@getLab');

    Route::post('activity/data-table', 'AdminActivityController@getDataTable');

  });

});

// -----------------------------------------------------------------------------
// API routes

Route::group(array('prefix' => 'api'), function() {

  // ---------------------------------------------------------------------------
  // Unprotected routes

  Route::group(array(), function() {

    // ---------------------------------------------------------------------------
    // User routes

    Route::post('user/login', 'ApiUserController@login');

  });

  // -------------------------------------------------------------------------

  Route::group(array('before' => 'auth.api.has_access:work_on_model_processes'), function() {

    // Process routes
    Route::post('process/check-status', 'ApiProcessController@checkStatus');

    // Activity routes
    //Route::post('activity/process/start', 'ApiActivityController@processStart');
    //Route::post('activity/process/end', 'ApiActivityController@processEnd');

    //----Phase 2
    //Line leader
    Route::get('user/line-leader', 'ApiUserController@getLineLeader');
    Route::get('process/break-list', 'ApiProcessLogController@breakList');
    Route::get('process/ng-list', 'ApiProcessLogController@ngList');
    Route::get('process/process-clear', 'ApiProcessLogController@processClear');

    //Process log
    Route::post('process/process-start', 'ApiProcessLogController@processStart');//cancel on phase2
    // Route::post('process/process-finish', 'ApiProcessLogController@processFinish');
    Route::post('process/process-break', 'ApiProcessLogController@processBreak');//cancel on phase2
    // Route::post('process/model-data', 'ApiProcessLogController@modelData');
    Route::post('process/request-part', 'ApiProcessLogController@requestPartList');
    Route::post('process/keep-first-serial', 'ApiProcessLogController@keepFirstSerial');
    Route::post('process/recover-work-status', 'ApiProcessLogController@recoverWorkingStatus');
    Route::post('process/check-iqc-lot', 'ApiProcessLogController@checkIqcLot');
    Route::post('process/check-wip-lot', 'ApiProcessLogController@checkWipLot');
    // Route::post('process/check-input-lot', 'ApiProcessLogController@checkInputLot');

    //--Phase 2.1 Aug 2017
    Route::get('process/get-shift-code', 'ApiPhase2Controller@getShiftCode');
    Route::post('process/model-data', 'ApiPhase2Controller@modelData');//replaced
    Route::post('process/process-finish', 'ApiPhase2Controller@processFinish');//replaced
    Route::post('process/check-input-lot', 'ApiPhase2Controller@checkInputLot');//replaced
  });

  // -------------------------------------------------------------------------

  Route::group(array(), function() {

    // Document category routes
    Route::get('document-category', 'ApiDocumentCategoryController@index')
         ->before('auth.api.has_access:view_documents');

    // Document routes
    Route::get('document/{id}/download', 'AdminDocumentController@getDownload')
         ->before('auth.api.has_access:view_documents');

  });

});
