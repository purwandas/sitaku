@extends('adminlte::page')

@section('title', config('app.name').' - '.@$title)

@section('content_header')
<h1 class="m-0 text-dark"><i class="{{ $icon }}"></i> {{ @$title }}</h1>
    @isset($breadcrumb)
    <div class="">
        <ol class="breadcrumb">
            @foreach($breadcrumb as $b)
                @isset($b['url'])
                    <li class="breadcrumb-item"><a href="{{ $b['url'] }}">{{ $b['label'] }}</a></li>
                @else
                    <li class="breadcrumb-item active">{{ $b['label'] }}</li>
                @endif
            @endforeach
        </ol>
    </div>
    @endisset
@stop

@section('content')
	@if(isset($includeView))
		@if(is_array($includeView))
			@if(count($includeView) > 0)
				@foreach($includeView as $key => $value)
					@if(is_numeric($key))
						@include($value)
					@else
						@include($key,$value)
					@endif
				@endforeach
			@endif
		@else
			@include($includeView)
		@endif
	@endif

	@if(isset($setupFilterBuilder) || isset($setupFormBuilder) || isset($setupDatatableBuilder))
		<div class="card">
			<div class="card-body">
				{{-- <h4 class="mt-0 header-title">Create User</h4> --}}
				{{-- @include('components.datatable_builder',['name' => 'user_table']) --}}
				@if(isset($setupFilterBuilder))
					@include('components.filter_builder',$setupFilterBuilder)
				@endif
				@if(isset($setupFormBuilder))
					@include('components.form_builder',$setupFormBuilder)
				@endif
				@if(isset($setupDatatableBuilder))
					@include('components.datatable_builder',$setupDatatableBuilder)
				@endif
			</div>
		</div>
	@endif
@endsection

@push('inject-view')
@if(isset($injectView))
	@if(is_array($injectView))
		@if(count($injectView) > 0)
			@foreach($injectView as $key => $value)
				@if(is_numeric($key))
					@include($value)
				@else
					@include($key,$value)
				@endif
			@endforeach
		@endif
	@else
		@include($injectView)
	@endif
@endif
@endpush