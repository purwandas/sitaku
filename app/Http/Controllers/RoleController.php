<?php

namespace App\Http\Controllers;

use App\Components\Filters\RoleFilter;
use App\Components\Helpers\FormBuilderHelper;
use App\Components\Traits\ApiController;
use App\Exports\RoleExportPdf;
use App\Exports\RoleExportXls;
use App\Imports\RoleImport;
use App\Templates\RoleImportSheetTemplate;
use \App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class RoleController extends Controller
{
    use ApiController;

    public $type, $label = "Role";

    public function index()
    {
        $data = [
            'title' => $this->label,
            'icon'  => 'fas fa-address-card',
            'breadcrumb' => [
                ['label' => $this->label],
            ]
        ];

        $form_data = new FormBuilderHelper(Role::class,$data);
        $final     = $form_data->get();
        
        return view('components.global_form', $final);
    }

    public function list(RoleFilter $filter)
    {
        $role = Role::orderBy('roles.id','desc')->filter($filter)->get();
        return $this->sendResponse($role, 'Get Data Success!');
    }

    public function select2(RoleFilter $filter)
    {
        return Role::orderBy('roles.id','desc')->filter($filter)->get();
    }

    public function datatable(RoleFilter $filter)
    {
        $data = Role::orderBy('roles.id','desc')->filter($filter);

        return \DataTables::of($data)
            ->addColumn('action', function ($data){
                return "<button onclick=\"editModalRole('".route('role.edit',['id'=>$data->id])."')\" class='btn btn-sm btn-primary btn-square' data-target='#modalFormRole' data-toggle='modal'><i class='fas fa-pencil-alt'></i></button>
                <button data-url=".route('role.delete',['id'=>$data->id])." class='btn btn-sm btn-danger btn-square js-swal-delete'><i class='fas fa-trash-alt'></i></button>";
            })
            ->make(true);
    }

    public function store(Request $request)
    {
        try{
            $role = DB::transaction(function () use ($request) {
                $role = new Role;
                $role->fillAndValidate()->save();
                return $role;
            });
        }catch(\Exception $ex){
            return $this->sendError('Insert Data Error!', $ex, 500);
        }

        return $this->sendResponse($role, 'Insert Data Success!');
    }

    public function detail($id)
    {
        $role = Role::orderBy('roles.id','desc')->findOrFail($id);
        return $this->sendResponse($role, 'Get Data Success!');
    }

    public function update(Request $request, $id)
    {
        try{
            $role = DB::transaction(function () use ($request, $id) {
                $role = Role::findOrFail($id);
                $role->fillAndValidate()->save();
                return $role;
            });
        }catch(\Exception $ex){
            return $this->sendError('Update Data Error!', $ex, 500);
        }

        return $this->sendResponse($role, 'Update Data Success!');
    }

    public function destroy($id)
    {
        try{
            DB::transaction(function () use ($id) {
                $role = Role::findOrFail($id);
                $role->delete();
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
            'model'   => RoleExportXls::class,
            'module'  => $this->label,
            'path'    => 'exports/role/xlsx',
            'ext'     => 'xlsx',
        ]);
        
        return $this->sendResponse([], 'Download '.$this->label.' has been processed.');

    }

    public function exportPdf(Request $request)
    {
        processing_jobs([
            'title'   => 'Download '.$this->label,
            'filters' => $request->all(),
            'model'   => RoleExportPdf::class,
            'module'  => $this->label,
            'path'    => 'exports/role/pdf',
        ]);
        
        return $this->sendResponse([], 'Download '.$this->label.' has been processed.');
    }

    public function import(Request $request)
    {
        processing_jobs([
            'title'  => 'Upload '.$this->label,
            'model'  => RoleImport::class,
            'module' => $this->label,
            'path'   => 'imports/role',
        ]);
        
        return $this->sendResponse([], 'Upload '.$this->label.' has been processed.');
    }

    public function importTemplate()
    {
        return Excel::download(new RoleImportSheetTemplate, 'Template For Import '.$this->label.' Data.xlsx');
    }
}
