<?php

namespace App\Http\Controllers;

use App\Components\Filters\CategoryFilter;
use App\Components\Helpers\FormBuilderHelper;
use App\Components\Traits\ApiController;
use App\Exports\CategoryExportPdf;
use App\Exports\CategoryExportXls;
use App\Imports\CategoryImport;
use App\Templates\CategoryImportSheetTemplate;
use \App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class CategoryController extends Controller
{
    use ApiController;

    public $type, $label = "Category";

    public function index()
    {
        $data = [
            'title' => $this->label,
            'icon'  => 'far fa-caret-square-right ',
            'breadcrumb' => [
                ['label' => $this->label],
            ]
        ];

        $form_data = new FormBuilderHelper(Category::class,$data);
        $final     = $form_data
                    ->useUtilities(false)
                    ->useFilter(false)
                    ->get();
        
        return view('components.global_form', $final);
    }

    public function list(CategoryFilter $filter)
    {
        $category = Category::filter($filter)->get();
        return $this->sendResponse($category, 'Get Data Success!');
    }

    public function select2(CategoryFilter $filter)
    {
        return Category::filter($filter)->get();
    }

    public function datatable(CategoryFilter $filter)
    {
        $data = Category::filter($filter);

        return \DataTables::of($data)
            ->addColumn('action', function ($data){
                return "<button onclick=\"editModalCategory('".route('category.edit',['id'=>$data->id])."')\" class='btn btn-sm btn-primary btn-square' data-target='#modalFormCategory' data-toggle='modal'><i class='fas fa-pencil-alt'></i></button>
                <button data-url=".route('category.delete',['id'=>$data->id])." class='btn btn-sm btn-danger btn-square js-swal-delete'><i class='fas fa-trash-alt'></i></button>";
            })
            ->make(true);
    }

    public function store(Request $request)
    {
        try{
            $category = DB::transaction(function () use ($request) {
                $category = new Category;
                $category->fillAndValidate()->save();
                return $category;
            });
        }catch(\Exception $ex){
            return $this->sendError('Insert Data Error!', $ex, 500);
        }

        return $this->sendResponse($category, 'Insert Data Success!');
    }

    public function detail($id)
    {
        $category = Category::findOrFail($id);
        return $this->sendResponse($category, 'Get Data Success!');
    }

    public function update(Request $request, $id)
    {
        try{
            $category = DB::transaction(function () use ($request, $id) {
                $category = Category::findOrFail($id);
                $category->fillAndValidate()->save();
                return $category;
            });
        }catch(\Exception $ex){
            return $this->sendError('Update Data Error!', $ex, 500);
        }

        return $this->sendResponse($category, 'Update Data Success!');
    }

    public function destroy($id)
    {
        try{
            DB::transaction(function () use ($id) {
                $category = Category::findOrFail($id);
                $category->delete();
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
            'model'   => CategoryExportXls::class,
            'module'  => $this->label,
            'path'    => 'exports/category/xlsx',
            'ext'     => 'xlsx',
        ]);
        
        return $this->sendResponse([], 'Download '.$this->label.' has been processed.');

    }

    public function exportPdf(Request $request)
    {
        processing_jobs([
            'title'   => 'Download '.$this->label,
            'filters' => $request->all(),
            'model'   => CategoryExportPdf::class,
            'module'  => $this->label,
            'path'    => 'exports/category/pdf',
        ]);
        
        return $this->sendResponse([], 'Download '.$this->label.' has been processed.');
    }

    public function import(Request $request)
    {
        processing_jobs([
            'title'  => 'Upload '.$this->label,
            'model'  => CategoryImport::class,
            'module' => $this->label,
            'path'   => 'imports/category',
        ]);
        
        return $this->sendResponse([], 'Upload '.$this->label.' has been processed.');
    }

    public function importTemplate()
    {
        return Excel::download(new CategoryImportSheetTemplate, 'Template For Import '.$this->label.' Data.xlsx');
    }
}
