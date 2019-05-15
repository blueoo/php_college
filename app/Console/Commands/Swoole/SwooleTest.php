<?php
/**
 * @description:
 * Created by IntelliJ IDEA.
 * @author zouhuaqiu
 * @date 2019/5/6
 */

namespace App\Console\Commands\Swoole;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SwooleTest extends Command
{

    /**
     * 引入进程池模块
     */
    use BaseProcessPool;

    protected $signature = 'command:swoole_test';
    protected $description = 'swoole_test';
    /**
     * @var int 消息队列的key，不同的进程池请务必使用不同的key
     */
    public static $queueKey = 0x70001;


    public function __construct()
    {
        parent::__construct();

    }

    public function handle()
    {
        // 工作的进程数
        $workerNum = 2;
        // 用来记录主进程pid，可以用来管理
        $pidFile = storage_path('pid/') . 'test.pid';
        // 初始化进程池
        $this->init($workerNum, self::$queueKey, $pidFile);
        // 启动进程池，这个函数会一直hold住
        $this->startPool();

    }

    /**
     * @description: 子进程结束时候会调用这里
     * @param $pool
     * @param $workerId
     * @author zouhuaqiu
     * @date 2019/5/8
     */
    public function onStop($pool, $workerId)
    {
        // TODO: Implement onStop() method.
        echo "Worker#{$workerId} is stopped\n";


    }

    /**
     * @description:信息接收时会调用，消息投递可以使用静态方法dispatch
     * 例子：
     * SwooleTest::dispatch(SwooleTest::$queueKey,json_encode(['jay'=>'hello']));
     *
     * @param $pool
     * @param $message
     * @author zouhuaqiu
     * @date 2019/5/8
     */
    public function onMessage($pool, $message)
    {
        // TODO: Implement onMessage() method.
        echo "Message: {$message}\n";

    }

    /**
     * @description:子进程启动时候会调用一次
     * @param $pool
     * @param $workerId
     * @author zouhuaqiu
     * @date 2019/5/8
     */
    public function onStart($pool, $workerId)
    {
        // TODO: Implement onStart() method.
        echo "Worker#{$workerId} is started\n";


    }
}