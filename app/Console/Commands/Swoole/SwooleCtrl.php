<?php
/**
 * @description:
 * Created by IntelliJ IDEA.
 * @author zouhuaqiu
 * @date 2019/5/7
 */

namespace App\Console\Commands\Swoole;

use Illuminate\Console\Command;

/**
 * @description: 用于优雅控制swoole的进程开启和关闭
 * Class SwooleCtrl
 * @package App\Console\Commands
 * @author zouhuaqiu
 * @date 2019/5/15
 */
class SwooleCtrl extends Command
{
    protected $signature = 'command:swoole_ctrl {pid_file} {cmd}';
    protected $description = 'swoole_ctrl';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $pidFile = storage_path('pid/') . $this->argument('pid_file');
        $cmd = $this->argument('cmd');

        if ($cmd == 'stop') {
            $this->shutdown($pidFile);
        }

        if ($cmd == 'reload') {
            $this->reload($pidFile);
        }

    }

    /**
     * @description: 关闭进程
     * @param $pidFile
     * @return bool
     * @author zouhuaqiu
     * @date 2019/5/15
     */
    public function shutdown($pidFile)
    {
        if (file_exists($pidFile)) {
            $pid = file_get_contents($pidFile);

            if (!\swoole_process::kill($pid, 0)) {
                echo "PID :{$pid} not exist \n";
                return false;
            }
            $sig = SIGTERM;
            \swoole_process::kill($pid, $sig);
            //等待5秒
            $time = time();
            $flag = false;
            while (true) {
                usleep(1000);
                if (!\swoole_process::kill($pid, 0)) {
                    echo "server stop at " . date("y-m-d h:i:s") . "\n";
                    if (is_file($pidFile)) {
                        unlink($pidFile);
                    }
                    $flag = true;
                    break;
                } else {
                    if (time() - $time > 5) {
                        echo "stop server fail.try again \n";
                        break;
                    }
                }
            }
            return $flag;
        } else {
            echo "pid 文件不存在，请执行查找主进程pid,kill!\n";
            return false;
        }
    }

    /**
     * @description:重启进程
     * @param $pidFile
     * @author zouhuaqiu
     * @date 2019/5/15
     */
    public function reload($pidFile)
    {
        if (file_exists($pidFile)) {
            $sig = SIGUSR1;
            $pid = file_get_contents($pidFile);
            if (!\swoole_process::kill($pid, 0)) {
                echo "pid :{$pid} not exist \n";
                return;
            }
            \swoole_process::kill($pid, $sig);
            echo "send server reload command at " . date("y-m-d h:i:s") . "\n";
        } else {
            echo "pid 文件不存在，请执行查找主进程pid,kill!\n";
        }
    }
}