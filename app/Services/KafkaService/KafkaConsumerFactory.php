<?php
/**
 * @description:
 * Created by IntelliJ IDEA.
 * @author zouhuaqiu
 * @date 2019/5/15
 */

namespace App\Services\KafkaService;

/**
 * @description: 消费者工厂
 * Class KafkaConsumerFactory
 * @package App\Services\KafkaService
 * @author zouhuaqiu
 * @date 2019/5/16
 */
class KafkaConsumerFactory
{
    private static $kafkaConsumerMap = [];

    public static function factory($topic, $conn = 'default',$config=[],$topicConfig = [])
    {
        $key = $topic . $conn;
        if (!array_key_exists($key, self::$kafkaConsumerMap)) {
            $producer = new KafkaConsumer();
            $producer->initConsumer($conn,$config);
            $producer->setTopic($topic,$topicConfig);
            self::$kafkaConsumerMap[$key] = $producer;
        }
        return self::$kafkaConsumerMap[$key];
    }

}