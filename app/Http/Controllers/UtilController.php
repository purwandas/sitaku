<?php

namespace App\Http\Controllers;

use App\Components\Filters\JobTraceFilter;
use App\JobTrace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UtilController extends Controller
{
    public function datatable(JobTraceFilter $filter)
	{
		return \DataTables::of(JobTrace::filter($filter)->with(['user'])->select('job_traces.*'))
			->addColumn('user_name', function($row){
				return $row->user->name ?? '-';
			})
			->editColumn('title', function($row){
				$icon = substr($row->file_path, -3) == 'pdf' ? 'pdf' : 'excel';

				return "<i class='fas fa-file-$icon'></i> ".$row->title;
			})
			->editColumn('status', function($row){
				$class = '';
				if($row->status == 'PROCESSING'){
					$class = 'primary';
				}elseif($row->status == 'FAILED'){
					$class = 'danger';
				}else{
					$class = 'success';
				}

				return "<div class='btn btn-sm btn-".$class."' style='cursor'>".$row->status."</div>";
			})
			->addColumn('action', function($row){
				$action = '';
				if($row->status != 'PROCESSING' && $row->status != 'FAILED'){
					$action = "<a href='".asset(Storage::url($row->file_path))."' target='_blank' class='btn btn-primary btn-sm'><i class='fa fa-download'></i></a>";
				}
				return $action;
			})
			->rawColumns(['title','status','action'])
			->make(true);
	}
}
