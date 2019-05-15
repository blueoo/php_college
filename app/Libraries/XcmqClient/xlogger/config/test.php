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
		'db'=>'redis',//用什么存储
		'base'=>array(
			'host'=>'localhost',
			'port'=>6379,
		),
		'rule'=>array(
			'class'=>'xlogger\rule\XLogger_Rule_Time',
			'param'=>array(
				'prefix'=>'test',
				'partition'=>'Y-m',// Y-m-d
				'database'=>0,
			),
			'key'=>'addTime',
		),
		'format'=>'json', //目前只支持这格式 并且默认也是这个
	),
	'test2'=>array(
		'db'=>'file',//用什么存储
		'base'=>dirname(__FILE__),
		'rule'=>array(
			'class'=>'xlogger\rule\XLogger_Rule_Time',
			'param'=>array(
				'prefix'=>'test',
				'partition'=>'Y-m-d',// Y-m-d
				'database'=>0,
			),
			'key'=>'addTime',
		),
		'format'=>'json',
	),
	
	'test3'=>array(
		'db'=>'mongodb',//用什么存储
		'base'=>'mongodb://127.0.0.1:27017',
		'rule'=>array(
			'class'=>'xlogger\rule\XLogger_Rule_Time',
			'param'=>array(
				'prefix'=>'test',
				'partition'=>'Y-m-d',// Y-m-d
				'database'=>'logtestdb',
			),
			'key'=>'addTime',
		),
		'format'=>'json',
	),

	'test4'=>array(
		'db'=>'file',//用什么存储
		'base'=>dirname(__FILE__),
		'rule'=>array(
			'class'=>'xlogger\rule\XLogger_Rule_Long',
			'param'=>array(
				'prefix'=>'test',
				'tableCount'=>10,
				'database'=>'logtestdb',
			),
			'key'=>'userId',
		),
		'format'=>'json',
		'enable'=>true
	),
);