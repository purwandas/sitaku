<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\JobTrace;
use App\Models\OAuthGoogle;
use App\Models\VideoSpace;
use Exception;
use App\Http\Helpers\YoutubeClient;
use Carbon\Carbon;

class UploadYoutube implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 
     */
    public $tries   = 3;
    public $timeout = 30;

    /**
     * 
     */
    private $trace, $params, $request;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(JobTrace $trace, $params = [])
    {
        $this->trace = $trace;
        $this->params = $params;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try
        {
            /**
             * 
             */
            $video_model = json_decode($this->trace->model);

            /**
             * Load saved google tokens
             */
            $oauth_google = OAuthGoogle::where("status", "active")->first();
            if (isset($oauth_google->token))
            {
                /**
                 * Check revoke token
                 */
                $oauth_google_token = json_decode($oauth_google->token);
                if ($oauth_google_token->expires_at > time())
                {
                    /**
                     * 
                     */
                    $oauth_google->update([
                        "status" => "revoke"
                    ]);

                    /**
                     * 
                     */
                    Log::info("Google OAuth Token Revoke! Please authorized again on admin panel!");
            
                    /**
                     * 
                     */
                    $this->trace->update([
                        "status" => JobTrace::STATUS_SUSPEND,
                        "log" => "Google OAuth Revoked"
                    ]);

                    /**
                     * Retry a new jobs
                     */
                    dispatch(new UploadYoutube($this->trace))->delay(Carbon::now()->addSeconds(15));
                }
                else
                {
                    /**
                     * Upload video
                     */
                    $adapter = new YoutubeClient();
                    $request = $adapter->Upload((array) $oauth_google_token, $this->trace->file_path, $video_model->title, strip_tags($video_model->content));

                    /**
                     * Process youtube response
                     */
                    if ($request["success"])
                    {
                        $video_space = VideoSpace::where("id", $video_model->id);
                        /**
                         * Skip delete
                         */
                        // unlink(storage_path($video_space->first()->local_path));

                        /**
                         * 
                         */ 
                        $video_space->update([
                            "status" => 1,
                            "yt_id" => @json_decode($request["response"])->id,
                            "local_path" => NULL,
                        ]);    

                        /**
                         * 
                         */
                        $this->trace->update([
                            "status" => JobTrace::STATUS_SUCCESS
                        ]);

                        /**
                         * Log
                         */
                    }
                    else
                    {
                        /**
                         * 
                         */
                        Log::info($request["response"]);

                        /**
                         * Retry a new jobs
                         */
                        dispatch(new UploadYoutube($this->trace))->delay(Carbon::now()->addSeconds(300));

                        /**
                         * 
                         */
                        $this->trace->update([
                            "status" => JobTrace::STATUS_SUSPEND,
                            "log" => "Something when wrong with the Google Client OAuth! Retrying in 5 minutes"
                        ]);
                    }

                }
            }
            else
            {
                /**
                 * 
                 */
                $this->trace->update([
                    "status" => JobTrace::STATUS_SUSPEND,
                    "log" => "Google Client OAuth Token Not Found"
                ]);

                /**
                 * Retry a new jobs
                 */
                dispatch(new UploadYoutube($this->trace))->delay(Carbon::now()->addSeconds(3));
            }
        }
        catch (Exception $e)
        {
            $this->trace->update([
                "status" => JobTrace::STATUS_FAILED,
                "log" => "Unexpected error : " . $e->getFile() . $e->getMessage()
            ]);
        }
    }

    /**
     * 
     */
    public function failed(Exception $e)
    {
        $this->trace->update([
            "status" => JobTrace::STATUS_FAILED,
            "log" => "Unexpected error : " . $e->getMessage()
        ]);
    }
}
