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

if( !defined('XCORE_API_PATH')){
    define('XCORE_API_PATH',dirname(dirname(__FILE__)).'/xcore');
}

if( !defined('XCMQ_API_PATH') ){
    define('XCMQ_API_PATH',__DIR__);
}
/**
 * 加载必须文件
*/

require_once(XCORE_API_PATH.'/base.inc.php');

xstatic::setAlias('xcmq', XCMQ_API_PATH);
xstatic::setAlias('PhpAmqpLib', XCMQ_API_PATH.'/PhpAmqpLib');

//use xcmq\module\Activemq as Activemq;

use xcmq\Xcmq as Xcmq;



//日志模块
if( !defined('XLOGGER_API_PATH') ){
    define('XLOGGER_API_PATH',dirname(dirname(__FILE__)).'/xlogger');
}

require_once(XLOGGER_API_PATH.'/base.inc.php');

