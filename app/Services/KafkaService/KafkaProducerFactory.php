<?php
/**
 * @description:
 * Created by IntelliJ IDEA.
 * @author zouhuaqiu
 * @date 2019/5/15
 */

namespace App\Services\KafkaService;

/**
 * @description:kafka生产者封装
 * Class KafkaProducerFactory
 * @package App\Services\KafkaService
 * @author zouhuaqiu
 * @date 2019/5/15
 */
class KafkaProducerFactory
{
    private static $kafkaProducerMap = [];

    public static function factory($topic, $conn = 'default',$config=[],$topicConfig = [])
    {
        $key = $topic . $conn;
        if (!array_key_exists($key, self::$kafkaProducerMap)) {
            $producer = new KafkaProducer();
            $producer->initProducer($conn,$config);
            $producer->setTopic($topic,$topicConfig);
            self::$kafkaProducerMap[$key] = $producer;
        }
        return self::$kafkaProducerMap[$key];
    }

}