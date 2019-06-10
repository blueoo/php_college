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

    public function initConsumer($conn = 'default', $config = [], $TopicConfig = [])
    {
        $brokers = config('kafka.connections.' . $conn . '.brokers');
        $logLevel = config('kafka.connections.' . $conn . '.logLevel');

        $conf = new \RdKafka\Conf();

        $conf->setRebalanceCb(function (\RdKafka\KafkaConsumer $kafka, $err, array $partitions = null) {
            switch ($err) {
                case RD_KAFKA_RESP_ERR__ASSIGN_PARTITIONS:
//                    echo "Assign: ";
//                    var_dump($partitions);
                    $kafka->assign($partitions);
                    break;

                case RD_KAFKA_RESP_ERR__REVOKE_PARTITIONS:
//                    echo "Revoke: ";
//                    var_dump($partitions);
                    $kafka->assign(NULL);
                    break;

                default:
                    throw new \Exception($err);
            }
        });
        if (!empty($config)) {
            foreach ($config as $key => $value) {
                $conf->set($key, $value);
            }
        }
        $conf->set('metadata.broker.list', $brokers);
        $topicConf = new \RdKafka\TopicConf();
        if (!empty($TopicConfig)) {
            foreach ($TopicConfig as $key => $value) {
                $topicConf->set($key, $value);
            }
        }
        $conf->setDefaultTopicConf($topicConf);

        $this->consumer = new \RdKafka\KafkaConsumer($conf);

        return $this;
    }

    public function setTopic(array $topic)
    {
        $this->consumer->subscribe($topic);
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
    public function consume($timeout)
    {

        return $this->consumer->consume($timeout);
    }

}