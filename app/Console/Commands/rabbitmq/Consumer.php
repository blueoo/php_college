<?php
/**
 * @description:
 * Created by IntelliJ IDEA.
 * @author zouhuaqiu
 * @date 2019/6/5
 */

namespace App\Console\Commands\rabbitmq;


use Illuminate\Console\Command;

class Consumer extends Command
{
    protected $signature = 'command:rabbit';
    protected $description = 'test_rabbit';


    public function __construct()
    {
        parent::__construct();

    }

    public function handle()
    {
        $conn_args = array(
            'host' => '10.19.2.122',
            'port' => '5672',
            'login' => 'phper',
            'password' => 'phper',
            'vhost' => '/'
        );
        $e_name = 'amq.direct'; //交换机名
        $q_name = 'queue'; //队列名
        $k_route = 'key_1'; //路由key

//创建连接和channel
        $conn = new \AMQPConnection($conn_args);
        if (!$conn->connect()) {
            die("Cannot connect to the broker!\n");
        }
        $channel = new \AMQPChannel($conn);

        //创建交换机
        $ex = new \AMQPExchange($channel);
        $ex->setName($e_name);
        $ex->setType(AMQP_EX_TYPE_DIRECT); //direct类型
        $ex->setFlags(AMQP_DURABLE); //持久化
        echo "Exchange Status:" . $ex->declareExchange() . "\n";

        //创建队列
        $q = new \AMQPQueue($channel);
        $q->setName($q_name);
        $q->setFlags(AMQP_DURABLE); //持久化
        echo "Message Total:" . $q->declareQueue() . "\n";

        //绑定交换机与队列，并指定路由键
        echo 'Queue Bind: ' . $q->bind($e_name, $k_route) . "\n";

        //阻塞模式接收消息
        echo "Message:\n";
        while (True) {

            $q->consume(array($this,'processMessage'));
            //$q->consume('processMessage', AMQP_AUTOACK); //自动ACK应答
            $q->ack();
        }
        $conn->disconnect();
    }

    public function processMessage($envelope, $queue)
    {
        $msg = $envelope->getBody();
        echo $msg . "\n"; //处理消息
        $queue->ack($envelope->getDeliveryTag()); //手动发送ACK应答
    }
}