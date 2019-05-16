<?php
/**
 * @description:
 * Created by IntelliJ IDEA.
 * @author zouhuaqiu
 * @date 2019/4/19
 */

namespace App\Http\Controllers\Index;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\LogService\LogService;
use App\Services\KafkaService\KafkaProducerFactory;

class IndexController extends Controller
{
    public function __construct()
    {


    }


    /**
     * @description:LogService 测试
     * @author zouhuaqiu
     * @date 2019/5/16
     */
    public function testLog()
    {
        $col_name = 'demo_log_' . date('Ym');
        $data = [
            'type' => 'test_log',
            'log' => ['hello'=>'world']
        ];
        //异步写log
        LogService::asyncLog($data, $col_name);
        //同步写log
        $data = [
            'type' => 'test_log_2',
            'log' => ['hello'=>'world!!!']
        ];
        LogService::mongoLog($data, $col_name);
        return 'testlog';
    }


    /**
     * @description:测试kafka生产者
     * @param Request $request
     * @return string
     * @author zouhuaqiu
     * @date 2019/5/15
     */
    public function kafkaProducer(Request $request)
    {
        $producer = KafkaProducerFactory::factory('test_test');

        $producer->produceMessage('ee');

        return 'hello';
    }


    /**
     * @description: 测试AMQP的用法，用于发送rabbitmq类库
     * @param Request $request
     * @return string
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     * @author zouhuaqiu
     * @date 2019/5/15
     */
    public function testAMQP(Request $request)
    {

        $conn = [
            // Rabbitmq 服务地址
            'host' => '127.0.0.1',
            // Rabbitmq 服务端口
            'port' => '5672',
            // Rabbitmq 帐号
            'login' => '*****',
            // Rabbitmq 密码
            'password' => '******',
            'vhost' => 'SYS_SA'
        ];

        //创建连接和channel
        $conn = new \AMQPConnection($conn);
        if (!$conn->connect()) {
            die("Cannot connect to the broker!\n");
        }
        $channel = new \AMQPChannel($conn);

        // 用来绑定交换机和队列
        $routingKey = 'productPrice_EBAY';

        $ex = new \AMQPExchange($channel);
        //  交换机名称
        $exchangeName = 'amq.direct';
        $ex->setName($exchangeName);

        // 设置交换机类型
        $ex->setType(AMQP_EX_TYPE_DIRECT);
        // 设置交换机是否持久化消息
        $ex->setFlags(AMQP_DURABLE);

        $ex->publish(json_encode(['hello' => 'world']), $routingKey);


        return "test";

    }

    public function export()
    {


        return 'export';
    }
}