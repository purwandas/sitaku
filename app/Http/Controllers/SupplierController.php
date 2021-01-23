<?php

namespace App\Http\Controllers;

use App\Components\Filters\SupplierFilter;
use App\Components\Helpers\FormBuilderHelper;
use App\Components\Traits\ApiController;
use App\Exports\SupplierExportPdf;
use App\Exports\SupplierExportXls;
use App\Imports\SupplierImport;
use App\Templates\SupplierImportSheetTemplate;
use \App\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class SupplierController extends Controller
{
    use ApiController;

    public $type, $label = "Supplier";

    public function index()
    {
        $data = [
            'title' => $this->label,
            'icon'  => 'fab fa-supple',
            'breadcrumb' => [
                ['label' => $this->label],
            ]
        ];

        $form_data = new FormBuilderHelper(Supplier::class,$data);
        $final     = $form_data
                    ->useUtilities(false)
                    ->useFilter(false)
                    ->get();
        
        return view('components.global_form', $final);
    }

    public function list(SupplierFilter $filter)
    {
        $supplier = Supplier::join('productions', 'productions.id', 'suppliers.production_id')
			->select('suppliers.*', 'productions.name as production_name')
			->filter($filter)->get();
        return $this->sendResponse($supplier, 'Get Data Success!');
    }

    public function select2(SupplierFilter $filter)
    {
        return Supplier::join('productions', 'productions.id', 'suppliers.production_id')
			->select('suppliers.*', 'productions.name as production_name')
			->filter($filter)->get();
    }

    public function datatable(SupplierFilter $filter)
    {
        $data = Supplier::join('productions', 'productions.id', 'suppliers.production_id')
			->select('suppliers.*', 'productions.name as production_name')
			->filter($filter);

        return \DataTables::of($data)
            ->addColumn('action', function ($data){
                return "<button onclick=\"editModalSupplier('".route('supplier.edit',['id'=>$data->id])."')\" class='btn btn-sm btn-primary btn-square' data-target='#modalFormSupplier' data-toggle='modal'><i class='fas fa-pencil-alt'></i></button>
                <button data-url=".route('supplier.delete',['id'=>$data->id])." class='btn btn-sm btn-danger btn-square js-swal-delete'><i class='fas fa-trash-alt'></i></button>";
            })
            ->make(true);
    }

    public function store(Request $request)
    {
        try{
            $supplier = DB::transaction(function () use ($request) {
                $supplier = new Supplier;
                $supplier->fillAndValidate()->save();
                return $supplier;
            });
        }catch(\Exception $ex){
            return $this->sendError('Insert Data Error!', $ex, 500);
        }

        return $this->sendResponse($supplier, 'Insert Data Success!');
    }

    public function detail($id)
    {
        $supplier = Supplier::join('productions', 'productions.id', 'suppliers.production_id')
			->select('suppliers.*', 'productions.name as production_name')
			->findOrFail($id);
        return $this->sendResponse($supplier, 'Get Data Success!');
    }

    public function update(Request $request, $id)
    {
        try{
            $supplier = DB::transaction(function () use ($request, $id) {
                $supplier = Supplier::findOrFail($id);
                $supplier->fillAndValidate()->save();
                return $supplier;
            });
        }catch(\Exception $ex){
            return $this->sendError('Update Data Error!', $ex, 500);
        }

        return $this->sendResponse($supplier, 'Update Data Success!');
    }

    public function destroy($id)
    {
        try{
            DB::transaction(function () use ($id) {
                $supplier = Supplier::findOrFail($id);
                $supplier->delete();
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
            'model'   => SupplierExportXls::class,
            'module'  => $this->label,
            'path'    => 'exports/supplier/xlsx',
            'ext'     => 'xlsx',
        ]);
        
        return $this->sendResponse([], 'Download '.$this->label.' has been processed.');

    }

    public function exportPdf(Request $request)
    {
        processing_jobs([
            'title'   => 'Download '.$this->label,
            'filters' => $request->all(),
            'model'   => SupplierExportPdf::class,
            'module'  => $this->label,
            'path'    => 'exports/supplier/pdf',
        ]);
        
        return $this->sendResponse([], 'Download '.$this->label.' has been processed.');
    }

    public function import(Request $request)
    {
        processing_jobs([
            'title'  => 'Upload '.$this->label,
            'model'  => SupplierImport::class,
            'module' => $this->label,
            'path'   => 'imports/supplier',
        ]);
        
        return $this->sendResponse([], 'Upload '.$this->label.' has been processed.');
    }

    public function importTemplate()
    {
        return Excel::download(new SupplierImportSheetTemplate, 'Template For Import '.$this->label.' Data.xlsx');
    }
}
