<?php

/**
 *  配置文件 
 *
 * @package config
 * @subpackage ./
 * @author  谢<jc3wish@126.com>
 */
return array(
    'trail'=>array(
        'db'=>'file',//用什么存储
        'base'=>dirname(__FILE__),
        'rule'=>array(
            'class'=>'xlogger\rule\XLogger_Rule_Time',
            'param'=>array(
                'prefix'=>'trail',
                'partition'=>'Y-m-d',// Y-m-d
                'database'=>0,
            ),
            'key'=>'addTime',
            ),
        'format'=>'json',
    ),    
    'sendMQ'=>array(
        'db'=>'file',//用什么存储
        'base'=>dirname(__FILE__),
        'rule'=>array(
            'class'=>'xlogger\rule\XLogger_Rule_Time',
            'param'=>array(
                'prefix'=>'sendMQ',
                'partition'=>'Y-m-d',// Y-m-d
                'database'=>0,
            ),
            'key'=>'addTime',
            ),
        'format'=>'json',
    ),

    'receiveMQ'=>array(
        'db'=>'file',//用什么存储
        'base'=>dirname(__FILE__),
        'rule'=>array(
            'class'=>'xlogger\rule\XLogger_Rule_Time',
            'param'=>array(
                'prefix'=>'receiveMQ',
                'partition'=>'Y-m-d',// Y-m-d
                'database'=>0,
            ),
            'key'=>'addTime',
            ),
        'format'=>'json',
    ),
    
    'connect_error'=>array(
        'db'=>'file',//用什么存储
        'base'=>dirname(__FILE__),
        'rule'=>array(
            'class'=>'xlogger\rule\XLogger_Rule_Time',
            'param'=>array(
                'prefix'=>'connect_error',
                'partition'=>'Y-m-d',// Y-m-d
                'database'=>0,
            ),
            'key'=>'addTime',
            ),
        'format'=>'json',
    ),       
);