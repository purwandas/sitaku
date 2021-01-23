<?php

namespace App\Http\Controllers;

use App\Components\Filters\UserFilter;
use App\Components\Helpers\FormBuilderHelper;
use App\Components\Traits\ApiController;
use App\Exports\UserExportPdf;
use App\Exports\UserExportXls;
use App\Imports\UserImport;
use App\Templates\UserImportSheetTemplate;
use \App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    use ApiController;

    public $type, $label = "User";

    public function index()
    {
        $data = [
            'title' => $this->label,
            'icon'  => 'fas fa-users-cog',
            'breadcrumb' => [
                ['label' => $this->label],
            ]
        ];

        $form_data = new FormBuilderHelper(User::class,$data);
        $final     = $form_data
            ->setCustomFormBuilder([
                'password' => [
                    'type' => 'password'
                ]
            ])
            ->setExceptDatatableColumns(['password'])
            ->setExceptFilter(['password'])
            ->useUtilities(false)
            ->useFilter(false)
            ->get();
        
        return view('components.global_form', $final);
    }

    public function list(UserFilter $filter)
    {
        $user = User::join('roles', 'roles.id', 'users.role_id')
			->select('users.*', 'roles.name as role_name')
			->orderBy('users.id','desc')->filter($filter)->get();
        return $this->sendResponse($user, 'Get Data Success!');
    }

    public function select2(UserFilter $filter)
    {
        return User::join('roles', 'roles.id', 'users.role_id')
			->select('users.*', 'roles.name as role_name')
			->orderBy('users.id','desc')->filter($filter)->get();
    }

    public function datatable(UserFilter $filter)
    {
        $data = User::join('roles', 'roles.id', 'users.role_id')
			->select('users.*', 'roles.name as role_name')
			->orderBy('users.id','desc')->filter($filter);

        return \DataTables::of($data)
            ->addColumn('action', function ($data){
                return "<button onclick=\"editModalUser('".route('user.edit',['id'=>$data->id])."')\" class='btn btn-sm btn-primary btn-square' data-target='#modalFormUser' data-toggle='modal'><i class='fas fa-pencil-alt'></i></button>
                <button data-url=".route('user.delete',['id'=>$data->id])." class='btn btn-sm btn-danger btn-square js-swal-delete'><i class='fas fa-trash-alt'></i></button>";
            })
            ->make(true);
    }

    public function store(Request $request)
    {
        try{
            $user = DB::transaction(function () use ($request) {
                $user = new User;
                $user->fillAndValidate()->save();
                $user->password = bcrypt($request->password);
                $user->save();
                return $user;
            });
        }catch(\Exception $ex){
            return $this->sendError('Insert Data Error!', $ex, 500);
        }

        return $this->sendResponse($user, 'Insert Data Success!');
    }

    public function detail($id)
    {
        $user = User::join('roles', 'roles.id', 'users.role_id')
			->select('users.*', 'roles.name as role_name')
			->orderBy('users.id','desc')->findOrFail($id);
        return $this->sendResponse($user, 'Get Data Success!');
    }

    public function update(Request $request, $id)
    {
        try{
            $user = DB::transaction(function () use ($request, $id) {
                $newRequest = $request->all();
                
                if(!empty($newRequest['password'])){
                    $newRequest['password'] = bcrypt($newRequest['password']);
                } else {
                    unset($newRequest['password']);
                }

                $user = User::findOrFail($id);
                $user->fillAndValidate($newRequest)->save();
                return $user;
            });
        }catch(\Exception $ex){
            return $this->sendError('Update Data Error!', $ex, 500);
        }

        return $this->sendResponse($user, 'Update Data Success!');
    }

    public function destroy($id)
    {
        try{
            DB::transaction(function () use ($id) {
                $user = User::findOrFail($id);
                $user->delete();
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
            'model'   => UserExportXls::class,
            'module'  => $this->label,
            'path'    => 'exports/user/xlsx',
            'ext'     => 'xlsx',
        ]);
        
        return $this->sendResponse([], 'Download '.$this->label.' has been processed.');

    }

    public function exportPdf(Request $request)
    {
        processing_jobs([
            'title'   => 'Download '.$this->label,
            'filters' => $request->all(),
            'model'   => UserExportPdf::class,
            'module'  => $this->label,
            'path'    => 'exports/user/pdf',
        ]);
        
        return $this->sendResponse([], 'Download '.$this->label.' has been processed.');
    }

    public function import(Request $request)
    {
        processing_jobs([
            'title'  => 'Upload '.$this->label,
            'model'  => UserImport::class,
            'module' => $this->label,
            'path'   => 'imports/user',
        ]);
        
        return $this->sendResponse([], 'Upload '.$this->label.' has been processed.');
    }

    public function importTemplate()
    {
        return Excel::download(new UserImportSheetTemplate, 'Template For Import '.$this->label.' Data.xlsx');
    }
}
