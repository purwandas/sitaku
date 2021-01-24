<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'job-trace','middleware' => ['auth:api']], function() {
	Route::post('datatable', 'UtilController@datatable')->name('job-trace.data');
});

Route::group(['prefix' => 'role','middleware' => ['auth:api']], function() {
	Route::get('', 'RoleController@list')->name('role.list');
	Route::post('', 'RoleController@store')->name('role.create');
	Route::get('{id}', 'RoleController@detail')->name('role.detail')->where('id', '[0-9]+');
	Route::put('{id}', 'RoleController@update')->name('role.edit')->where('id', '[0-9]+');
	Route::delete('{id}', 'RoleController@destroy')->name('role.delete')->where('id', '[0-9]+');
	Route::post('datatable', 'RoleController@datatable')->name('role.datatable');
	Route::post('export-xls', 'RoleController@exportXls')->name('role.export-xls');
	Route::post('export-pdf', 'RoleController@exportPdf')->name('role.export-pdf');
	Route::post('import', 'RoleController@import')->name('role.import');
	Route::post('select2', 'RoleController@select2')->name('role.select2');
});

Route::group(['prefix' => 'user','middleware' => ['auth:api']], function() {
	Route::get('', 'UserController@list')->name('user.list');
	Route::post('', 'UserController@store')->name('user.create');
	Route::get('{id}', 'UserController@detail')->name('user.detail')->where('id', '[0-9]+');
	Route::put('{id}', 'UserController@update')->name('user.edit')->where('id', '[0-9]+');
	Route::delete('{id}', 'UserController@destroy')->name('user.delete')->where('id', '[0-9]+');
	Route::post('datatable', 'UserController@datatable')->name('user.datatable');
	Route::post('export-xls', 'UserController@exportXls')->name('user.export-xls');
	Route::post('export-pdf', 'UserController@exportPdf')->name('user.export-pdf');
	Route::post('import', 'UserController@import')->name('user.import');
	Route::post('select2', 'UserController@select2')->name('user.select2');
});

Route::group(['prefix' => 'category','middleware' => ['auth:api']], function() {
	Route::get('', 'CategoryController@list')->name('category.list');
	Route::post('', 'CategoryController@store')->name('category.create');
	Route::get('{id}', 'CategoryController@detail')->name('category.detail')->where('id', '[0-9]+');
	Route::put('{id}', 'CategoryController@update')->name('category.edit')->where('id', '[0-9]+');
	Route::delete('{id}', 'CategoryController@destroy')->name('category.delete')->where('id', '[0-9]+');
	Route::post('datatable', 'CategoryController@datatable')->name('category.datatable');
	Route::post('export-xls', 'CategoryController@exportXls')->name('category.export-xls');
	Route::post('export-pdf', 'CategoryController@exportPdf')->name('category.export-pdf');
	Route::post('import', 'CategoryController@import')->name('category.import');
	Route::post('select2', 'CategoryController@select2')->name('category.select2');
});

Route::group(['prefix' => 'production','middleware' => ['auth:api']], function() {
	Route::get('', 'ProductionController@list')->name('production.list');
	Route::post('', 'ProductionController@store')->name('production.create');
	Route::get('{id}', 'ProductionController@detail')->name('production.detail')->where('id', '[0-9]+');
	Route::put('{id}', 'ProductionController@update')->name('production.edit')->where('id', '[0-9]+');
	Route::delete('{id}', 'ProductionController@destroy')->name('production.delete')->where('id', '[0-9]+');
	Route::post('datatable', 'ProductionController@datatable')->name('production.datatable');
	Route::post('export-xls', 'ProductionController@exportXls')->name('production.export-xls');
	Route::post('export-pdf', 'ProductionController@exportPdf')->name('production.export-pdf');
	Route::post('import', 'ProductionController@import')->name('production.import');
	Route::post('select2', 'ProductionController@select2')->name('production.select2');
});

