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