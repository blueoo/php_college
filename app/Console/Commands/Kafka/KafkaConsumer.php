<?php
/**
 * @description:
 * Created by IntelliJ IDEA.
 * @author zouhuaqiu
 * @date 2019/5/7
 */

namespace App\Console\Commands\Kafka;

use Illuminate\Console\Command;
use App\Services\KafkaService\KafkaConsumerFactory;

/**
 * @description:消费者示例
 * Class KafkaConsumer
 * @package App\Console\Commands\Kafka
 * @author zouhuaqiu
 * @date 2019/5/16
 */
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
        $config = [];
        $config['group.id'] = 'test_demo';
        $config['enable.auto.commit'] = 'false';
        $topic_config['auto.offset.reset'] = 'earliest';
        $consumer = KafkaConsumerFactory::factory(['test_demo'], 'default', $config, $topic_config);
        while (true) {
            $msg = $consumer->consume(120 * 1000);
            if (null === $msg) {
                continue;
            } elseif ($msg->err) {
                echo $msg->errstr(), "\n";
            } else {
                var_dump($msg);


                $consumer->commit();
            }

        }


    }


}