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
use App\Libraries\ConsistentHash\ConsistentHashFactory;
use Illuminate\Support\Facades\Redis;
use App\Console\Commands\Swoole\SwooleTest;
use App\Console\Commands\Swoole\SwooleClientSimple;
use App\Console\Commands\Swoole\SwooleClientLib;
use Eureka\EurekaClient;
class IndexController extends Controller
{
    private $client;

    public function __construct(
        SwooleClientSimple $client
    )
    {
        $this->client = $client;

    }


    public function testEureka()
    {
        $client = new EurekaClient([
            'eurekaDefaultUrl' => 'http://10.19.2.76:8761/eureka',
            'hostName' => 'zhq.demo.com',
            'appName' => 'php_service',
            'ip' => '10.19.2.122',
            'port' => ['80', true],
            'homePageUrl' => 'http://zhq.demo.com',
            'statusPageUrl' => 'http://zhq.demo.com/info',
            'healthCheckUrl' => 'http://zhq.demo.com/health'
        ]);
        $client->register();
        return 'test';
    }

    public function testClient()
    {

        try {

            $this->client->singleSend(str_repeat('A', 1000));


//            $this->client->longConnect(json_encode(['hello' => 'world']));
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        return "hh";
    }

    /**
     * @description:swoole进程池 投递任务
     * @author zouhuaqiu
     * @date 2019/5/16
     */
    public function testSwoole()
    {
        SwooleTest::dispatch(SwooleTest::$queueKey, json_encode(['jay' => 'hello']));
        return 'testSwoole';
    }

    /**
     * @description: 简化Flexihash的一致性hash实现
     * @return mixed
     * @author zouhuaqiu
     * @date 2019/5/16
     */
    public function testConsistentHash()
    {
        $total = 100; // 总的分表数目
        $user_id = 102321; // 这里举个user_id的例子
        $table_num = ConsistentHashFactory::getHashFactory($total)->lookup($user_id);

        return $table_num;
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
            'log' => ['hello' => 'world']
        ];
        //异步写log
        LogService::asyncLog($data, $col_name);
        //同步写log
        $data = [
            'type' => 'test_log_2',
            'log' => ['hello' => 'world!!!']
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
        $producer = KafkaProducerFactory::factory('test_demo');

        $producer->produceMessage('kafka');
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
            'host' => '10.19.2.122',
            // Rabbitmq 服务端口
            'port' => '5672',
            // Rabbitmq 帐号
            'login' => 'phper',
            // Rabbitmq 密码
            'password' => 'phper',
            'vhost'=>'/'
        ];

        //创建连接和channel
        $conn = new \AMQPConnection($conn);
        if (!$conn->connect()) {
            die("Cannot connect to the broker!\n");
        }
        $channel = new \AMQPChannel($conn);

        $k_route = 'key_1';

        $ex = new \AMQPExchange($channel);
        //  交换机名称
        $exchangeName = 'amq.direct';
        $ex->setName($exchangeName);
        // 设置交换机类型
        $ex->setType(AMQP_EX_TYPE_DIRECT);
        // 设置交换机是否持久化消息
        $ex->setFlags(AMQP_DURABLE);

        $ex->publish('hello_world', $k_route);
        return "test";

    }

    public function testRedis()
    {
        Redis::set('test_name_sen', 'Taylor!!');
        $name = Redis::get('test_name_sen');
        var_dump($name);

        return 'testRedis';
    }

    public function export()
    {

        return 'export';
    }

    public function import()
    {

        return 'import';
    }


}