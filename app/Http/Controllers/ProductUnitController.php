<?php

namespace App\Http\Controllers;

use App\Components\Filters\ProductUnitFilter;
use App\Components\Helpers\FormBuilderHelper;
use App\Components\Traits\ApiController;
use App\Exports\ProductUnitExportPdf;
use App\Exports\ProductUnitExportXls;
use App\Imports\ProductUnitImport;
use App\Product;
use App\Templates\ProductUnitImportSheetTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use \App\ProductUnit;

class ProductUnitController extends Controller
{
    use ApiController;

    public $type, $label = "Product Unit";

    public function index()
    {
        $data = [
            'title' => $this->label,
            'icon'  => 'fa fa-user-md',
            'breadcrumb' => [
                ['label' => $this->label],
            ]
        ];

        $form_data = new FormBuilderHelper(ProductUnit::class,$data);
        $final     = $form_data
                    ->useUtilities(false)
                    ->useFilter(false)
                    ->get();
        
        return view('components.global_form', $final);
    }

    public function list(ProductUnitFilter $filter)
    {
        $productUnit = ProductUnit::join('products', 'products.id', 'product_units.product_id')
			->leftJoin('units', 'units.id', 'product_units.unit_id')
			->select('product_units.*', 'products.name as product_name', 'units.name as unit_name')
			->filter($filter)->get();
        return $this->sendResponse($productUnit, 'Get Data Success!');
    }

    public function select2(ProductUnitFilter $filter)
    {
        return ProductUnit::join('products', 'products.id', 'product_units.product_id')
			->leftJoin('units', 'units.id', 'product_units.unit_id')
			->select('product_units.*', 'products.name as product_name', 'units.name as unit_name')
			->filter($filter)->get();
    }

    public function datatable(ProductUnitFilter $filter)
    {
        $data = ProductUnit::join('products', 'products.id', 'product_units.product_id')
			->leftJoin('units', 'units.id', 'product_units.unit_id')
			->select('product_units.*', 'products.name as product_name', 'units.name as unit_name')
			->filter($filter);

        return \DataTables::of($data)
            ->addColumn('action', function ($data){
                return "<button onclick=\"editModalProductUnit('".route('product-unit.edit',['id'=>$data->id])."')\" class='btn btn-sm btn-primary btn-square' data-target='#modalFormProductUnit' data-toggle='modal'><i class='fas fa-pencil-alt'></i></button>
                <button data-url=".route('product-unit.delete',['id'=>$data->id])." class='btn btn-sm btn-danger btn-square js-swal-delete'><i class='fas fa-trash-alt'></i></button>";
            })
            ->make(true);
    }

    public function getPrice($productId = '', $unitId = '')
    {
        $price = null;
        if (! ($data = Product::whereId($productId)->whereUnitId($unitId)->first()) ) {
            $data = ProductUnit::whereProductId($productId)->whereUnitId($unitId)->first();
        }

        $price = @$data->selling_price;
        return $this->sendResponse(['selling_price' => $price], 'Get Data Success!');
    }

    public function store(Request $request)
    {
        try{
            $productUnit = DB::transaction(function () use ($request) {
                $productUnit = new ProductUnit;
                $productUnit->fillAndValidate()->save();
                return $productUnit;
            });
        }catch(\Exception $ex){
            return $this->sendError('Insert Data Error!', $ex, 500);
        }

        return $this->sendResponse($productUnit, 'Insert Data Success!');
    }

    public function detail($id)
    {
        $productUnit = ProductUnit::join('products', 'products.id', 'product_units.product_id')
			->leftJoin('units', 'units.id', 'product_units.unit_id')
			->select('product_units.*', 'products.name as product_name', 'units.name as unit_name')
			->findOrFail($id);
        return $this->sendResponse($productUnit, 'Get Data Success!');
    }

    public function update(Request $request, $id)
    {
        try{
            $productUnit = DB::transaction(function () use ($request, $id) {
                $productUnit = ProductUnit::findOrFail($id);
                $productUnit->fillAndValidate()->save();
                return $productUnit;
            });
        }catch(\Exception $ex){
            return $this->sendError('Update Data Error!', $ex, 500);
        }

        return $this->sendResponse($productUnit, 'Update Data Success!');
    }

    public function destroy($id)
    {
        try{
            DB::transaction(function () use ($id) {
                $productUnit = ProductUnit::findOrFail($id);
                $productUnit->delete();
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
            'model'   => ProductUnitExportXls::class,
            'module'  => $this->label,
            'path'    => 'exports/product-unit/xlsx',
            'ext'     => 'xlsx',
        ]);
        
        return $this->sendResponse([], 'Download '.$this->label.' has been processed.');

    }

    public function exportPdf(Request $request)
    {
        processing_jobs([
            'title'   => 'Download '.$this->label,
            'filters' => $request->all(),
            'model'   => ProductUnitExportPdf::class,
            'module'  => $this->label,
            'path'    => 'exports/product-unit/pdf',
        ]);
        
        return $this->sendResponse([], 'Download '.$this->label.' has been processed.');
    }

    public function import(Request $request)
    {
        processing_jobs([
            'title'  => 'Upload '.$this->label,
            'model'  => ProductUnitImport::class,
            'module' => $this->label,
            'path'   => 'imports/product-unit',
        ]);
        
        return $this->sendResponse([], 'Upload '.$this->label.' has been processed.');
    }

    public function importTemplate()
    {
        return Excel::download(new ProductUnitImportSheetTemplate, 'Template For Import '.$this->label.' Data.xlsx');
    }
}
