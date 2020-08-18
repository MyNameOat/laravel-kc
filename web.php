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

    Route::prefix('whs')->name('whs.')->group(function () {
        Route::get('/dashboard', 'Whs\DashboardController@dashboard')->name('dashboard');

        //Pr
        Route::get('/prs-approve', 'Whs\PrController@approveIndex')->name('prs-approve.index');
        Route::get('/prs-approve/{id}', 'Whs\PrController@approveShow')->name('prs-approve.show');
        Route::put('/prs-approve/{id}/update', 'Whs\PrController@approveUpdate')->name('prs-approve.update');
        Route::get('/prs-report', 'Whs\PrController@reportIndex')->name('prs-report.index');
        Route::get('/prs-report/{id}', 'Whs\PrController@reportShow')->name('prs-report.show');
        Route::resource('prs', 'Whs\PrController');

        //WithdrawRedLabel
        Route::get('/withdraw-red-label/approve', 'Whs\WithDrawRedLabelController@approveIndex')->name('withdraw-red-label.approve.index');
        Route::get('/withdraw-red-label/approve/{id}', 'Whs\WithDrawRedLabelController@approveShow')->name('withdraw-red-label.approve.show');
        Route::post('/withdraw-red-label/approve/{id}', 'Whs\WithDrawRedLabelController@approveStore')->name('withdraw-red-label.approve.store');
        Route::resource('withdraw-red-label', 'Whs\WithDrawRedLabelController');

        //Requisition
        Route::get('/requisitions/approve', 'Whs\RequisitionController@approveIndex')->name('requisitions.approve.index');
        Route::resource('requisitions', 'Whs\RequisitionController');
    });

    Route::prefix('whs-center')->name('whs-center.')->group(function () {
        Route::get('/dashboard', 'WhsCenter\DashboardController@dashboard')->name('dashboard');
        Route::get('/pos-select-pr', 'WhsCenter\Pocontroller@selectPr')->name('pos.select-pr');
        Route::get('/pos-select-vendor', 'WhsCenter\Pocontroller@selectVendor')->name('pos.select-vendor');
        Route::delete('/pos-cancel-pr/{id}', 'WhsCenter\PoController@cancelPr')->name('pos.cancel-pr');
        Route::delete('/pos-clear-pr/{id}', 'WhsCenter\PoController@clearPr')->name('pos.clear-pr');
        Route::resource('pos', 'WhsCenter\PoController');
        Route::resource('vendors', 'WhsCenter\VendorController');

        Route::get('/goods/set-check-goods', 'WhsCenter\SetCheckGoodController@index')->name('goods.set-check-goods.index');
        Route::post('/goods/set-check-goods', 'WhsCenter\SetCheckGoodController@store')->name('goods.set-check-goods.store');
        Route::get('/goods/{id}/set-check-goods', 'WhsCenter\SetCheckGoodController@show')->name('goods.set-check-goods.show');
        Route::post('/goods/{id}/set-check-goods', 'WhsCenter\SetCheckGoodController@setMinMax')->name('goods.set-check-goods.setMinMax');

        Route::get('/goods/set-ratio-goods', 'WhsCenter\SetRatioGoodController@index')->name('goods.set-ratio-goods.index');
        Route::post('/goods/set-ratio-goods/showGoodRatio', 'WhsCenter\SetRatioGoodController@showGoodRatio');
        Route::post('/goods/set-ratio-goods/showBaseRatio', 'WhsCenter\SetRatioGoodController@showBaseRatio');
        Route::post('/goods/set-ratio-goods/storeBaseRatio', 'WhsCenter\SetRatioGoodController@storeBaseRatio');
        Route::post('/goods/set-ratio-goods/showGoodModal', 'WhsCenter\SetRatioGoodController@showGoodModal');
        Route::post('/goods/set-ratio-goods/storeGoodRatio', 'WhsCenter\SetRatioGoodController@storeGoodRatio');
        Route::post('/goods/set-ratio-goods/deleteGoodRatio', 'WhsCenter\SetRatioGoodController@deleteGoodRatio');
        Route::post('/goods/set-ratio-goods/checkOutGood', 'WhsCenter\SetRatioGoodController@checkOutGood')->name('goods.set-ratio-goods.checkOutGood');
        Route::post('/goods/set-ratio-goods/showGoodDetailPoint', 'WhsCenter\SetRatioGoodController@showGoodDetailPoint');
        Route::post('/goods/set-ratio-goods/storeGoodDetailPoint', 'WhsCenter\SetRatioGoodController@storeGoodDetailPoint')->name('goods.set-ratio-goods.storeGoodDetailPoint');
        Route::post('/goods/set-ratio-goods/showMemberType', 'WhsCenter\SetRatioGoodController@showMemberType');
        Route::post('/goods/set-ratio-goods/setBaseRatioMemType', 'WhsCenter\SetRatioGoodController@setBaseRatioMemType');


        Route::get('/goods/set-price-goods', 'WhsCenter\SetPriceGoodController@index')->name('goods.set-price-goods.index');
        Route::post('/goods/set-price-goods/showGoodPrice', 'WhsCenter\SetPriceGoodController@showGoodPrice');
        Route::post('/goods/set-price-goods/showGoodModal', 'WhsCenter\SetPriceGoodController@showGoodModal');
        Route::post('/goods/set-price-goods/checkOutGood', 'WhsCenter\SetPriceGoodController@checkOutGood')->name('goods.set-price-goods.checkOutGood');
        Route::get('/goods/set-price-goods/{good_id}', 'WhsCenter\SetPriceGoodController@showWarehouseByGood')->name('goods.set-price-goods.showWarehouseByGood');
        Route::post('/goods/set-price-goods/deleteGood', 'WhsCenter\SetPriceGoodController@deleteGood');
        Route::post('/goods/set-price-goods/{good_id}/setBasePrice', 'WhsCenter\SetPriceGoodController@setBasePrice')->name('goods.set-price-goods.setBasePrice');
        Route::post('/goods/set-price-goods/{good_id}/infoGood', 'WhsCenter\SetPriceGoodController@infoGood');
        Route::post('/goods/set-price-goods/{good_id}/setPrice', 'WhsCenter\SetPriceGoodController@setPrice');
        Route::post('/goods/set-price-goods/storeGoodDetailBenefit', 'WhsCenter\SetPriceGoodController@storeGoodDetailBenefit')->name('goods.set-price-goods.storeGoodDetailBenefit');
        Route::post('/goods/set-price-goods/showGoodDetailBenefit', 'WhsCenter\SetPriceGoodController@showGoodDetailBenefit');
        Route::post('/goods/set-price-goods/deleteGoodDetailBenefit', 'WhsCenter\SetPriceGoodController@deleteGoodDetailBenefit');
        Route::get('/goods/set-price-goods/good-benefit/{good_benefit_id}', 'WhsCenter\SetPriceGoodController@showWarehouseByGoodDetailBenefit')->name('goods.set-price-goods.showWarehouse');
        Route::post('/goods/set-price-goods/good-benefit/{good_benefit_id}/setGoodDetailBenefitBasePrice', 'WhsCenter\SetPriceGoodController@setGoodDetailBenefitBasePrice')->name('goods.set-price-goods.setGoodDetailBenefitBasePrice');
        Route::post('/goods/set-price-goods/good-benefit/{good_benefit_id}/infoGoodDetailBenefit', 'WhsCenter\SetPriceGoodController@infoGoodDetailBenefit');

        Route::get('/members/set-members', 'WhsCenter\MemberController@index')->name('members.set-members.index');
        Route::post('/members/set-members/showCustomer', 'WhsCenter\MemberController@showCustomer');
        Route::post('/members/set-members/checkOutCustomer', 'WhsCenter\MemberController@checkOutCustomer')->name('members.set-members.checkOutCustomer');
        Route::post('/members/set-members/showMember', 'WhsCenter\MemberController@showMember');
        Route::post('/members/set-members/randomCode', 'WhsCenter\MemberController@randomCode');
        Route::post('/members/set-members/showWarehouse', 'WhsCenter\MemberController@showWarehouse');
        Route::post('/members/set-members/showMemberType', 'WhsCenter\MemberController@showMemberType');
        Route::post('/members/set-members/showBank', 'WhsCenter\MemberController@showBank');
        Route::post('/members/set-members/checkMember', 'WhsCenter\MemberController@checkMember');
        Route::post('/members/set-members/saveMember', 'WhsCenter\MemberController@saveMember');
        Route::post('/members/set-members/uploadAvatar', 'WhsCenter\MemberController@uploadAvatar')->name('members.set-members.uploadAvatar');
        Route::post('/members/set-members/uploadFile', 'WhsCenter\MemberController@uploadFile')->name('members.set-members.uploadFile');
        Route::post('/members/set-members/destroyMember', 'WhsCenter\MemberController@destroyMember');
        Route::get('/members/set-members/showProfile/{member_id}', 'WhsCenter\MemberController@showProfile')->name('members.set-members.showProfile');

        Route::get('/members/set-members/showExcel', 'WhsCenter\MemberController@showMemberSummaryExcel')->name('members.set-members.showExcel');
        Route::post('/members/set-members/exportExcel', 'WhsCenter\MemberController@exportMemberSummaryExcel')->name('members.set-members.exportExcel');

        //*************** New function ******************
        Route::get('/members/set-members/showProfile/{member_id}/showPointBenefitDetail', 'WhsCenter\MemberController@showPointBenefitDetail');
        Route::get('/members/set-members/showProfile/{member_id}/hs-bill-point-benefit/{h_s_id}', 'WhsCenter\MemberController@showHsBillPointBenefit');
        //*************** Newfunction *******************

        Route::get('/members/set-member-types', 'WhsCenter\MemberTypeController@index')->name('members.set-member-types.index');
        Route::post('/members/set-member-types', 'WhsCenter\MemberTypeController@getMemberType');
        Route::post('/members/set-member-types/storeMemberType', 'WhsCenter\MemberTypeController@storeMemberType');
        Route::post('/members/set-member-types/editMemberType', 'WhsCenter\MemberTypeController@editMemberType');
        Route::post('/members/set-member-types/deleteMemberType', 'WhsCenter\MemberTypeController@deleteMemberType');

        // รายงานเช็คสต๊อกสินค้าของทุกสาขา
        Route::get('/report-amount-good-warehouses', 'WhsCenter\ReportAmountGoodWarehouseController@index')->name('report-amount-good-warehouses.index');
        Route::post('/report-amount-good-warehouses/ajaxSearchGoodByType', 'WhsCenter\ReportAmountGoodWarehouseController@ajaxSearchGoodByType')->name('report-amount-good-warehouses.ajaxSearchGoodByType');
        Route::post('/report-amount-good-warehouses/ajaxSearchSelectedGoodList', 'WhsCenter\ReportAmountGoodWarehouseController@ajaxSearchSelectedGoodList')->name('report-amount-good-warehouses.ajaxSearchSelectedGoodList');
        // รายงานเช็คสต๊อกสินค้าขายได้ของทุกสาขา ประจำเดือน
        Route::get('/report-sell-good-month-warehouses', 'WhsCenter\ReportAmountGoodWarehouseController@sellMonthIndex')->name('report-sell-good-month-warehouses.index');
        Route::post('/report-sell-good-month-warehouses/ajaxSearchSelectedGoodListSellMonth', 'WhsCenter\ReportAmountGoodWarehouseController@ajaxSearchSelectedGoodListSellMonth')->name('report-sell-good-month-warehouses.ajaxSearchSelectedGoodListSellMonth');
        // รายงานเช็คสต๊อกสินค้าคงเหลือของทุกสาขา ประจำเดือน
        Route::get('/report-amount-good-month-warehouses', 'WhsCenter\ReportAmountGoodWarehouseController@amountMonthIndex')->name('report-amount-good-month-warehouses.index');
        Route::post('/report-amount-good-month-warehouses/ajaxSearchSelectedGoodListAmountMonth', 'WhsCenter\ReportAmountGoodWarehouseController@ajaxSearchSelectedGoodListAmountMonth')->name('report-amount-good-month-warehouses.ajaxSearchSelectedGoodListAmountMonth');

    });


    Route::prefix('logistics')->name('logistics.')->group(function () {

        //car
        Route::get('/callpageEditCar/{car_id}', 'Logistics\CarController@callpageedit')->name('callpageEditCar');
        Route::resource('/logistics', 'Logistics\CarController');
        Route::get('/callpageDataList', 'Logistics\CarController@callpageDataList')->name('callpageDataList');
        Route::post('/dataListCar', 'Logistics\CarController@ListDataCar');
        Route::get('/callpageEvenAdd', 'Logistics\CarController@callpageEvenAdd');
        Route::post('/EventEditCar', 'Logistics\CarController@EventEditCar')->name('EventEditCar');
        Route::post('/EventAddCar', 'Logistics\CarController@EventAddCar')->name('EventAddCar');
        Route::post('/EventDeleleCar', 'Logistics\CarController@EventDeleleCar')->name('EventDeleleCar');
        Route::get('/EventShowDataCar/{car_id}', 'Logistics\CarController@EventShowDataCar')->name('EventShowDataCar');
        Route::get('/callpageHistoryCar/{car_id}', 'Logistics\CarController@callpageHistoryCar')->name('callpageHistoryCar');
        Route::get('/callpageListLogCar/{car_id}', 'Logistics\CarController@callpageListLogCar')->name('callpageListLogCar');
        Route::post('/dataListlogcar', 'Logistics\CarController@dataListlogcar')->name('dataListlogcar');
        Route::post('/EvenAddlogcar', 'Logistics\CarController@EvenAddlogcar')->name('EvenAddlogcar');
        Route::get('/callpageEvenEditLogcar/{logcar_id}', 'Logistics\CarController@callpageEvenEditLogcar')->name('callpageEvenEditLogcar');


        //Transporttype
        Route::get('/callPageTransporttype', 'Logistics\CarController@callPageTransporttype')->name('callPageTransporttype');
        Route::post('/evenAdddataLogCar', 'Logistics\CarController@evenAdddataLogCar')->name('evenAdddataLogCar');
        Route::get('/evengetdatatypecar/{car_id}', 'Logistics\CarController@evengetdatatypecar')->name('evengetdatatypecar');

        //transferDocument
        Route::get('/callPagetransferDocument', 'Logistics\CarController@callPagetransferDocument')->name('callPagetransferDocument');
        Route::get('/callPagetransferDocumentadd', 'Logistics\CarController@callPagetransferDocumentadd')->name('callPagetransferDocumentadd');
        Route::post('/listdataemployee', 'Logistics\CarController@listdataemployee')->name('listdataemployee');
        Route::post('/addemployeeid', 'Logistics\CarController@addemployeeid')->name('addemployeeid');

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
