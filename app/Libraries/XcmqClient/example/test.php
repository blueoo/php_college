<?php
date_default_timezone_set('PRC');
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
    
    public function receiveMQ(){
        echo "receiveMQ start\r\n";

        $obj =new testclass();
        
        $this->mqObj->receiveMQ('direct0000',array($obj,'testmq'));
        echo "receiveMQ end\r\n";
    }

    public function test01(){
        echo "\r\n";
        echo "test01 start \r\n";
        echo "\r\n";
        //$this->mqObj->setConfirm(true);
        echo 'test start : setConfirm=true'."\r\n";
        echo "rountingkey direct0000 true"."\r\n";
        $d = json_encode(array('key'=>'oooooooooo11111'));
        $r=$this->mqObj->sendMQ($d,'direct00002');
        if(!$r){
            print_r($this->mqObj->getLastSendError());
        }
        echo 'test01 over : setConfirm=true'."\r\n";
    }

    public function test02(){
        echo "\r\n";
        echo "test02 start \r\n";
        echo "\r\n";
        echo "batchPublish rountingkey direct0000 true"."\r\n";
        
        $d = array(
            json_encode(array('key'=>'c1')),
            json_encode(array('key'=>'c2')),
            json_encode(array('key'=>'c3')),
        );
        
        for($i=1;$i<1000;$i++){
            $d[]=json_encode(array('key'=>'c'.rand()));
        }
        $d[] = json_encode(array('key'=>'cover'));
        $r = $this->mqObj->batchPublish($d,'direct0000');

        if($r !== true){
            print_r($r);
        }
        echo 'test02 over : setConfirm=true'."\r\n";
    }
    
    public function testBatchRand(){
        echo "\r\n";
        echo "\r\n";
        
        if(rand(0,1)){
            $confirm  = true;
            $confirms = 'true';
        }else{
            $confirm  = false;
            $confirms = 'false';
        }
        
        if(rand(0,1)){
            $rountingkey  = 'direct0000';
        }else{
            $rountingkey  = 'direct00002';
        }
        
        $this->mqObj->setConfirm($confirm);
        echo "test start : setConfirm=$confirms"."\r\n";
        echo "batchPublish rountingkey $rountingkey"."\r\n";
        
        $d = array(
            json_encode(array('key'=>'c'.rand())),
            json_encode(array('key'=>'c'.rand())),
            json_encode(array('key'=>'c'.rand())),
        );
        //$mqObj->setAttributes(array('priority'=>10));
        $r = $this->mqObj->batchPublish($d,$rountingkey);
        
        if($r !== true){
            print_r($d);
            print_r($r);
        }
        echo "test over : setConfirm=$confirms"."\r\n";
    }
    
    
    public function testRand(){
        echo "\r\n";
        echo "\r\n";
        
        if(rand(0,1)){
            $confirm  = true;
            $confirms = 'true';
        }else{
            $confirm  = false;
            $confirms = 'false';
        }
        
        if(rand(0,1)){
            $rountingkey  = 'direct0000';
        }else{
            $rountingkey  = 'direct00002';
        }
        $this->mqObj->setConfirm($confirm);
        
        echo "test start : setConfirm=$confirms"."\r\n";
        echo "rountingkey $rountingkey"."\r\n";
        $d = json_encode(array('key'=>'oooooooooo'.rand()));
        $r=$this->mqObj->sendMQ($d,$rountingkey);
        if(!$r){
            print_r("\r\n".$d."\r\n");
            print_r($this->mqObj->getLastSendError());
        }
        echo "test over : setConfirm=$confirms"."\r\n";
    }
    
    public function testBacthRand2(){
        echo "\r\n";
        echo "\r\n";
        
        if(rand(0,1)){
            $confirm  = true;
            $confirms = 'true';
        }else{
            $confirm  = false;
            $confirms = 'false';
        }

        $this->mqObj->setConfirm($confirm);
        $this->mqObj->setBatchPublish(true);

        echo "test start : setConfirm=$confirms"."\r\n";
        #echo "batchPublish rountingkey $rountingkey"."\r\n";
        
        $d = array(
            json_encode(array('key'=>'c'.rand())),
            json_encode(array('key'=>'c'.rand())),
            json_encode(array('key'=>'c'.rand())),
        );
        foreach($d as $k=>$v){
            if(rand(0,1)){
                $rountingkey  = 'direct0000';
            }else{
                $rountingkey  = 'direct00002';
            }
            echo "batchSend rountingkey $rountingkey"."\r\n";
            $r=$this->mqObj->sendMQ($v,$rountingkey);
        }
        
            try{
                $r=$this->mqObj->waitConfirm();
            }catch (\Exception $e){
                print_r($e->getMessage());
                $r=$d;
            }
            
            if($r !== true){
                print_r($d);
                print_r($r);
            }
        
        echo "test over : setConfirm=$confirms"."\r\n";
        
        $this->mqObj->setBatchPublish(false);
    }
    
    
    public function testBacthRand3(){
       
        
        $confirm  = false;
        $confirms = 'false';

        $this->mqObj->setConfirm($confirm);
        $this->mqObj->setBatchPublish(true);

        echo "test start : setConfirm=$confirms"."\r\n";
        #echo "batchPublish rountingkey $rountingkey"."\r\n";
        
        $d = array(
            json_encode(array('key'=>'c'.rand())),
            json_encode(array('key'=>'c'.rand())),
            json_encode(array('key'=>'c'.rand())),
        );

        $rountingkey  = 'direct0000';
        echo "batchSend rountingkey $rountingkey"."\r\n";
        echo $d[0]."\r\n";
        $r=$this->mqObj->sendMQ($d[0],$rountingkey);
        
        $rountingkey  = 'direct00002';
        echo "batchSend rountingkey $rountingkey"."\r\n";
        echo $d[1]."\r\n";
        $r=$this->mqObj->sendMQ($d[1],$rountingkey);
        $rountingkey  = 'direct00002';
        echo "batchSend rountingkey $rountingkey"."\r\n";
        echo $d[2]."\r\n";
        $r=$this->mqObj->sendMQ($d[2],$rountingkey);
        
        try{
            $r=$this->mqObj->waitConfirm();
        }catch (\Exception $e){
            print_r($e->getMessage());
            $r=$d;
        }
        
        if($r !== true){
            print_r($d);
            print_r($r);
        }
        
        echo "test over : setConfirm=$confirms"."\r\n";
        
        $this->mqObj->setBatchPublish(false);
        

       echo "\r\n";
        echo "\r\n";
        
        $confirm  = true;
        $confirms = 'true';

        $this->mqObj->setConfirm($confirm);
        $this->mqObj->setBatchPublish(true);
        
        echo "test start : setConfirm=$confirms"."\r\n";
        #echo "batchPublish rountingkey $rountingkey"."\r\n";
        
        $d = array(
            json_encode(array('key'=>'c'.rand())),
            json_encode(array('key'=>'c'.rand())),
            json_encode(array('key'=>'c'.rand())),
        );
        foreach($d as $k=>$v){
            if(rand(0,1)){
                $rountingkey  = 'direct0000';
            }else{
                $rountingkey  = 'direct00002';
            }
            $rountingkey  = 'direct00002';
            echo $v."\r\n";
            echo "batchSend rountingkey $rountingkey"."\r\n";
            $r=$this->mqObj->sendMQ($v,$rountingkey);
        }
        

            try{
                $r=$this->mqObj->waitConfirm();
            }catch (\Exception $e){
                print_r($e->getMessage());
                $r=$d;
            }
            
            if($r !== true){
                print_r($d);
                print_r($r);
            }
        
        echo "test over : setConfirm=$confirms"."\r\n";
        
        $this->mqObj->setBatchPublish(false);
    }
    
    
    public function testConnectTimeOut(){
        echo "\r\n";
        echo "\r\n";
        echo "test write connect time out start\r\n";
        //$this->mqObj->setConfirm(false);
        $time = 120;
        echo "sleep $time s start".date('Y-m-d H:i:s')."\r\n";
        sleep($time);
        echo "sleep $time s over".date('Y-m-d H:i:s')."\r\n";
        $this->test01();

        echo "test write connect time out over\r\n";
    }
}


