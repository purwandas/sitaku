<?php

use Illuminate\Support\Facades\Route;

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

Auth::routes();

Route::get('/home', function() {
    return view('home');
})->name('home')->middleware('auth');
Route::get('/', function() {
    return view('home');
})->middleware('auth')->name('dashboard.index');

Route::group(['middleware' => ['auth']],function(){

	Route::group(['prefix' => 'dashboard','middleware' => ['auth']], function() {
		Route::get('data', 'DashboardController@getdata')->name('dashboard.data');
	});

	// Route::get('/available-input', 'HomeController@index')->name('available-input.index');

	Route::get('purchase-form/{id?}', 'PurchaseController@form')->name('purchase-form.index');
	Route::get('sales-form/{id?}', 'SalesController@form')->name('sales-form.index');

	Route::group(['prefix' => 'purchase','middleware' => ['auth']], function() {
		Route::get('', 'PurchaseController@index')->name('purchase.index');
		Route::get('import-template', 'PurchaseController@importTemplate')->name('purchase.import-template');
	});

	Route::group(['prefix' => 'sales','middleware' => ['auth']], function() {
		Route::get('', 'SalesController@index')->name('sales.index');
		Route::get('import-template', 'SalesController@importTemplate')->name('sales.import-template');
	});

	Route::group(['prefix' => 'trend-moment','middleware' => ['auth']], function() {
		Route::get('/', 'TrendMomentController@index')->name('trend-moment.index');
		Route::get('/go/{product?}/{month?}', 'TrendMomentController@index')->name('trend-moment.go');
		Route::get('/calc', 'TrendMomentController@calculation')->name('trend-moment.calc');
		Route::get('import-template', 'TrendMomentController@importTemplate')->name('trend-moment.import-template');
	});

	Route::group(['prefix' => 'role','middleware' => ['auth']], function() {
		Route::get('', 'RoleController@index')->name('role.index');
		Route::get('import-template', 'RoleController@importTemplate')->name('role.import-template');
	});

	Route::group(['prefix' => 'user','middleware' => ['auth']], function() {
		Route::get('', 'UserController@index')->name('user.index');
		Route::get('import-template', 'UserController@importTemplate')->name('user.import-template');
	});

	Route::group(['prefix' => 'category','middleware' => ['auth']], function() {
		Route::get('', 'CategoryController@index')->name('category.index');
		Route::get('import-template', 'CategoryController@importTemplate')->name('category.import-template');
	});

	Route::group(['prefix' => 'production','middleware' => ['auth']], function() {
		Route::get('', 'ProductionController@index')->name('production.index');
		Route::get('import-template', 'ProductionController@importTemplate')->name('production.import-template');
	});

	Route::group(['prefix' => 'supplier','middleware' => ['auth']], function() {
		Route::get('', 'SupplierController@index')->name('supplier.index');
		Route::get('import-template', 'SupplierController@importTemplate')->name('supplier.import-template');
	});

	Route::group(['prefix' => 'unit','middleware' => ['auth']], function() {
		Route::get('', 'UnitController@index')->name('unit.index');
		Route::get('import-template', 'UnitController@importTemplate')->name('unit.import-template');
	});

	Route::group(['prefix' => 'product','middleware' => ['auth']], function() {
		Route::get('', 'ProductController@index')->name('product.index');
		Route::get('import-template', 'ProductController@importTemplate')->name('product.import-template');
	});

	Route::group(['prefix' => 'product-unit','middleware' => ['auth']], function() {
		Route::get('', 'ProductUnitController@index')->name('product-unit.index');
		Route::get('import-template', 'ProductUnitController@importTemplate')->name('product-unit.import-template');
	});

});