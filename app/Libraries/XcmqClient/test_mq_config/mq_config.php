<?php
return array(
    'SA' => array(
        'mq' => 'rabbitmqorg',//用什么存储
        'heartbeat' => 180,
        'base' => array('host' => '127.0.0.1', 'port' => '5672', 'login' => '*******', 'password' => '*********', 'vhost' => 'SYS_SA'),
        //'connect_timeout'=>2, // 获取队列数据时，没有数据的时候，等待服务端推数据过来的时间，默认永不过期，如果过期则会自动重新请求
        'queue' => array(
            'baseBrand_SOA' => array('queue_name' => 'baseBrand_SOA', 'durable' => true, 'auto_delete' => false, 'routingkey' => 'baseBrand')
        ),
        'delivery_mode' => 2, //2 消息持久化, 1 非持化，默认为 2
        'format' => 'json', // serialize(数据所有数据源有效),json(对数据进行json转换，数据源是数组的情况，否则失败) , string(不进行任何处理)
        'exchange' => array(
            array('exchange_name' => 'amq.direct', 'durable' => true, 'auto_delete' => false),
        ),
        'amqp_ex_type' => 'direct', //topic,headers(暂未知)   fanout(最快,和routingkey没有任何关系)  direct(按routingkey接收和发送)
        'reconnect' => array(
            'write' => array('count' => 0, 'sleep' => 0.1), //只对没有开启事务写的有操作有效,如果 count次数内写入成功后，后面再次进行写入，失败又重新计算，默认为 0，sleep 默认为0.1
            // 消费线程重连次数，与sleep时间,默认30次，sleep 1秒
            'read' => array('count' => 0, 'sleep' => 1),
        ),
        'write_read_log' => true,
    ),
);