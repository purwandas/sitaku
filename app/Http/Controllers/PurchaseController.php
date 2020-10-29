<?php

namespace App\Http\Controllers;

use App\Components\Filters\PurchaseDetailFilter;
use App\Components\Filters\PurchaseFilter;
use App\Components\Helpers\DatatableBuilderHelper;
use App\Components\Helpers\FormBuilderHelper;
use App\Components\Traits\ApiController;
use App\Exports\PurchaseExportPdf;
use App\Exports\PurchaseExportXls;
use App\Imports\PurchaseImport;
use App\ProductUnit;
use App\Templates\PurchaseImportSheetTemplate;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use \App\Purchase;
use \App\PurchaseDetail;

class PurchaseController extends Controller
{
    use ApiController;

    public $type, $label = "Purchase", $icon = 'fa fa-shopping-cart';

    public function index()
    {
        $data = [
            'title' => $this->label,
            'icon'  => 'fa fa-user-md',
            'breadcrumb' => [
                ['label' => $this->label],
            ]
        ];

        $form_data = new FormBuilderHelper(Purchase::class,$data);
        $final     = $form_data
                    ->setCreatable(false)
                    ->useFormBuilder(false)
                    ->setAdditionalDatatableColumns(['product'])
                    ->setOrderDatatableColumns([
                        4 => 'product',
                    ])
                    ->setDatatableColumnDefs([
                        4 => ['class','text-center'],
                        5 => ['class','text-right'],
                        6 => ['class','text-right'],
                        7 => ['class','text-right'],
                    ])
                    ->setDatatableButtons(['export-xls','export-pdf','job-status'])
                    ->injectView(['inject/sales-detail' => ['name' => 'purchase']])
                    ->get();
        
        return view('components.global_form', $final);
    }

