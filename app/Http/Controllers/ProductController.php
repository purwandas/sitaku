<?php

namespace App\Http\Controllers;

use App\Components\Filters\ProductFilter;
use App\Components\Helpers\FormBuilderHelper;
use App\Components\Traits\ApiController;
use App\Exports\ProductExportPdf;
use App\Exports\ProductExportXls;
use App\Imports\ProductImport;
use App\Templates\ProductImportSheetTemplate;
use \App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    use ApiController;

    public $type, $label = "Product";

    public function index()
    {
        $data = [
            'title' => $this->label,
            'icon'  => 'fas fa-file-medical ',
            'breadcrumb' => [
                ['label' => $this->label],
            ]
        ];

        $form_data = new FormBuilderHelper(Product::class,$data);
        $final     = $form_data->get();
        
        return view('components.global_form', $final);
    }

    public function list(ProductFilter $filter)
    {
        $product = Product::join('categories', 'categories.id', 'products.category_id')
			->join('productions', 'productions.id', 'products.production_id')
			->select('products.*', 'categories.name as category_name', 'productions.name as production_name')
			->filter($filter)->get();
        return $this->sendResponse($product, 'Get Data Success!');
    }

    public function select2(ProductFilter $filter)
    {
        return Product::join('categories', 'categories.id', 'products.category_id')
			->join('productions', 'productions.id', 'products.production_id')
			->select('products.*', 'categories.name as category_name', 'productions.name as production_name')
			->filter($filter)->get();
    }

    public function datatable(ProductFilter $filter)
    {
        $data = Product::join('categories', 'categories.id', 'products.category_id')
			->join('productions', 'productions.id', 'products.production_id')
			->select('products.*', 'categories.name as category_name', 'productions.name as production_name')
			->filter($filter);

        return \DataTables::of($data)
            ->addColumn('action', function ($data){
                return "<button onclick=\"editModalProduct('".route('product.edit',['id'=>$data->id])."')\" class='btn btn-sm btn-primary btn-square' data-target='#modalFormProduct' data-toggle='modal'><i class='fas fa-pencil-alt'></i></button>
                <button data-url=".route('product.delete',['id'=>$data->id])." class='btn btn-sm btn-danger btn-square js-swal-delete'><i class='fas fa-trash-alt'></i></button>";
            })
            ->make(true);
    }

    public function store(Request $request)
    {
        try{
            $product = DB::transaction(function () use ($request) {
                $product = new Product;
                $product->fillAndValidate()->save();
                return $product;
            });
        }catch(\Exception $ex){
            return $this->sendError('Insert Data Error!', $ex, 500);
        }

        return $this->sendResponse($product, 'Insert Data Success!');
    }

    public function detail($id)
    {
        $product = Product::join('categories', 'categories.id', 'products.category_id')
			->join('productions', 'productions.id', 'products.production_id')
			->select('products.*', 'categories.name as category_name', 'productions.name as production_name')
			->findOrFail($id);
        return $this->sendResponse($product, 'Get Data Success!');
    }

    public function update(Request $request, $id)
    {
        try{
            $product = DB::transaction(function () use ($request, $id) {
                $product = Product::findOrFail($id);
                $product->fillAndValidate()->save();
                return $product;
            });
        }catch(\Exception $ex){
            return $this->sendError('Update Data Error!', $ex, 500);
        }

        return $this->sendResponse($product, 'Update Data Success!');
    }

    public function destroy($id)
    {
        try{
            DB::transaction(function () use ($id) {
                $product = Product::findOrFail($id);
                $product->delete();
            });
        }catch(\Exception $ex){
            return $this->sendError('Delete Data Error!', $ex, 500);
        }

        return $this->sendResponse([], 'Delete Data Success!');
    }

    public function exportXls(Request $request)
    {
        processing_jobs([
            'title'   => 'Download '.$this->label,
            'filters' => $request->all(),
            'model'   => ProductExportXls::class,
            'module'  => $this->label,
            'path'    => 'exports/product/xlsx',
            'ext'     => 'xlsx',
        ]);
        
        return $this->sendResponse([], 'Download '.$this->label.' has been processed.');

    }

    public function exportPdf(Request $request)
    {
        processing_jobs([
            'title'   => 'Download '.$this->label,
            'filters' => $request->all(),
            'model'   => ProductExportPdf::class,
            'module'  => $this->label,
            'path'    => 'exports/product/pdf',
        ]);
        
        return $this->sendResponse([], 'Download '.$this->label.' has been processed.');
    }

    public function import(Request $request)
    {
        processing_jobs([
            'title'  => 'Upload '.$this->label,
            'model'  => ProductImport::class,
            'module' => $this->label,
            'path'   => 'imports/product',
        ]);
        
        return $this->sendResponse([], 'Upload '.$this->label.' has been processed.');
    }

    public function importTemplate()
    {
        return Excel::download(new ProductImportSheetTemplate, 'Template For Import '.$this->label.' Data.xlsx');
    }
}
