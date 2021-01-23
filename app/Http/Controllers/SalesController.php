<?php

namespace App\Http\Controllers;

use App\Components\Filters\SalesDetailFilter;
use App\Components\Filters\SalesFilter;
use App\Components\Helpers\FormBuilderHelper;
use App\Components\Traits\ApiController;
use App\Exports\SalesExportPdf;
use App\Exports\SalesExportXls;
use App\Imports\SalesImport;
use App\Product;
use App\SalesDetail;
use App\Templates\SalesImportSheetTemplate;
use App\TrendMoment;
use App\Unit;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use \App\Sales;

class SalesController extends Controller
{
    use ApiController;

    public $type, $label = "Sales Report", $icon = 'fa fa-shopping-cart';

    public function index()
    {
        $data = [
            'title' => $this->label,
            'icon'  => $this->icon,
            'breadcrumb' => [
                ['label' => $this->label],
            ]
        ];

        $form_data = new FormBuilderHelper(Sales::class,$data);
        $final     = $form_data
                    ->setCreatable(false)
                    ->useFormBuilder(false)
                    ->setAdditionalDatatableColumns(['product'])
                    ->setOrderDatatableColumns([
                        3 => 'product',
                    ])
                    ->setDatatableColumnDefs([
                        3 => ['class','text-center'],
                        4 => ['class','text-right'],
                        5 => ['class','text-right'],
                        6 => ['class','text-right'],
                    ])
                    ->setDatatableButtons(['export-xls','export-pdf','job-status'])
                    ->injectView(['inject/sales-detail'])
                    ->get();
        
        return view('components.global_form', $final);
    }

    public function form($id = 0)
    {
        $label = 'Sales';
        $data = [
            'title' => $label,
            'icon'  => $this->icon,
            'breadcrumb' => [
                ['label' => ((!empty($id)) ? 'Edit ' : 'Add new ').$label],
            ],
            'customVariables' => [
                'id' => $id,
            ],
        ];

        $customFormBuilder = [];

        if (!(@\Auth::user()->role_id) == User::MASTER_ADMIN)
        $customFormBuilder['user_id'] = [
            'type' => 'hidden',
            'value' => @\Auth::user()->id

        ];

        $customFormBuilder['date'] = [
            'type'      => 'date',
            'value'     => Carbon::now()->format('Y-m-d'),
            'elOptions' => [
                'placeholder' => 'Product',
                'required'    => 'required'
            ]
        ];

        $multipleColumn2[0] = [
            'type'      => 'select2',
            'name'      => 'product',
            'text'      => 'obj.name',
            'options'   => 'product.select2',
            'keyTerm'   => '_name',
            'elOptions' => [
                'placeholder' => 'Product',
                'required'    => 'required',
                'class'       => 'get-price product',
            ],
            'pluginOptions' => [
                'allowClear' => false,
            ]
        ];

        $multipleColumn2[1] = [
            'type'    => 'text',
            'name'    => 'unit_price',
            'options' => [
                'labelText' => 'Price',
                'elOptions' => [
                    'disabled' => true,
                    'placeholder' => 'Price',
                    'class'       => 'form-control money calc price',
                    'min'         => 0
                ]
            ]
        ];

        $multipleColumn2[2] = [
            'type'    => 'text',
            'name'    => 'unit_stock',
            'options' => [
                'labelText' => 'Stock',
                'elOptions' => [
                    'disabled' => true,
                    'placeholder' => 'Stock',
                    'class'       => 'form-control money calc stock',
                    'min'         => 0,
                    'width' => '100px',
                ]
            ]
        ];

        $multipleColumn2[3] = [
            'type'    => 'text',
            'name'    => 'unit_qty',
            'options' => [
                'labelText' => 'Qty',
                'elOptions' => [
                    'disabled' => true,
                    'placeholder' => 'Qty',
                    'class'       => 'form-control money calc qty',
                    'min'         => 0,
                    'width' => '100px',
                ]
            ]
        ];

        $multipleColumn2[4] = [
            'type'      => 'text',
            'name'      => 'total',
            'options' => [
                'elOptions' => [
                    'placeholder' => 'Total',
                    'required'    => 'required',
                    'readonly'    => 'readonly',
                    'class'       => 'form-control money sub-total',
                ]
            ]
        ];

        $customFormBuilder['detail'] = [
            'type'     => 'multiplecolumn',
            'useLabel' => false,
            'columns'  => $multipleColumn2
        ];

        $form_data = new FormBuilderHelper(Sales::class,$data);
        $final     = $form_data
                    ->setFormPage(true)
                    ->useModal(false)
                    ->useDatatable(false)
                    ->setExceptFormBuilderColumns(['total_payment', 'total_paid', 'total_change'])
                    ->setCustomFormBuilder($customFormBuilder)
                    ->injectView('inject/sales-form')
                    ->get();
        
        return view('components.global_form', $final);
    }