    public function form($id = 0)
    {
        $label = 'Purchase';
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
            ]
        ];

        $multipleColumn2[1] = [
            'type'      => 'select2',
            'name'      => 'unit',
            'text'      => 'obj.name',
            'options'   => 'unit.select2',
            'keyTerm'   => '_name',
            'elOptions' => [
                'placeholder' => 'Unit',
                'required'    => 'required',
                'class'       => 'get-price unit',
            ]
        ];

        $multipleColumn2[2] = [
            'type'    => 'text',
            'name'    => 'unit_price',
            'options' => [
                'labelText' => 'Price',
                'elOptions' => [
                    'placeholder' => 'Price',
                    'class'       => 'form-control money calc price',
                    'min'         => 0
                ]
            ]
        ];

        $multipleColumn2[3] = [
            'type'    => 'text',
            'name'    => 'unit_qty',
            'options' => [
                'labelText' => 'Qty',
                'elOptions' => [
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

        $form_data = new FormBuilderHelper(Purchase::class,$data);
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

    public function list(PurchaseFilter $filter)
    {
        $purchase = Purchase::join('users', 'users.id', 'purchases.user_id')
			->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
			->select('purchases.*', 'users.name as user_name', 'suppliers.name as supplier_name')
			->filter($filter)->get();
        return $this->sendResponse($purchase, 'Get Data Success!');
    }

    public function select2(PurchaseFilter $filter)
    {
        return Purchase::join('users', 'users.id', 'purchases.user_id')
			->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
			->select('purchases.*', 'users.name as user_name', 'suppliers.name as supplier_name')
			->filter($filter)->get();
    }

    public function datatable(PurchaseFilter $filter)
    {
        $data = Purchase::join('users', 'users.id', 'purchases.user_id')
			->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
			->select('purchases.*', 'users.name as user_name', 'suppliers.name as supplier_name')
			->filter($filter);

        return \DataTables::of($data)
            ->editColumn('product', function($data){
                $detail = PurchaseDetail::wherePurchaseId($data->id)->join('products','products.id','purchase_details.product_id')->select('purchase_details.*' ,'products.name as product')->get()->pluck('product')->toArray();
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
                $result = "<button onclick=\"editModalPurchase('".route('purchase.edit',['id'=>$data->id])."')\" class='btn btn-sm btn-primary btn-square' data-target='#modalFormPurchase' data-toggle='modal'><i class='fas fa-pencil-alt'></i></button>";
                $result = "<button data-url=".route('purchase.delete',['id'=>$data->id])." class='btn btn-sm btn-danger btn-square js-swal-delete'><i class='fas fa-trash-alt'></i></button>";
                return $result;
            })
            ->rawColumns(['product','action'])
            ->make(true);
    }

    public function datatableDetail(PurchaseDetailFilter $filter, $id)
    {
        $data = PurchaseDetail::wherePurchaseId($id)->with(['product','unit'])->filter($filter);

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
            $purchase = DB::transaction(function () use ($request) {
                $purchase = new Purchase;
                $newRequest = $request->only(['date','user_id', 'supplier_id', 'total_payment', 'total_paid', 'total_change']);
                if (!array_key_exists('user_id', $newRequest)) {
                    $newRequest['user_id'] = \Auth::user()->id;
                }
                $newRequest['total_payment'] = getAutoNumeric($newRequest['total_payment']);
                $newRequest['total_paid']    = getAutoNumeric($newRequest['total_paid']);
                $newRequest['total_change']  = getAutoNumeric($newRequest['total_change']);
                $purchase->fillAndValidate($newRequest)->save();

                foreach ($request['detail'] as $key => $value) {
                    ProductUnit::updateOrCreate([
                        'product_id' => $value['product'],
                        'unit_id'    => $value['unit'],
                    ],[
                        'selling_price' => getAutoNumeric($value['unit_price'])
                    ]);

                    PurchaseDetail::updateOrCreate([
                        'purchase_id'   => $purchase->id,
                        'product_id' => $value['product'],
                        'unit_id'    => $value['unit'],
                    ],[
                        'price'      => getAutoNumeric($value['unit_price']),
                        'qty'        => getAutoNumeric($value['unit_qty']),
                        'total'      => getAutoNumeric($value['total']),
                    ]);
                }
                return $purchase;
            });
        }catch(\Exception $ex){
            return $this->sendError('Insert Data Error!', $ex, 500);
        }

        return $this->sendResponse($purchase, 'Insert Data Success!');
    }

    public function detail($id)
    {
        $purchase = Purchase::join('users', 'users.id', 'purchases.user_id')
			->join('suppliers', 'suppliers.id', 'purchases.supplier_id')
			->select('purchases.*', 'users.name as user_name', 'suppliers.name as supplier_name')
			->findOrFail($id);
        return $this->sendResponse($purchase, 'Get Data Success!');
    }

    public function update(Request $request, $id)
    {
        try{
            $purchase = DB::transaction(function () use ($request, $id) {
                $purchase = Purchase::findOrFail($id);
                $purchase->fillAndValidate()->save();
                return $purchase;
            });
        }catch(\Exception $ex){
            return $this->sendError('Update Data Error!', $ex, 500);
        }

        return $this->sendResponse($purchase, 'Update Data Success!');
    }

    public function destroy($id)
    {
        try{
            DB::transaction(function () use ($id) {
                $purchase = Purchase::findOrFail($id);
                $purchase->delete();
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
            'model'   => PurchaseExportXls::class,
            'module'  => $this->label,
            'path'    => 'exports/purchase/xlsx',
            'ext'     => 'xlsx',
        ]);
        
        return $this->sendResponse([], 'Download '.$this->label.' has been processed.');

    }

    public function exportPdf(Request $request)
    {
        processing_jobs([
            'title'   => 'Download '.$this->label,
            'filters' => $request->all(),
            'model'   => PurchaseExportPdf::class,
            'module'  => $this->label,
            'path'    => 'exports/purchase/pdf',
        ]);
        
        return $this->sendResponse([], 'Download '.$this->label.' has been processed.');
    }

    public function import(Request $request)
    {
        processing_jobs([
            'title'  => 'Upload '.$this->label,
            'model'  => PurchaseImport::class,
            'module' => $this->label,
            'path'   => 'imports/purchase',
        ]);
        
        return $this->sendResponse([], 'Upload '.$this->label.' has been processed.');
    }

    public function importTemplate()
    {
        return Excel::download(new PurchaseImportSheetTemplate, 'Template For Import '.$this->label.' Data.xlsx');
    }
}
