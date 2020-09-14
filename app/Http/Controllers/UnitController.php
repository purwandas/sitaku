<?php

namespace App\Http\Controllers;

use App\Components\Filters\UnitFilter;
use App\Components\Helpers\FormBuilderHelper;
use App\Components\Traits\ApiController;
use App\Exports\UnitExportPdf;
use App\Exports\UnitExportXls;
use App\Imports\UnitImport;
use App\Templates\UnitImportSheetTemplate;
use \App\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class UnitController extends Controller
{
    use ApiController;

    public $type, $label = "Unit";

    public function index()
    {
        $data = [
            'title' => $this->label,
            'icon'  => 'fa fa-user-md',
            'breadcrumb' => [
                ['label' => $this->label],
            ]
        ];

        $form_data = new FormBuilderHelper(Unit::class,$data);
        $final     = $form_data->get();
        
        return view('components.global_form', $final);
    }

    public function list(UnitFilter $filter)
    {
        $unit = Unit::filter($filter)->get();
        return $this->sendResponse($unit, 'Get Data Success!');
    }

    public function select2(UnitFilter $filter)
    {
        return Unit::filter($filter)->get();
    }

    public function datatable(UnitFilter $filter)
    {
        $data = Unit::filter($filter);

        return \DataTables::of($data)
            ->addColumn('action', function ($data){
                return "<button onclick=\"editModalUnit('".route('unit.edit',['id'=>$data->id])."')\" class='btn btn-sm btn-primary btn-square' data-target='#modalFormUnit' data-toggle='modal'><i class='fas fa-pencil-alt'></i></button>
                <button data-url=".route('unit.delete',['id'=>$data->id])." class='btn btn-sm btn-danger btn-square js-swal-delete'><i class='fas fa-trash-alt'></i></button>";
            })
            ->make(true);
    }

    public function store(Request $request)
    {
        try{
            $unit = DB::transaction(function () use ($request) {
                $unit = new Unit;
                $unit->fillAndValidate()->save();
                return $unit;
            });
        }catch(\Exception $ex){
            return $this->sendError('Insert Data Error!', $ex, 500);
        }

        return $this->sendResponse($unit, 'Insert Data Success!');
    }

    public function detail($id)
    {
        $unit = Unit::findOrFail($id);
        return $this->sendResponse($unit, 'Get Data Success!');
    }

    public function update(Request $request, $id)
    {
        try{
            $unit = DB::transaction(function () use ($request, $id) {
                $unit = Unit::findOrFail($id);
                $unit->fillAndValidate()->save();
                return $unit;
            });
        }catch(\Exception $ex){
            return $this->sendError('Update Data Error!', $ex, 500);
        }

        return $this->sendResponse($unit, 'Update Data Success!');
    }

    public function destroy($id)
    {
        try{
            DB::transaction(function () use ($id) {
                $unit = Unit::findOrFail($id);
                $unit->delete();
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
            'model'   => UnitExportXls::class,
            'module'  => $this->label,
            'path'    => 'exports/unit/xlsx',
            'ext'     => 'xlsx',
        ]);
        
        return $this->sendResponse([], 'Download '.$this->label.' has been processed.');

    }

    public function exportPdf(Request $request)
    {
        processing_jobs([
            'title'   => 'Download '.$this->label,
            'filters' => $request->all(),
            'model'   => UnitExportPdf::class,
            'module'  => $this->label,
            'path'    => 'exports/unit/pdf',
        ]);
        
        return $this->sendResponse([], 'Download '.$this->label.' has been processed.');
    }

    public function import(Request $request)
    {
        processing_jobs([
            'title'  => 'Upload '.$this->label,
            'model'  => UnitImport::class,
            'module' => $this->label,
            'path'   => 'imports/unit',
        ]);
        
        return $this->sendResponse([], 'Upload '.$this->label.' has been processed.');
    }

    public function importTemplate()
    {
        return Excel::download(new UnitImportSheetTemplate, 'Template For Import '.$this->label.' Data.xlsx');
    }
}
