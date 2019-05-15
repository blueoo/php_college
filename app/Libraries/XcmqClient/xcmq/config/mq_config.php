<?php

/**
 *  配置文件 
 *
 * @package config
 * @subpackage ./
 * @author  谢<jc3wish@126.com>
 */
return array(
	//test模块
	'test'=>array(
		'mq'=>'activemq',//用什么存储
		'base'=>'tcp://192.168.7.177:61613',
        'queue'=>array('test'=>'/queue/test','mytest2'=>'/queque/mytest2'),
		'format'=>'serialize', 
        'prefetchSize'=>1000,
        'persistent'=>true, //固化 true  不固化false
	),
    'testRabbit5'=>array(
		'mq'=>'rabbitmqorg',//用什么存储
		'base'=>array( 'host'=>'10.40.6.46' , 'port'=> '5672', 'login'=>'admin' , 'password'=> 'admin','vhost' =>'/'),
        'read_timeout'=>3,
        'write_timeout'=>3,
        'heartbeat'     =>90,
        'queue'=>array(
            'test'=>array('queue_name'=>'direct0000','durable'=>true,'auto_delete'=>false,'routingkey'=>'keydirect00000'),
            'test2'=>array('queue_name'=>'direct0001','durable'=>true,'auto_delete'=>true,'routingkey'=>'keydirect00001'),
            ),
        'delivery_mode'=>2, //2 消息持久化, 1 非持化，默认为 2
		'format'=>'json', // serialize(数据所有数据源有效),json(对数据进行json转换，数据源是数组的情况，否则失败) , string(不进行任何处理)
        'exchange'=>array('exchange_name'=>'ExChangedirect0000','durable'=>true,'auto_delete'=>false),
        'amqp_ex_type'=>'direct', //topic,headers(暂未知)   fanout(最快,和routingkey没有任何关系)  direct(按routingkey接收和发送) 
        'prefetchSize'=>0, //客户端将预取数据到大小
        'prefetchCount'=>1, //可以同时接收多少 条未ack的数据
        'reconnect'=>array(
            'write'=>array('count'=>0,'sleep'=>0.1), //只对没有开启事务写的有操作有效,如果 count次数内写入成功后，后面再次进行写入，失败又重新计算，默认为 0，sleep 默认为0.1
            // 消费线程重连次数，与sleep时间,默认30次，sleep 1秒
            'read' =>array('count'=>0,'sleep'=>1),
        ),
	), 
    
    
);