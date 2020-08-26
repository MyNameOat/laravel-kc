<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

    Route::get('/', function () {
        return view('welcome');
    });

    Auth::routes(['register' => false]);

    Route::get('/select-warehouse', 'HomeController@selectWarehouse')->name('select-warehouse');
    Route::post('/select-warehouse', 'HomeController@storeWarehouse')->name('store-warehouse');

    Route::get('/home', 'HomeController@index')->name('home');

    Route::get('/calculateMemberPointBenefit', 'MemberPointBenefitController@calculateMemberPointBenefit');
    Route::get('/calculateMemberPointBenefit', 'MemberPointBenefitController@calculatePointBenefitHSDoor');


    Route::middleware(['auth', 'warehouse'])->group(function () {
    Route::get('/select-program', 'HomeController@selectProgram')->name('select-program');
    Route::get('/stock-table', 'HomeController@getStockTable')->name('stock-table');
    Route::get('/red-label-table', 'HomeController@getRedLabelTable')->name('red-label-table');
    Route::get('/good-table', 'HomeController@getGoodTable')->name('good-table');

    Route::prefix('board')->name('board.')->group(function () {
        Route::get('/dashboard', 'Board\DashboardController@dashboard')->name('dashboard');
        Route::get('/summary-sale/current-day', 'Board\DashboardController@summarySaleCurrentDay')->name('summary-sale.current-day');
    });

    Route::prefix('inv')->name('inv.')->group(function () {

        Route::get('/dashboard', 'Inv\RequisitionController@dashboard')->name('dashboard');
        Route::get('/requisition', 'Inv\RequisitionController@index')->name('index');
        Route::get('/requisition/store-detail/id/{id_bill}', 'Inv\RequisitionController@reportStoreDetail')->name('report-store-detail');

        Route::post('/requisition/search-goods', 'Inv\RequisitionController@searchGoods')->name('search-goods');
        Route::get('/requisition/form-create', 'Inv\RequisitionController@formCreate')->name('form-create');
        Route::post('/requisition/save-store', 'Inv\RequisitionController@saveStore')->name('save-store');
        Route::get('/requisition/form-edit/id/{id_bill}', 'Inv\RequisitionController@formEdit')->name('form-edit');
        Route::post('/requisition/edit-store', 'Inv\RequisitionController@updateStore')->name('update-store');
        Route::get('/requisition/delete-store/id/{id_bill}', 'Inv\RequisitionController@deleteStore')->name('delete-store');

        Route::get('/requisition/approve', 'Inv\RequisitionController@approve')->name('approve');
        Route::get('/requisition/approve-detail/id/{id_bill}', 'Inv\RequisitionController@approveDetail')->name('approve-detail');
        Route::get('/requisition/approve/check-status/id/{id_bill}/no', 'Inv\RequisitionController@approveCheckStatusNo')->name('approve-check-status-no');
        Route::get('/requisition/approve/check-status/id/{id_bill}/off', 'Inv\RequisitionController@approveCheckStatusOff')->name('approve-check-status-off');

        Route::get('/requisition/report-status/config', 'Inv\RequisitionController@reportRequisitions')->name('report-status-config');
        Route::post('/requisition/report-status/delete/{id}', 'Inv\RequisitionController@reportStatusDelete')->name('report-status-delete');
    });
});
