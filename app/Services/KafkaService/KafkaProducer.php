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

    /**
     * @description:初始化生产者，从config/kafka.php加载配置
     * @param string $conn
     * @param array $config
     * @return $this
     * @author zouhuaqiu
     * @date 2019/5/16
     */
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

    /**
     * @description:设置topic和queue不一样，topic可以同时被多个消费者订阅
     * @param $topic
     * @param array $topicConfig
     * @return $this
     * @throws \Exception
     * @author zouhuaqiu
     * @date 2019/5/16
     */
    public function setTopic($topic, $topicConfig = [])
    {
        if (is_null($this->producer)) {
            throw new \Exception('producer must be init');
        }
        if (!empty($topicConfig)) {
            $topicConf = new \RdKafka\TopicConf();
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

    /**
     * @description:Kafka轮询一次就相当于拉取（poll）一定时间段broker中可消费的数据，在这个指定时间段里拉取，时间到了就立刻返回数据。
     * @param $time_ms
     * @return $this
     * @author zouhuaqiu
     * @date 2019/5/16
     */
    public function poll($time_ms)
    {
        $this->producer->poll($time_ms);
        return $this;
    }

}