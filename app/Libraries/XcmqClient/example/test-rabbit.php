<?php
ini_set('display_errors','On'); 
error_reporting(E_ALL);
//error_reporting(E_ERROR | E_WARNING | E_PARSE);
//error_reporting(E_ERROR);
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
define('XCMQ_API_PATH',dirname(dirname(__FILE__)).'/xcmq');

/**
 * 加载必须文件
*/
class testclass{
    public function testmq($data,$obj){
        print_r($data);
        //print_r(json_decode($data));
        $obj->ack();
        //exit();
        return;
        if($data == 'it is test' || $data['key'] == 'it is test'){
            $obj->ack();
        }
    }
    
}
define('XCMQ_CONF_FILE',dirname(__FILE__).'/config/mq_config.php');

define('XLOGGER_CONF_PATH',dirname(__FILE__).'/config/xlogger');

require_once(dirname(dirname(__FILE__)).'/XcmqClient.php');

class testMq{
    
    
    function __construct(){
        $this->mqObj = XcmqClient::singleton('test01');
    }
    
    function start(){
        
        echo "soa_test7\r\n";
        
        $s = "2-piece 8\'\' nylon tongs set-purple,orange";
        $r=$this->mqObj->sendMQ(array('key'=>$s),'testqueued');
        
        return;
        $r=$this->mqObj->sendMQ(json_encode(array('key'=>'it is test')),'testqueued');
        
        $r=$this->mqObj->sendMQ(json_encode(array('key'=>'ssssssssssssssssss')),'testqueued');
        echo "soa_test7 over\r\n";
        
        $r=$this->mqObj->sendMQ(json_encode(array('key'=>'it is test')),null,'amq.fanout',array('delivery_mode'=>1));

        //$obj =new testclass();
        
        //$this->mqObj->receiveMQ('testqueued',array($obj,'testmq'));
        
        //$this->mqObj->receiveMQ('testqu.d',array($obj,'testmq'));
    }    
    
    public function testMaxlen(){

        $mqObj = XcmqClient::singleton('test01');
        
        
        echo 'test start : setConfirm=true'."\r\n";
        echo "rountingkey direct0000 true"."\r\n";
        $r=$mqObj->sendMQ(json_encode(array('key'=>'oooooooooo')),'stockWriteBack_GOODS');
        if(!$r){
            print_r($mqObj->getLastSendError());
        }
        echo 'test over : setConfirm=true'."\r\n";
        
    }    
    
}
$obj = new testMq();
//$obj->receiveMQ();
$obj->testMaxlen();
