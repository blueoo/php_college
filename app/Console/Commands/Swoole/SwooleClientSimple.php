<?php
/**
 * @description:
 * Created by IntelliJ IDEA.
 * @author zouhuaqiu
 * @date 2019/5/23
 */

namespace App\Console\Commands\Swoole;

use Illuminate\Console\Command;

class SwooleClientSimple extends Command
{

    protected $signature = 'command:swoole_client';
    protected $description = 'swoole_client';


    public function __construct()
    {
        parent::__construct();

    }
    /**
     * @description:同步式短连接发送
     * @param $data
     * @throws \Exception
     * @author zouhuaqiu
     * @date 2019/5/25
     */
    public function singleSend($data)
    {
        $client = new \swoole_client(SWOOLE_SOCK_TCP);

        // 这里是连接
        $timeout = 10;
        if (!$client->connect('10.19.2.122', 9999, $timeout)) {
            throw new \Exception('connect error');
        }
        // 发送前判断一下是否已经连接上
        if ($client->isConnected()) {
            // 发送的时候将body的长度添加到包头
            $head = pack('N', strlen($data));
            $packet = $head . $data;
            $packLen = strlen($packet);
            $count = $client->send($packet);

            if ($count == $packLen) {
                // 发送长度等于你要发送的包长才算是真正的成功
                echo 'send success!:' . $count . PHP_EOL;
            }
            $client->close();
        }

    }

    /**
     * @description:长连接发的例子，先连接，再发送，发送完之后再去关闭
     * @param $data
     * @throws \Exception
     * @author zouhuaqiu
     * @date 2019/5/25
     */
    public function longConnect($data)
    {
        $client = new \swoole_client(SWOOLE_SOCK_TCP);

        $timeout = 10;
        if (!$client->connect('10.19.2.76', 9999, $timeout)) {
            throw new \Exception('connect error');
        }


        for ($i = 0; $i < 10000; $i++) {

            if ($client->isConnected()) {
                // 发送的时候将body的长度添加到包头
                $head = pack('N', strlen($data));
                $packet = $head . $data;
                $packLen = strlen($packet);
                $count = $client->send($packet);
                if ($count == $packLen) {
                    // 发送长度等于你要发送的包长才算是真正的成功
                    echo 'send success!:' . $count . PHP_EOL;
                }
            }

        }

        if ($client->isConnected()){
            $client->close();
        }



    }

    public function handle()
    {


    }
}