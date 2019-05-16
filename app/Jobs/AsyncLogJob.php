<?php

namespace App\Jobs;
use App\Services\LogService\LogService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Exception;

/**
 * @description:异步log任务的实现
 * Class AsyncLogJob
 * @package App\Jobs
 * @author zouhuaqiu
 * @date 2019/5/16
 */
class AsyncLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public $data;
    /**
     * AsyncLogJob constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;

    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $insert   = $this->data['insert'];
        $col_name = $this->data['col_name'];
        $connection = LogService::connectionMongodb($col_name);
        $connection ->insert($insert);

    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {


    }

}