Route::group(['prefix' => 'product','middleware' => ['auth:api']], function() {
	Route::get('', 'ProductController@list')->name('product.list');
	Route::post('', 'ProductController@store')->name('product.create');
	Route::get('{id}', 'ProductController@detail')->name('product.detail')->where('id', '[0-9]+');
	Route::put('{id}', 'ProductController@update')->name('product.edit')->where('id', '[0-9]+');
	Route::delete('{id}', 'ProductController@destroy')->name('product.delete')->where('id', '[0-9]+');
	Route::post('datatable', 'ProductController@datatable')->name('product.datatable');
	Route::post('export-xls', 'ProductController@exportXls')->name('product.export-xls');
	Route::post('export-pdf', 'ProductController@exportPdf')->name('product.export-pdf');
	Route::post('import', 'ProductController@import')->name('product.import');
	Route::post('select2', 'ProductController@select2')->name('product.select2');
	Route::post('get-data-product', 'ProductController@getDataProduct')->name('product.get-data-product');

});

Route::group(['prefix' => 'supplier','middleware' => ['auth:api']], function() {
	Route::get('', 'SupplierController@list')->name('supplier.list');
	Route::post('', 'SupplierController@store')->name('supplier.create');
	Route::get('{id}', 'SupplierController@detail')->name('supplier.detail')->where('id', '[0-9]+');
	Route::put('{id}', 'SupplierController@update')->name('supplier.edit')->where('id', '[0-9]+');
	Route::delete('{id}', 'SupplierController@destroy')->name('supplier.delete')->where('id', '[0-9]+');
	Route::post('datatable', 'SupplierController@datatable')->name('supplier.datatable');
	Route::post('export-xls', 'SupplierController@exportXls')->name('supplier.export-xls');
	Route::post('export-pdf', 'SupplierController@exportPdf')->name('supplier.export-pdf');
	Route::post('import', 'SupplierController@import')->name('supplier.import');
	Route::post('select2', 'SupplierController@select2')->name('supplier.select2');
});

Route::group(['prefix' => 'unit','middleware' => ['auth:api']], function() {
	Route::get('', 'UnitController@list')->name('unit.list');
	Route::post('', 'UnitController@store')->name('unit.create');
	Route::get('{id}', 'UnitController@detail')->name('unit.detail')->where('id', '[0-9]+');
	Route::put('{id}', 'UnitController@update')->name('unit.edit')->where('id', '[0-9]+');
	Route::delete('{id}', 'UnitController@destroy')->name('unit.delete')->where('id', '[0-9]+');
	Route::post('datatable', 'UnitController@datatable')->name('unit.datatable');
	Route::post('export-xls', 'UnitController@exportXls')->name('unit.export-xls');
	Route::post('export-pdf', 'UnitController@exportPdf')->name('unit.export-pdf');
	Route::post('import', 'UnitController@import')->name('unit.import');
	Route::post('select2', 'UnitController@select2')->name('unit.select2');
});

Route::group(['prefix' => 'sales','middleware' => ['auth:api']], function() {
	Route::get('', 'SalesController@list')->name('sales.list');
	Route::post('', 'SalesController@store')->name('sales.create');
	Route::get('{id}', 'SalesController@detail')->name('sales.detail')->where('id', '[0-9]+');
	Route::put('{id}', 'SalesController@update')->name('sales.edit')->where('id', '[0-9]+');
	Route::delete('{id}', 'SalesController@destroy')->name('sales.delete')->where('id', '[0-9]+');
	Route::post('datatable', 'SalesController@datatable')->name('sales.datatable');
	Route::post('datatable-detail/{id?}', 'SalesController@datatableDetail')->name('sales.datatable-detail');
	Route::post('export-xls', 'SalesController@exportXls')->name('sales.export-xls');
	Route::post('export-pdf', 'SalesController@exportPdf')->name('sales.export-pdf');
	Route::post('import', 'SalesController@import')->name('sales.import');
	Route::post('select2', 'SalesController@select2')->name('sales.select2');
});

