<?php

namespace App\Http\Controllers;

use App\Product;
use App\Sales;
use App\Supplier;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function getData()
    {
    	$product = Product::get();
    	$supplier = Supplier::get();
    	$sales = Sales::get();
    	$result = [
    		'total_product' => $product->count(),
    		'total_product_stock' => $product->sum('stock'),
    		'total_supplier' => $supplier->count(),
    		'total_daily_income' => number_format($sales->where('date',Carbon::now()->format('Y-m-d'))->sum('total_payment'),0,',','.'),
    		'total_all_income' => number_format($sales->sum('total_payment'),0,',','.'),
    	];

    	return response()->json($result);
    }

    public function getDatatable(Request $request)
    {
    	$data = Product::join('categories','products.category_id','categories.id')
    					->select('categories.name as category_name','products.name as product_name','stock')
    					->get();

    	return \DataTables::of($data)->make(true);
    }
}
