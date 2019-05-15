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
if( !defined('XCORE_API_PATH') )
{
	define('XCORE_API_PATH',dirname(dirname(__FILE__)).'/xcore');;
}

require_once XCORE_API_PATH.'/static/xstatic.class.php';

if (!function_exists('__autoload')) {
    spl_autoload_register(array('xstatic', 'autoload'));
}else{
    spl_autoload_register(array('xstatic', 'autoload'));
}

xstatic::setAlias('mq', XCORE_API_PATH.'/mq');
xstatic::setAlias('factory', XCORE_API_PATH.'/factory');
xstatic::setAlias('orm', XCORE_API_PATH.'/orm');
xstatic::setAlias('nosql', XCORE_API_PATH.'/nosql');
 