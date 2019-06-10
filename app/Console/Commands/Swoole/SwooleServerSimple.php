<?php
/**
 * @description:
 * Created by IntelliJ IDEA.
 * @author zouhuaqiu
 * @date 2019/5/23
 */

namespace App\Console\Commands\Swoole;

use Illuminate\Console\Command;

class SwooleServerSimple extends Command
{
    protected $signature = 'command:swoole_server';
    protected $description = 'swoole_server';
    public $server;

    public function __construct()
    {
        parent::__construct();

    }


    public function handle()
    {
        $this->server = new \swoole_server('10.19.2.122', 9999);
        $this->server->set([
            'open_length_check' => true,
            'package_length_type' => "N", // 4个字节
            'package_length_offset' => 0,
            'package_body_start' => 4, // 表示只计算包体的长度，不包含长头的长度
            'package_max_length' => 80000, // 最大包长
            'pid_file' => storage_path('pid/') . 'server.pid',
            'worker_num' => 1,
            'max_request' => 5000,
            'task_worker_num' => 2,
            'task_max_request' => 1000,
        ]);

        //监听连接进入事件
        $this->server->on('connect', function ($server, $fd) {
            //echo "Client: Connect.\n";
        });

        //监听数据发送事件
        $this->server->on('receive', function ($server, $fd, $from_id, $data) {

            $info = unpack('N', $data);
            $len = $info[1];
            $body = substr($data, -$len);
            $rs = $this->server->task($data);
            if ($rs === false) {


            }

        });

        $this->server->on('task', function ($server, $taskId, $fromId, $data) {
            var_dump($taskId);
            var_dump(strlen($data));
            var_dump($data);
            $this->server->finish($data);


        });

        $this->server->on('finish', function ($server, $taskId, $data) {
            var_dump($taskId);
            echo "finish received data '{$data}'\n";
        });

        //监听连接关闭事件
        $this->server->on('close', function ($server, $fd) {
            //echo "Client: Close.\n";
        });
        echo "start.\n";
        //启动服务器
        $this->server->start();


    }
}