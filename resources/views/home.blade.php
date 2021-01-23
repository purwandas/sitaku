@extends('adminlte::page')

@section('title', env('APP_NAME').(@$title ? ' - '.$title : ''))

@section('content_header')
    <h1 class="m-0 text-dark">Dashboard</h1>
@stop

@section('content')
    <div class="row">
        <div class="row col-lg-12" style="padding-right: 0px;">
            <div class="col-lg-6 col-6" style="padding-right: 0px;">
              <div class="card">
                <!-- small box -->
                <div class="small-box bg-primary">
                  <div class="inner">
                    <h3 id="total_product"></h3>

                    <p>Total Product</p>
                  </div>
                  <div class="icon">
                    <i class="fas fa-boxes"></i>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 col-6" style="padding-right: 0px;">
              <div class="card">
                <!-- small box -->
                <div class="small-box bg-success">
                  <div class="inner">
                    <h3><span id="total_supplier"></span></h3>

                    <p>Total Supplier</p>
                  </div>
                  <div class="icon">
                    <i class="fas fa-truck"></i>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 col-6" style="padding-right: 0px;">
              <div class="card">
                <!-- small box -->
                <div class="small-box bg-warning">
                  <div class="inner">
                    <h3><span id="total_daily_income"></span></h3>
                    <p>Total Daily Income</p>
                  </div>
                  <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6 col-6" style="padding-right: 0px;">
              <div class="card">
                <!-- small box -->
                <div class="small-box bg-danger">
                  <div class="inner">
                    <h3><span id="total_all_income"></span></h3>
                    <p>Total All Income</p>
                  </div>
                  <div class="icon">
                    <i class="fas fa-money-check"></i>
                  </div>
                </div>
              </div>
            </div>
        </div>
        <div class="row col-lg-12" style="padding-right: 0px;">
            <div class="row col-md-12"><h5 style="padding-left: 7.5px"><strong>Total Stock Product</strong></h5></div>
            {!! 
                DatatableBuilderHelper::render([
                    'name' => 'total_product_stock',
                    'url' => route('dashboard.product-stock-datatable'),
                    'columns' => [
                        ['name' => 'category_name'],
                        ['name' => 'product_name'],
                        ['name' => 'stock'],
                    ]
                ])
            !!}
        </div>
    </div>
@stop

@section('adminlte_css')
    <style type="text/css">
      .small-box{
        box-shadow: unset !important;
        margin-bottom: unset !important;
      }
      .small-box .icon>i{
        font-size: 30px !important;
        position: absolute;
        right: 15px;
        top: 15px;
        transition: all .3s linear;
      }
      .small-box .icon>i:hover{
        font-size: 35px !important;
      }
    </style>
@endsection

@push('additional-js')
<script type="text/javascript">
    $(document).ready(function(){
        getDashboardData();
    })

    function getDashboardData() {
        $.get('{{route('dashboard.data')}}',function(data){
            $('#total_product').html(data.total_product)
            $('#total_supplier').html(data.total_supplier)
            $('#total_daily_income').html(data.total_daily_income)
            $('#total_all_income').html(data.total_all_income)
        })
    }
</script>
@endpush