    public function list(SalesFilter $filter)
    {
        $sales = Sales::filter($filter)->get();
        return $this->sendResponse($sales, 'Get Data Success!');
    }

    public function select2(SalesFilter $filter)
    {
        return Sales::filter($filter)->get();
    }

    public function datatable(SalesFilter $filter)
    {
        $data = Sales::join('users','users.id','sales.user_id')->select('sales.*','users.name as user_name')->filter($filter);

        return \DataTables::of($data)
            ->editColumn('product', function($data){
                $detail = SalesDetail::whereSalesId($data->id)->join('products','products.id','sales_details.product_id')->select('sales_details.*' ,'products.name as product')->get()->pluck('product')->toArray();
                if (count($detail)) {
                    if (count($detail) <= 1) {
                        $result = implode(', ', $detail);
                    } else {
                        $result = "<button onclick=\"detailModal('".$data->id."')\" class='btn btn-sm btn-info btn-square' data-target='#detailModal' data-toggle='modal'><i class='fas fa-info'></i> Detail</button>";
                    }
                }
                return @$result ?? '-';
            })
            ->editColumn('total_payment', function($data){
                return currency_ifexist($data->total_payment, 'Rp');
            })
            ->editColumn('total_paid', function($data){
                return currency_ifexist($data->total_paid, 'Rp');
            })
            ->editColumn('total_change', function($data){
                return currency_ifexist($data->total_change, 'Rp');
            })
            ->addColumn('action', function ($data){
                $result = "<button onclick=\"editModalSales('".route('sales.edit',['id'=>$data->id])."')\" class='btn btn-sm btn-primary btn-square' data-target='#modalFormSales' data-toggle='modal'><i class='fas fa-pencil-alt'></i></button>";
                $result = "<button data-url=".route('sales.delete',['id'=>$data->id])." class='btn btn-sm btn-danger btn-square js-swal-delete'><i class='fas fa-trash-alt'></i></button>";
                return $result;
            })
            ->rawColumns(['product','action'])
            ->make(true);
    }

    public function datatableDetail(SalesDetailFilter $filter, $id)
    {
        $data = SalesDetail::whereSalesId($id)->with(['product','unit'])->filter($filter);

        return \DataTables::of($data)
            ->addColumn('product', function($data){
                return $data->product->name;
            })
            ->addColumn('unit', function($data){
                return $data->unit->name;
            })
            ->editColumn('qty', function($data){
                return currency_ifexist($data->qty, '');
            })
            ->editColumn('price', function($data){
                return currency_ifexist($data->price, 'Rp');
            })
            ->editColumn('total', function($data){
                return currency_ifexist($data->total, 'Rp');
            })
            ->make(true);
    }

