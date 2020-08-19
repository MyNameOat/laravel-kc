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

        Route::get('/dashboard', 'Inv\RequisitionBillsController@dashboard')->name('dashboard');
        Route::get('/requisition', 'Inv\RequisitionBillsController@index')->name('index');
        Route::get('/requisition/store-detail/id/{id_bill}', 'Inv\RequisitionBillsController@reportStoreDetail')->name('report-store-detail');


        Route::post('/requisition/search-goods', 'Inv\RequisitionBillsController@searchGoods')->name('search-goods');
        Route::get('/requisition/form-create', 'Inv\RequisitionBillsController@formCreate')->name('form-create');
        Route::post('/requisition/save-store', 'Inv\RequisitionBillsController@saveStore')->name('save-store');
        Route::get('/requisition/form-edit/id/{id_bill}', 'Inv\RequisitionBillsController@formEdit')->name('form-edit');
        Route::post('/requisition/edit-store', 'Inv\RequisitionBillsController@updateStore')->name('update-store');
        Route::get('/requisition/delete-store/id/{id_bill}', 'Inv\RequisitionBillsController@deleteStore')->name('delete-store');


        Route::get('/requisition/approve', 'Inv\RequisitionBillsController@approve')->name('approve');
        Route::get('/requisition/approve-detail/id/{id_bill}', 'Inv\RequisitionBillsController@approveDetail')->name('approve-detail');
        Route::get('/requisition/approve/check-status/id/{id_bill}/no', 'Inv\RequisitionBillsController@approveCheckStatusNo')->name('approve-check-status-no');
        Route::get('/requisition/approve/check-status/id/{id_bill}/off', 'Inv\RequisitionBillsController@approveCheckStatusOff')->name('approve-check-status-off');

        Route::get('/requisition/report-status/config', 'Inv\RequisitionBillsController@reportRequisitions')->name('report-status-config');

    });
});
