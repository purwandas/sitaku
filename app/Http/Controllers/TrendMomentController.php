<?php

namespace App\Http\Controllers;

use App\Components\Filters\TrendMomentFilter;
use App\Components\Helpers\DatatableBuilderHelper;
use App\Components\Helpers\FormBuilderHelper;
use App\Components\Traits\ApiController;
use App\Exports\TrendMomentExportPdf;
use App\Exports\TrendMomentExportXls;
use App\Imports\TrendMomentImport;
use App\Product;
use App\Templates\TrendMomentImportSheetTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use \App\TrendMoment;

class TrendMomentController extends Controller
{
    use ApiController;

    public $type, $label = "Trend Moment";

    public function index(Request $request, $product = '', $month = '')
    {
        $data = [
            'title' => $this->label,
            'icon'  => 'fa fa-user-md',
            'breadcrumb' => [
                ['label' => $this->label],
            ]
        ];

        $form_data = new FormBuilderHelper(TrendMoment::class,$data);
        $calculation = !empty($product) ? $this->calculation($product, $month) : [];
        $final     = $form_data
            ->useFormBuilder(false)
            ->useDatatable(false)
            ->setCustomVariables($calculation)
            ->includeView(['inject/trend-moment'])
            ->get();
        
        return view('components.global_form', $final);
    }

    public function calculation($product, $month)
    {
        // TEST SELECTED MONTH
        // $month = 1;
        // TEST SELECTED PRODUCT
        // $product = null;

        $trendData  = TrendMoment::
            when( @$product, function($q) use ($product){
                $q->whereProductId($product);
            })
            ->orderByDesc('id')
            ->limit(12)
            ->get();
        
        $data        = $trendData->sortBy('id');
        $dataResult  = [];
        $sigX        = 0;
        $sigY        = 0;
        $sigXY       = 0;
        $sigXSquare  = 0;
        $seasonIndex = 0;
        $idx         = 0;

        foreach ($data as $key => $value) {
            $x = $idx;
            $y = $value->total_sales;

            $dataResult[$idx]          = $value;
            $dataResult[$idx]['x']     = $x;
            $dataResult[$idx]['y']     = $y;
            $dataResult[$idx]['xy']    = ($x * $y);
            $dataResult[$idx]['xx']    = ($x * $x);
            $dataResult[$idx]['month'] = TrendMoment::monthArray()[$value->month_];

            if ($month == $value->month_) {
                $seasonIndex = $y;
            }

            $sigX       += $x;
            $sigY       += $y;
            $sigXY      += ($x * $y);
            $sigXSquare += ($x * $x);

            $idx ++;
        }

        $productName = Product::findOrFail($product)->name;

        if ($idx == 0 || $sigY == 0) {
            return [
                'message' => 'No Data Found',
                'product' => [$product, $productName],
                'month'   => $month,
            ];
        }


        $avg = $sigY / ($idx);
        $seasonIndex /= $avg;

        // ΣY = n.a + b.ΣX
        $n = $idx;
        
        if ( ( ($sigXSquare * $n) - ($sigX * $sigX) ) == 0) {
            return [
                'message' => 'Cannot calculate data',
                'product' => [$product, $productName],
                'month'   => $month,
            ];
        }

        // ΣXY = a.ΣX + b.ΣX²


        // b = (ΣXY (n) - ΣY (ΣX)) / (ΣX² (n) - ΣX (ΣX))
        $b = ( ($sigXY * $n) - ($sigY *$sigX) ) / ( ($sigXSquare * $n) - ($sigX * $sigX) );

        // a = (ΣY - b.ΣX) - n
        $a = ($sigY - ($b * $sigX)) / $n;

        $y = $a + ($b * $n);

        $seasonY = $seasonIndex * $y;


        $next = [];
        $i = 0;
        do{
            $next[$i]['prediction'] = round($a + ($b * ($n + $i)), 2);
            $next[$i]['trend']      = round($seasonIndex * $next[$i]['prediction'], 2);
            $next[$i]['idx']        = $i + $idx;
            $i++;
        }while($i <= 12);

        return [
            'sales'       => $dataResult,
            'a'           => $a,
            'b'           => $b,
            'sigX'        => $sigX,
            'sigY'        => $sigY,
            'sigXY'       => $sigXY,
            'sigXSquare'  => $sigXSquare,
            'n'           => $n,
            'seasonIndex' => $seasonIndex,
            'avg'         => $avg,
            'Y'           => $y,
            'ySeason'     => $seasonY,
            'next'        => $next,
            'product'     => [$product, $productName],
            'month'       => $month,
        ];
    }