    public function store(Request $request)
    {
        try{
            $sales = DB::transaction(function () use ($request) {
                $sales      = new Sales;
                $newRequest = $request->only(['date','user_id', 'total_payment', 'total_paid', 'total_change']);
                if (!array_key_exists('user_id', $newRequest)) {
                    $newRequest['user_id'] = \Auth::user()->id;
                }
                $newRequest['total_payment'] = getAutoNumeric($newRequest['total_payment']);
                $newRequest['total_paid']    = getAutoNumeric($newRequest['total_paid']);
                $newRequest['total_change']  = getAutoNumeric($newRequest['total_change']);
                $sales->fillAndValidate($newRequest)->save();

                foreach ($request['detail'] as $key => $value) {
                    $product = Product::findOrFail($value['product']);

                    SalesDetail::updateOrCreate([
                        'sales_id'   => $sales->id,
                        'product_id' => $product->id,
                        'unit_id'    => $product->unit_id,
                    ],[
                        'price'      => getAutoNumeric($value['unit_price']),
                        'qty'        => getAutoNumeric($value['unit_qty']),
                        'total'      => getAutoNumeric($value['total']),
                    ]);

                    $now = Carbon::now();

                    $trend = TrendMoment::where('product_id',$product->id)
                                        ->where('month_',$now->month)
                                        ->where('year_',$now->year)
                                        ->first();

                    if(!$trend){
                        $trend = new TrendMoment;
                        $trend->product_id = $product->id;
                        $trend->month_ = $now->month;
                        $trend->year_ = $now->year;
                        $trend->total_sales = $value['unit_qty'];
                        $trend->save();
                    }else{
                        $trend->total_sales = $trend->total_sales + $value['unit_qty'];
                        $trend->save();
                    }

                    // $trend = TrendMoment::firstOrCreate([
                    //     'product_id' => $value['product'],
                    //     'month_'     => $now->month,
                    //     'year_'      => $now->year,
                    // ]);

                    // $trend->total_sales = $trend->total_sales + $value['unit_qty'];

                    // Ngurangin Stock
                    if($value['unit_qty'] <= $product->stock){
                        $product->stock = $product->stock - $value['unit_qty'];
                        $product->save();
                    }else{
                        DB::rollback();
                        return [
                            'status' => false,
                            'data' => $product->name,
                        ];
                    }
                }

                return [
                    'status' => true,
                    'data' => $sales,
                ];
            });
        }catch(\Exception $ex){
            return $this->sendError('Insert Data Error!', $ex, 500);
        }

        if($sales['status'] == false){
            return $this->sendError('Insert Data Error!', 'Insufficient stock for: '.$sales['data'], 500);
        }
        
        return $this->sendResponse($sales['data'], 'Insert Data Success!');
    }

    public function detail($id)
    {
        $sales = Sales::findOrFail($id);
        return $this->sendResponse($sales, 'Get Data Success!');
    }

    public function update(Request $request, $id)
    {
        try{
            $sales = DB::transaction(function () use ($request, $id) {
                $sales = Sales::findOrFail($id);
                $sales->fillAndValidate()->save();
                return $sales;
            });
        }catch(\Exception $ex){
            return $this->sendError('Update Data Error!', $ex, 500);
        }

        return $this->sendResponse($sales, 'Update Data Success!');
    }

    public function destroy($id)
    {
        try{
            DB::transaction(function () use ($id) {
                $sales = Sales::findOrFail($id);
                // Balikin Stock
                foreach ($sales->sales_details as $detail) {
                    $product = Product::findOrFail($detail->product_id);
                    $product->stock = $product->stock + $detail->qty;
                    $product->save();
                }

                $sales->sales_details()->delete();
                $sales->delete();
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
            'model'   => SalesExportXls::class,
            'module'  => $this->label,
            'path'    => 'exports/sales/xlsx',
            'ext'     => 'xlsx',
        ]);
        
        return $this->sendResponse([], 'Download '.$this->label.' has been processed.');

    }

    public function exportPdf(Request $request)
    {
        processing_jobs([
            'title'   => 'Download '.$this->label,
            'filters' => $request->all(),
            'model'   => SalesExportPdf::class,
            'module'  => $this->label,
            'path'    => 'exports/sales/pdf',
        ]);
        
        return $this->sendResponse([], 'Download '.$this->label.' has been processed.');
    }

    public function import(Request $request)
    {
        processing_jobs([
            'title'  => 'Upload '.$this->label,
            'model'  => SalesImport::class,
            'module' => $this->label,
            'path'   => 'imports/sales',
        ]);
        
        return $this->sendResponse([], 'Upload '.$this->label.' has been processed.');
    }

    public function importTemplate()
    {
        return Excel::download(new SalesImportSheetTemplate, 'Template For Import '.$this->label.' Data.xlsx');
    }
}
