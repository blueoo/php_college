<?php
/**
 * @description:
 * Created by IntelliJ IDEA.
 * @author zouhuaqiu
 * @date 2019/5/7
 */

namespace App\Console\Commands\Kafka;

use Illuminate\Console\Command;

class KafkaConsumer extends Command
{
    protected $signature = 'command:consumer';
    protected $description = 'consumer';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $rk = new \RdKafka\Consumer();
        $rk->setLogLevel(LOG_DEBUG);
        $rk->addBrokers("10.19.2.223:9092,10.19.2.224:9092,10.19.2.225:9092");
        $topic = $rk->newTopic("test_test");
        $topic->consumeStart(0, RD_KAFKA_OFFSET_STORED);

        while (true) {
            // The first argument is the partition (again).
            // The second argument is the timeout.
            $msg = $topic->consume(0, 1000);
            if (null === $msg) {
                continue;
            } elseif ($msg->err) {
                echo $msg->errstr(), "\n";
            } else {
                var_dump($msg);
            }
        }


    }


}