    public function list(TrendMomentFilter $filter)
    {
        $trendMoment = TrendMoment::filter($filter)->get();
        return $this->sendResponse($trendMoment, 'Get Data Success!');
    }

    public function select2(TrendMomentFilter $filter)
    {
        return TrendMoment::filter($filter)->get();
    }

    public function datatable(Request $request, TrendMomentFilter $filter)
    {
        $data = TrendMoment::filter($filter);

        return \DataTables::of($data)
            ->editColumn('month_', function ($data){
                return TrendMoment::monthArray()[$data->month_];
            })
            ->addColumn('action', function ($data){
                $buttons = [];

                $buttons = array_merge($buttons, [
                                'edit' => [
                                    'onclick' => "editModalTrendMoment('".route('trend-moment.edit',['id'=>$data->id])."')",
                                    'data-target' => '#modalFormTrendMoment',
                                    'icon' => getSvgIcon('cil-pencil','mt-m-2'),
                                ],
                                'delete' => [
                                    'data-url' => route('trend-moment.delete',['id'=>$data->id]),
                                    'icon' => getSvgIcon('cil-trash','mt-m-2'),
                                ],
                            ]);

                return DatatableBuilderHelper::button($buttons);
            })
            ->make(true);
    }

    public function store(Request $request)
    {
        try{
            $trendMoment = DB::transaction(function () use ($request) {
                $trendMoment = new TrendMoment;
                $trendMoment->fillAndValidate()->save();
                return $trendMoment;
            });
        }catch(\Exception $ex){
            return $this->sendError('Insert Data Error!', $ex, 500);
        }

        return $this->sendResponse($trendMoment, 'Insert Data Success!');
    }

    public function detail($id)
    {
        $trendMoment = TrendMoment::findOrFail($id);
        return $this->sendResponse($trendMoment, 'Get Data Success!');
    }

    public function update(Request $request, $id)
    {
        try{
            $trendMoment = DB::transaction(function () use ($request, $id) {
                $trendMoment = TrendMoment::findOrFail($id);
                $trendMoment->fillAndValidate()->save();
                return $trendMoment;
            });
        }catch(\Exception $ex){
            return $this->sendError('Update Data Error!', $ex, 500);
        }

        return $this->sendResponse($trendMoment, 'Update Data Success!');
    }

    public function destroy($id)
    {
        try{
            DB::transaction(function () use ($id) {
                $trendMoment = TrendMoment::findOrFail($id);
                $trendMoment->delete();
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
            'model'   => TrendMomentExportXls::class,
            'module'  => $this->label,
            'path'    => 'exports/trend-moment/xlsx',
            'ext'     => 'xlsx',
        ]);
        
        return $this->sendResponse([], 'Download '.$this->label.' has been processed.');

    }

    public function exportPdf(Request $request)
    {
        processing_jobs([
            'title'   => 'Download '.$this->label,
            'filters' => $request->all(),
            'model'   => TrendMomentExportPdf::class,
            'module'  => $this->label,
            'path'    => 'exports/trend-moment/pdf',
        ]);
        
        return $this->sendResponse([], 'Download '.$this->label.' has been processed.');
    }

    public function import(Request $request)
    {
        processing_jobs([
            'title'  => 'Upload '.$this->label,
            'model'  => TrendMomentImport::class,
            'module' => $this->label,
            'path'   => 'imports/trend-moment',
        ]);
        
        return $this->sendResponse([], 'Upload '.$this->label.' has been processed.');
    }

    public function importTemplate()
    {
        return Excel::download(new TrendMomentImportSheetTemplate, 'Template For Import '.$this->label.' Data.xlsx');
    }
}
