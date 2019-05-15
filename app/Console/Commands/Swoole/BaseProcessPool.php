<?php
/**
 * @description:
 * Created by IntelliJ IDEA.
 * @author zouhuaqiu
 * @date 2019/5/8
 */

namespace App\Console\Commands\Swoole;

/** 进程池基础类 ，基于swoole的进程池实现
 * Trait BaseProcessPool
 * @package App\Console\Commands
 */
trait BaseProcessPool
{
    public $pool;
    public $processNum;

    public function init($processNum, $queueKey, $pidFile = null)
    {
        $this->processNum = $processNum;
        $this->pool = new \Swoole\Process\Pool($this->processNum, SWOOLE_IPC_MSGQUEUE, $queueKey);
        $this->pool->on('WorkerStart', array($this, 'onStart'));
        $this->pool->on('Message', array($this, 'onMessage'));
        $this->pool->on('WorkerStop', array($this, 'onStop'));

        if (!empty($pidFile)) {
            $pid = posix_getpid();
            $file = fopen($pidFile, "w") or die("Unable to open file!");
            fwrite($file, $pid);
            fclose($file);
        }

    }


    public function startPool()
    {

        $this->pool->start();
    }

    public abstract function onStop($pool, $workerId);

    public abstract function onMessage($pool, $message);

    public abstract function onStart($pool, $workerId);


    public static function dispatch($queueKey, string $data)
    {
        $mq = new \Swoole\MsgQueue($queueKey);
        $mq->push($data);
    }


}