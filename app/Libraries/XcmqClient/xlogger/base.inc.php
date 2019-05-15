<?php
/**
 *  API  文件基础文件 
 *
 * @package api
 * @subpackage ./
 * @author  谢<jc3wish@126.com>
 */

 /*
	配置　API　目录
 */

if( !defined('XCORE_API_PATH') ){
	define('XCORE_API_PATH',dirname(dirname(__FILE__)).'/xcore');
} 

if( !defined('XLOGGER_API_PATH') ){
	define('XLOGGER_API_PATH',__DIR__);
}
/**
 * 加载必须文件
*/

require_once(XCORE_API_PATH.'/base.inc.php');

xstatic::setAlias('xlogger', XLOGGER_API_PATH);
/*
use xlogger\module\XLogger_Redis as XLogger_Redis;
use xlogger\module\XLogger_File as XLogger_File;
use xlogger\module\XLogger_Mongodb as XLogger_Mongodb;
*/
use xlogger\module\XLogger as XLogger;
