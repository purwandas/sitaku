<?php

namespace App\Http\Controllers;

use App\Components\Filters\ProductionFilter;
use App\Components\Helpers\FormBuilderHelper;
use App\Components\Traits\ApiController;
use App\Exports\ProductionExportPdf;
use App\Exports\ProductionExportXls;
use App\Imports\ProductionImport;
use App\Templates\ProductionImportSheetTemplate;
use \App\Production;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ProductionController extends Controller
{
    use ApiController;

    public $type, $label = "Production";

    public function index()
    {
        $data = [
            'title' => $this->label,
            'icon'  => 'fas fa-industry ',
            'breadcrumb' => [
                ['label' => $this->label],
            ]
        ];

        $form_data = new FormBuilderHelper(Production::class,$data);
        $final     = $form_data->get();
        
        return view('components.global_form', $final);
    }

    public function list(ProductionFilter $filter)
    {
        $production = Production::filter($filter)->get();
        return $this->sendResponse($production, 'Get Data Success!');
    }

    public function select2(ProductionFilter $filter)
    {
        return Production::filter($filter)->get();
    }

    public function datatable(ProductionFilter $filter)
    {
        $data = Production::filter($filter);

        return \DataTables::of($data)
            ->addColumn('action', function ($data){
                return "<button onclick=\"editModalProduction('".route('production.edit',['id'=>$data->id])."')\" class='btn btn-sm btn-primary btn-square' data-target='#modalFormProduction' data-toggle='modal'><i class='fas fa-pencil-alt'></i></button>
                <button data-url=".route('production.delete',['id'=>$data->id])." class='btn btn-sm btn-danger btn-square js-swal-delete'><i class='fas fa-trash-alt'></i></button>";
            })
            ->make(true);
    }

    public function store(Request $request)
    {
        try{
            $production = DB::transaction(function () use ($request) {
                $production = new Production;
                $production->fillAndValidate()->save();
                return $production;
            });
        }catch(\Exception $ex){
            return $this->sendError('Insert Data Error!', $ex, 500);
        }

        return $this->sendResponse($production, 'Insert Data Success!');
    }

    public function detail($id)
    {
        $production = Production::findOrFail($id);
        return $this->sendResponse($production, 'Get Data Success!');
    }

    public function update(Request $request, $id)
    {
        try{
            $production = DB::transaction(function () use ($request, $id) {
                $production = Production::findOrFail($id);
                $production->fillAndValidate()->save();
                return $production;
            });
        }catch(\Exception $ex){
            return $this->sendError('Update Data Error!', $ex, 500);
        }

        return $this->sendResponse($production, 'Update Data Success!');
    }

    public function destroy($id)
    {
        try{
            DB::transaction(function () use ($id) {
                $production = Production::findOrFail($id);
                $production->delete();
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
            'model'   => ProductionExportXls::class,
            'module'  => $this->label,
            'path'    => 'exports/production/xlsx',
            'ext'     => 'xlsx',
        ]);
        
        return $this->sendResponse([], 'Download '.$this->label.' has been processed.');

    }

    public function exportPdf(Request $request)
    {
        processing_jobs([
            'title'   => 'Download '.$this->label,
            'filters' => $request->all(),
            'model'   => ProductionExportPdf::class,
            'module'  => $this->label,
            'path'    => 'exports/production/pdf',
        ]);
        
        return $this->sendResponse([], 'Download '.$this->label.' has been processed.');
    }

    public function import(Request $request)
    {
        processing_jobs([
            'title'  => 'Upload '.$this->label,
            'model'  => ProductionImport::class,
            'module' => $this->label,
            'path'   => 'imports/production',
        ]);
        
        return $this->sendResponse([], 'Upload '.$this->label.' has been processed.');
    }

    public function importTemplate()
    {
        return Excel::download(new ProductionImportSheetTemplate, 'Template For Import '.$this->label.' Data.xlsx');
    }
}
