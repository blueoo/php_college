<?php
/**
 * @description:
 * Created by IntelliJ IDEA.
 * @author zouhuaqiu
 * @date 2019/5/15
 */

namespace App\Services\KafkaService;


class KafkaProducer
{

    private $producer;
    private $topic;

    public function initProducer($conn = 'default', $config = [])
    {
        $brokers = config('kafka.connections.' . $conn . '.brokers');
        $logLevel = config('kafka.connections.' . $conn . '.logLevel');
        if (!empty($config)) {
            $conf = new \RdKafka\Conf();
            foreach ($config as $key => $value) {
                $conf->set($key, $value);
            }
            $this->producer = new \RdKafka\Producer($conf);
        } else {
            $this->producer = new \RdKafka\Producer();
        }
        $this->producer->setLogLevel($logLevel);
        $this->producer->addBrokers($brokers);
        return $this;
    }

    public function setTopic($topic, $topicConfig = [])
    {
        if (is_null($this->producer)) {
            throw new \Exception('producer must be init');
        }
        if (!empty($topicConfig)) {
            $topicConf = new RdKafka\TopicConf();
            foreach ($topicConfig as $key => $value) {
                $topicConf->set($key, $value);
            }
            $this->topic = $this->producer->newTopic($topic, $topicConf);
        } else {

            $this->topic = $this->producer->newTopic($topic);
        }
        return $this;
    }

    public function produceMessage($message)
    {
        if (is_null($this->topic)) {
            throw new \Exception('Topic must be set');
        }

        $this->topic->produce(RD_KAFKA_PARTITION_UA, 0, $message);
        return $this;
    }

    public function poll($time_ms)
    {
        $this->topic->poll($time_ms);
        return $this;
    }

}