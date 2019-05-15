<?php

/**
 *  配置文件 
 *
 * @package config
 * @subpackage ./
 * @author  谢<jc3wish@126.com>
 */
return array(
    'test01'=>array(
		'mq'=>'rabbitmqorg',//用什么存储
		'base'=>array( 'host'=>'10.40.6.89' , 'port'=> '5672', 'login'=>'testamdin111' , 'password'=> 'testamdin111','vhost' =>'testvhost'),
        'connect_timeout'=>30,
        'write_timeout'=>3,
        'heartbeat'=>100,
        'keepalive' => false,
        'confirm_select'=>false,
        'queue'=>array(
            'testqu.d'=>array('queue_name'=>'testqu.d.*','is_tmp'=>true,'exchange'=>'amq.fanout','auto_ack'=>true,),
            'testqueued'=>array('queue_name'=>'testqueued','durable'=>true,'auto_delete'=>false,'routingkey'=>'testqueued',),  //'x-expires'=>15000, exchange_name default amq.direct
            'direct0000'=>array('queue_name'=>'direct0000','durable'=>true,'auto_delete'=>false,'routingkey'=>'direct0000'),
            //'autodetelttest'=>array('queue_name'=>'autodetelttest','durable'=>true,'auto_delete'=>true,'routingkey'=>'testqueued'),
            //'testqueued2'=>array('queue_name'=>'testqueued2','durable'=>true,'auto_delete'=>false,'routingkey'=>'testqueued','exchange_name'=>'amq.direct'),
         ),
         
        'delivery_mode'=>2, //2 消息持久化, 1 非持化，默认为 2
        'mandatory' => true,
		//'format'=>'json', // serialize(数据所有数据源有效),json(对数据进行json转换，数据源是数组的情况，否则失败) , string(不进行任何处理)
        'exchange'=>array(
            //array('exchange_name'=>'amq.direct','durable'=>true,'auto_delete'=>false,'exchange_type'=>'direct'),
            //array('exchange_name'=>'amq.fanout','durable'=>true,'auto_delete'=>false,'exchange_type'=>'fanout'),
            array('exchange_name'=>'testexhchange'),
         ),
         'reconnect'=>array(
            //'read'=>array('sleep'=>1,'count'=>2),
         ),
	),
    
    //192.168.0.110
    'test02'=>array(
        'mq'=>'rabbitmqorg',
        'base'=>array('host'=>'10.40.6.89','port'=>'5672','login'=>'web_pm_test','password'=>'web_pm_test','vhost'=>'PDM'),
        'queue'=>array(
            'publish_PDM'=>array('queue_name'=>'publish_PDM','durable'=>true,'auto_delete'=>false,'routingkey'=>'publish_PDM','exchange_name'=>'amq.direct'),
            'publish_PM'=>array('queue_name'=>'publish_PM','durable'=>true,'auto_delete'=>false,'routingkey'=>'publish_PM','exchange_name'=>'amq.direct'),
        ),
        'exchange'=>array(
            'amq.direct'=>array('exchange_name'=>'amq.direct','exchange_type'=>'direct','durable'=>true,'auto_delete'=>false),
        ),
        'delivery_mode'=>2,
        'format'=>'string',
    ),
    
);
