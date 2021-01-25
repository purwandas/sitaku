<?php

namespace App\Jobs;

use App\Models\Exports\PdfExport;
use App\JobTrace;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class UniversalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries   = 1;
    public $timeout = 3600;

    protected $trace;
    protected $params;
    protected $type;
    protected $ext;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(JobTrace $trace, $params, $type = 'import', $ext = 'xlsx')
    {
        $this->trace  = $trace;
        $this->params = $params;
        $this->type   = $type;
        $this->ext    = $ext;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $file = storage_path('app/public').'/'.$this->trace->file_path;
        $model = $this->trace->model;
        try{
            if ($this->type == 'imports'){
                Excel::import(new $model, $file);
            }

            if ($this->type == 'exports'){
                dirExists($this->trace->file_path);

                if ($this->ext == 'xlsx') {
                    Excel::store(new $model($this->params), $this->trace->file_path, 'public');
                } else {
                    $model::print($this->params, 'storage/'.$this->trace->file_path);
                }
            }
            
            $this->trace->update([
                'status' => 'DONE',
            ]);

        }catch(\Exception $ex){
            $this->trace->update([
                'status' => 'FAILED',
                'log'    => $ex->getMessage(),
            ]);
        }
    }

    public function failed($exception)
    {
        // Send user notification of failure, etc...
        $this->trace->update([
            'status' => 'FAILED',
            'log'    => 'SYSTEM ERROR : '.$exception->getMessage(),
        ]);

        // File::deleteDirectory($this->trace->path, $this->trace->name);
    }
}