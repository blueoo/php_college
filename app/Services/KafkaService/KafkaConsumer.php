<?php
/**
 * @description:
 * Created by IntelliJ IDEA.
 * @author zouhuaqiu
 * @date 2019/5/15
 */

namespace App\Services\KafkaService;


class KafkaConsumer
{
    private $consumer;
    private $topic;

    public function initConsumer($conn = 'default', $config = [])
    {
        $brokers = config('kafka.connections.' . $conn . '.brokers');
        $logLevel = config('kafka.connections.' . $conn . '.logLevel');
        if (!empty($config)) {
            $conf = new \RdKafka\Conf();
            foreach ($config as $key => $value) {
                $conf->set($key, $value);
            }
            $this->consumer = new \RdKafka\Consumer($conf);
        } else {
            $this->consumer = new \RdKafka\Consumer();
        }
        $this->consumer->setLogLevel($logLevel);
        $this->consumer->addBrokers($brokers);
        return $this;
    }

    public function setTopic($topic, $topicConfig = [])
    {
        if (is_null($this->consumer)) {
            throw new \Exception('Consumer must be init');
        }
        if (!empty($topicConfig)) {
            $topicConf = new RdKafka\TopicConf();
            foreach ($topicConfig as $key => $value) {
                $topicConf->set($key, $value);
            }
            $this->topic = $this->consumer->newTopic($topic, $topicConf);
        } else {

            $this->topic = $this->consumer->newTopic($topic);
        }
        return $this;
    }

    /**
     * @description:设置消费的分区和
     * @param $partition
     * @param $offsetMode
     * @return $this
     * @author zouhuaqiu
     * @date 2019/5/15
     */
    public function consumeStart($partition, $offsetMode)
    {

        $this->topic->consumeStart($partition, $offsetMode);
        return $this;
    }

    /**
     * @description:获取message
     * @param $partition
     * @param $timeout
     * @return mixed
     * @author zouhuaqiu
     * @date 2019/5/15
     */
    public function consume($partition, $timeout)
    {

        return $this->topic->consume($partition, $timeout);
    }

}