$obj = new testMq();
//echo "start test \r\n";
//$obj->testConnectTimeOut();
//exit();
/*test write_timeout的情况下会不会异常*/

/*start*/
/*请把 xcmq\module\Xcmq_RabbitMQORG 356,441 两行注释去掉*/
/*
$obj->test02();
$obj->test01();
$obj->test02();
$obj->test01();

exit('test over');
*/
/*over*/

//$obj->receiveMQ();
//$obj->testBacthRand3();
//exit();
/*
$obj->mqObj->setAttributes(array('expiration'=>5000));
for($i = 0;$i<10;$i++){
    $a = rand(0,2);
    if($a==0){
        $obj->testRand();
    }else if($a == 1){
        $obj->testBatchRand();
    }else{
        $obj->testBacthRand2();
    }
}
echo "\r\n";
echo "phpversion:".phpversion()."\r\n";
if(function_exists('pcntl_signal')){
    echo "pcntl:true";
}else{
    echo "pcntl:false";
}

echo "\r\n";
echo "\r\n";

$obj->testConnectTimeOut();

$obj->receiveMQ();

*/

$mqObj = XcmqClient::singleton('test01');

/*
echo "事务\r\n";
$mqObj->setConfirm(false);
$mqObj->begin();
$mqObj->setMsgKey('orderId251133')->sendMQ('test msg11111','testqueued');
$mqObj->setMsgKey('orderId251134')->sendMQ('test msg222111','testqueued');
$mqObj->setMsgKey('orderId251135')->sendMQ('test msg233111','testqueued');
$r=$mqObj->commit();

var_dump($r);

*/

echo "逐个提交 批量\r\n";

$mqObj->setBatchPublish(true);
$mqObj->setMsgKey('orderId251144')->sendMQ('test msg11','testqueued');
$mqObj->setMsgKey('orderId251145')->sendMQ('test msg222','testqueued');
$mqObj->setMsgKey('orderId251146')->sendMQ('test msg233"','testqueued');
$r = $mqObj->waitConfirm();
var_dump($r);


$mqObj->receiveMQ('testqueued',function($data,$obj){
    print_r($data);
    $obj->ack();
    return;
});

exit();

exit();


$mqObj->setConfirm(true);

echo "单个提交\r\n";

$r = $mqObj->sendMQ('test msg','testqueued');

$r = $mqObj->setMsgKey('orderId251121')->sendMQ('test msg','testqueued');

var_dump($r);

//批量
echo "批量\r\n";
$data = array(
    array(
        'Body'=>'batch test msg1',
        'MsgKey'=>'orderId25111',
    ),
    array(
        'Body'=>'batch test msg2',
        'MsgKey'=>'orderId25222',
    ),
);
$r = $mqObj->setMsgKey('orderId251122')->batchPublish($data,'testqueued');
var_dump($r);