Route::group(['prefix' => 'product-unit','middleware' => ['auth:api']], function() {
	Route::get('', 'ProductUnitController@list')->name('product-unit.list');
	Route::post('', 'ProductUnitController@store')->name('product-unit.create');
	Route::post('get-price/{productId?}/{unitId?}', 'ProductUnitController@getPrice')->name('product-unit.get-price');
	Route::get('{id}', 'ProductUnitController@detail')->name('product-unit.detail')->where('id', '[0-9]+');
	Route::put('{id}', 'ProductUnitController@update')->name('product-unit.edit')->where('id', '[0-9]+');
	Route::delete('{id}', 'ProductUnitController@destroy')->name('product-unit.delete')->where('id', '[0-9]+');
	Route::post('datatable', 'ProductUnitController@datatable')->name('product-unit.datatable');
	Route::post('export-xls', 'ProductUnitController@exportXls')->name('product-unit.export-xls');
	Route::post('export-pdf', 'ProductUnitController@exportPdf')->name('product-unit.export-pdf');
	Route::post('import', 'ProductUnitController@import')->name('product-unit.import');
	Route::post('select2', 'ProductUnitController@select2')->name('product-unit.select2');
});

Route::group(['prefix' => 'product','middleware' => ['auth:api']], function() {
	Route::get('', 'ProductController@list')->name('product.list');
	Route::post('', 'ProductController@store')->name('product.create');
	Route::get('{id}', 'ProductController@detail')->name('product.detail')->where('id', '[0-9]+');
	Route::put('{id}', 'ProductController@update')->name('product.edit')->where('id', '[0-9]+');
	Route::delete('{id}', 'ProductController@destroy')->name('product.delete')->where('id', '[0-9]+');
	Route::post('datatable', 'ProductController@datatable')->name('product.datatable');
	Route::post('export-xls', 'ProductController@exportXls')->name('product.export-xls');
	Route::post('export-pdf', 'ProductController@exportPdf')->name('product.export-pdf');
	Route::post('import', 'ProductController@import')->name('product.import');
	Route::post('select2', 'ProductController@select2')->name('product.select2');
});

Route::group(['prefix' => 'purchase','middleware' => ['auth:api']], function() {
	Route::get('', 'PurchaseController@list')->name('purchase.list');
	Route::post('', 'PurchaseController@store')->name('purchase.create');
	Route::get('{id}', 'PurchaseController@detail')->name('purchase.detail')->where('id', '[0-9]+');
	Route::put('{id}', 'PurchaseController@update')->name('purchase.edit')->where('id', '[0-9]+');
	Route::delete('{id}', 'PurchaseController@destroy')->name('purchase.delete')->where('id', '[0-9]+');
	Route::post('datatable', 'PurchaseController@datatable')->name('purchase.datatable');
	Route::post('datatable-detail/{id?}', 'PurchaseController@datatableDetail')->name('purchase.datatable-detail');
	Route::post('export-xls', 'PurchaseController@exportXls')->name('purchase.export-xls');
	Route::post('export-pdf', 'PurchaseController@exportPdf')->name('purchase.export-pdf');
	Route::post('import', 'PurchaseController@import')->name('purchase.import');
	Route::post('select2', 'PurchaseController@select2')->name('purchase.select2');
});

Route::group(['prefix' => 'trend-moment','middleware' => ['auth:api']], function() {
	Route::get('', 'TrendMomentController@list')->name('trend-moment.list');
	Route::post('', 'TrendMomentController@store')->name('trend-moment.create');
	Route::get('{id}', 'TrendMomentController@detail')->name('trend-moment.detail')->where('id', '[0-9]+');
	Route::put('{id}', 'TrendMomentController@update')->name('trend-moment.edit')->where('id', '[0-9]+');
	Route::delete('{id}', 'TrendMomentController@destroy')->name('trend-moment.delete')->where('id', '[0-9]+');
	Route::post('datatable', 'TrendMomentController@datatable')->name('trend-moment.datatable');
	Route::post('export-xls', 'TrendMomentController@exportXls')->name('trend-moment.export-xls');
	Route::post('export-pdf', 'TrendMomentController@exportPdf')->name('trend-moment.export-pdf');
	Route::post('import', 'TrendMomentController@import')->name('trend-moment.import');
	Route::post('select2', 'TrendMomentController@select2')->name('trend-moment.select2');
});