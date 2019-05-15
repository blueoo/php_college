<?php 
namespace xcmq\module;
use PhpAmqpLib\Connection\AMQPStreamConnection as AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

/**
 *  Xcmq_RabbitMQORG 类
 *
 * @package xcmq
 * @subpackage xcmq.module
 * @author  谢<jc3wish@126.com>
 * 
 * $Id$
 */
class Xcmq_RabbitMQORG extends Xcmq_Abstract
{
    /**
    * @var 连接对象
    */
    protected $_conn          = NULL;
    
    //连接及配置参数
    protected $_config        = array();
    
    //消息队列载体对象池
    
    private $_queueArr      = array();
    
    // 当前消息交换机 对象
    private $_exchange      = null;
    
    // 当前获取的队列的消息对象
    private $_message       = null;
    
    //消息持久化
    private $_delivery_mode = 2;
    
    //消息属性
    private $_p = array('delivery_mode'=>2);
    
    //回调函数
    private $_callable = '';    
    
    // queue routingkey 关系数组
    private $_routKeyQueue = array();
    
    //交换机列表
    private $_exchangeList = array();
    
    //连接多少强制断连接,默认不会强制断开连接
    private $_channel_timeout = 0;
    
    //是否自动ack
    private $_auto_ack = false;
    
    //是否确认到了队列再返回成功
    private $_mandatory = true;
    
    //true为已经开启过confirm模式
    private $_isConfirmStart = false;
    
    //是否启动confirm模式
    private $_confirm_select = true;
    
    //是否批量提交
    private $_batch_publish = false;
    
    //confirm模式下问内核要数据的次数
    private $_getRecvCount = 0;
    
    //confirm模式下没有找到队列返回的信息
    private $_sendNoFindQueue = array();
    
    //写操作wait等待时间
    private $_writeTimeout = 0;
    
    private $_channel_id = 1;

    //心跳时间
    private $_heartbeat = null;

    //调用日志的回调函数
    private $_logCallFunc = null;

    //当前连接vhost_name
    private $_vhost = '';

    //当前消费的队列名
    private $_consumerQueueName = '';

    //当前消费数据的MsgId
    private $_consumerMsgId = '';

    /**
    * 构造函数
    */
    public function __construct() 
    {

    }
    
    //重新连接
    public function reConnect(){
       $this->_isConfirmStart = false;
       try{
           $r = $this->_conn->close();
       }catch (\Exception $e){
           return $this->connect();
       }
       return $this->connect();
    }

    public function setMsgKey($key){

    }

    public function setHeartbeat($heartbeat){
        $this->_heartbeat = $heartbeat*1;
    }
    
    public function connect(){
        if ($this->_heartbeat == null){
            $heartbeat = $this->_config['heartbeat'] = (isset($this->_config['heartbeat'])  && is_numeric($this->_config['heartbeat']) )?$this->_config['heartbeat']:60;
        }else{
            $heartbeat = $this->_heartbeat;
        }
        $connection_timeout = (isset($this->_config['write_timeout']) && is_numeric($this->_config['write_timeout']) )?$this->_config['write_timeout']:3;
        $read_timeout = (isset($this->_config['read_timeout']) && is_numeric($this->_config['read_timeout']))?$this->_config['read_timeout']:3;
        if ($heartbeat != 0 && $read_timeout < $heartbeat*2 ) {
            $read_timeout = $heartbeat*2;
        }
        $keepalive = (isset($this->_config['keepalive']) && $this->_config['keepalive'])?true:false;

        if(!function_exists('pcntl_signal')){
            //$read_timeout = ceil($heartbeat/2);
            $connection_timeout = $heartbeat+10;
        }
        try{
            $this->_conn = new AMQPStreamConnection(
                $this->_config['base']['host'],
                $this->_config['base']['port'],
                $this->_config['base']['login'],
                $this->_config['base']['password'],
                $this->_config['base']['vhost'],
                'false',
                'AMQPLAIN',
                null,
                'en_US',
                $connection_timeout,
                $read_timeout,
                null,
                $keepalive,
                $heartbeat
                );
            $this->_channel = $this->_conn->channel();
        }catch (\Exception $e) {
           throw new \Exception($e->getMessage()); 
           return false;
        }
        $this->_vhost = $this->_config['base']['vhost'];
        unset($this->_config['base']['connect_timeout']);

        $this->_config['format'] = isset($this->_config['format'])?$this->_config['format']:'';
        return true;
    }
    
    private function reGetChannel(){
        $this->_channel->close();
        $this->_channel = $this->_conn->channel($this->_channel_id++);
        $this->_isConfirmStart = false;
    }
    
    /**
    * 队列消费，exchange_declare,queue_declare 等操作初始化
    * return bool
    */
    private function _initGetInfo($val){
        //$this->_exchange_declare();
        $BindExchangeName = isset($val['exchange_name'])?$val['exchange_name']:'amq.direct';
        $exchange_type = $this->_declareExchange($BindExchangeName);
        $queueName = $val['queue_name'];
        if($exchange_type == 'fanout'){
            $strPol = 'abcdefghijklmnopqrstuvwxyz';
            $queueName = str_replace('.*',$strPol[rand(0,25)].'-'.microtime(true),$queueName);
            $this->_channel->queue_declare($queueName, false, false, true, false,false);
            $this->_channel->queue_bind($queueName, $BindExchangeName);
            return $queueName ;
        }
        //通过queue_name判断是不是当前要进行 queue_declare 的队列，如果是的话，则进行queue_declare ，否则跳过
        if(!isset($v['durable'])){
            $v['durable'] = true;
        }
        if(!isset($v['auto_delete'])){
            $v['auto_delete'] = false;
        }
        $AMQP_DURABLE =$v['durable']===true?true:false;
        $AMQP_AUTODELETE = $v['auto_delete']===true?true:false;

        $this->_channel->queue_declare($queueName, false, $AMQP_DURABLE, false, $AMQP_AUTODELETE,false,$this->_getQueueP($v));
        return true;
    }

    private function _getQueueP($v){
        $p = new AMQPTable();
        if(isset($v['x-max-priority'])){
            $p->set('x-max-priority',$v['x-max-priority']*1);
        }
        if(isset($v['x-message-ttl'])){
            $p->set('x-message-ttl',$v['x-message-ttl']*1);
        }  
        if(isset($v['x-expires'])){
            $p->set('x-expires',$v['x-expires']*1);
        }
        if(isset($v['x-max-length'])){
            $p->set('x-max-length',$v['x-max-length']*1);
        }
        if(isset($v['x-max-length-bytes'])){
            $p->set('x-max-length-bytes',$v['x-max-length-bytes']*1);
        }
        if(isset($v['x-dead-letter-exchange'])){
            $p->set('x-dead-letter-exchange',$v['x-dead-letter-exchange']);
        }
        if(isset($v['x-dead-letter-routing-key'])){
            $p->set('x-dead-letter-routing-key',$v['x-dead-letter-routing-key']);
        }
        return $p;
    }

    private function _declareExchange($BindExchangeName){
        $exchange_type = "direct";
        foreach( $this->_config['exchange'] as $k=>$v ){
            $exchangeName = $v['exchange_name'];
            if ($exchangeName != $BindExchangeName){
                continue;
            }
            if(in_array($exchangeName,array('amq.default','amq.direct','amq.topic','amq.fanout'))){
                continue;
            }
            $v['durable'] = isset($v['durable'])?$v['durable']:true;
            $v['auto_delete'] = isset($v['auto_delete'])?$v['auto_delete']:false;
            $exchangeNameAutoDelete = $v['auto_delete']===true?true:false;
            $exchangeNameDurable = $v['durable']===true?true:false;
            $exchange_type = isset($v['exchange_type'])?$v['exchange_type']:'direct';
            $this->_channel->exchange_declare($exchangeName, $exchange_type, false, $exchangeNameDurable, $exchangeNameAutoDelete,false,true);
            $this->_exchangeList[$exchangeName] = $exchange_type;
        }
        return $exchange_type;
    }
    /**
    * 通过routinkey找到队列名及交换机进行操作初始化
    * return bool
    */
    private function _initQueueByRoutingKey($BindExchangeName,$routingkey){
        //判断是否已经做过初始化操作
        if (isset($this->_RoutingKeyInit[$BindExchangeName.'_'.$routingkey])){
            return true;
        }
        $exchange_type = $this->_declareExchange($BindExchangeName);
        $this->_RoutingKeyInit[$BindExchangeName.'_'.$routingkey] = true;
        if($exchange_type == "fanout"){
            return true;
        }
        foreach( $this->_config['queue'] as $k=>$v ){
            if(isset($v['is_tmp']) && $v['is_tmp']==true){
                continue;
            }
            if(!isset($v['durable'])){
                $v['durable'] = true;
            }
            if(!isset($v['auto_delete'])){
                $v['auto_delete'] = false;
            }
            $v['exchange_name'] = isset($v['exchange_name'])?$v['exchange_name']:'amq.direct';
            if ($routingkey != $v['routingkey'] || $BindExchangeName != $v['exchange_name']){
                continue;
            }
            $queueName = $v['queue_name'];
            $AMQP_DURABLE =$v['durable']===true?true:false;
            $AMQP_AUTODELETE = $v['auto_delete']===true?true:false;
            $this->_channel->queue_declare($queueName, false, $AMQP_DURABLE, false, $AMQP_AUTODELETE,true,$this->_getQueueP($v));
            if(isset($v['routingkey'])){
                $this->_channel->queue_bind($queueName, $v['exchange_name'],$v['routingkey'],true);
                $this->_routKeyQueue[$v['routingkey']][] = array('queue_name'=>$queueName,'exchange_name'=>$v['exchange_name']);
            }
        }

        return true;
    }

    public function setConfirm($bool=false){
        if(!$bool){
            if($this->_isConfirmStart){
                return $this;
            }
        }
        $this->_confirm_select = $bool;
        return $this;
    }
    
    public function setMandatory($bool){
        if($bool){
            $this->_mandatory = true;
            $this->_confirm_select = true;
        }else{
            $this->_mandatory = false;
        }
        return $this;
    }
    
    public function setBatchPublish($bool){
        $this->_batch_publish = $bool;
        if($this->_confirm_select){
            $this->_startConfirm();
        }
        $this->_getRecvCount=0;
        return $this;
    }
    
    public function setAttributes($p){
        $p['delivery_mode'] = isset($p['delivery_mode'])?$p['delivery_mode']:$this->_delivery_mode;
        if (isset($this->_p['message_id'])){
            $p['message_id'] = $this->_p['message_id'];
        }
        $this->_p = $p;
    }

    public function setMsgKeyAndMsgId($key,$msgId){
        $this->_p['message_id'] = $key.'#'.$msgId;
    }

    public function getMsgId($key,$msgId){
        return $key.'#'.$msgId;
    } 
    
    public function setWriteTimeout($timeout){
        $this->_writeTimeout = $timeout*1;
    }

    public function noFindQueueBack($reply_code,$reply_text,$exchange,$routing_key,$message){
        if($this->_confirm_select){
            $this->_sendNoFindQueue[]=array('data'=>$message->body,'p'=>$message->get_properties(),'routingkey'=>$routing_key,'exchange'=>$exchange,'code'=>101,'msg'=>$reply_text);
        }
    }
    
    //启动confirm模式
    private function _startConfirm(){
        if(!$this->_isConfirmStart){
            $this->_channel->confirm_select();
            if($this->_mandatory){
                $this->_channel->set_return_listener(array(&$this,'noFindQueueBack'));
            }
            
            $this->_channel->set_ack_handler(array(&$this,'ackHandler'));
            $this->_writeTimeout = (isset($this->_config['write_timeout']) && is_numeric($this->_config['write_timeout']) )?$this->_config['write_timeout']:0;
            $this->_isConfirmStart = true;
        }
    }
    
    //批量提交
    public function batchPublish($d,$routingkey=null,$exchangeName='amq.direct'){
        if(!$routingkey && $exchangeName == 'amq.direct'){
            throw new \Exception('请传入routingkey 或者非direct模式的交换机'); 
        }
        $this->_initQueueByRoutingKey($exchangeName,$routingkey);
        
        if( $routingkey ){
            if(!isset($this->_routKeyQueue[$routingkey])){
                $return = array();
                foreach($d as $k=>$v){
                    $return[]=array('data'=>$v['Body'],'p'=>$this->_p,'code'=>103,'routingkey'=>$routingkey,'exchange'=>$exchangeName,'msg'=>'routingkey:'.$routingkey .' not exsit');
                }
                return array('error'=>$return);
            }
            $routKeyArr[$routingkey] = '';
            $exchangeName = $this->_routKeyQueue[$routingkey][0]['exchange_name'];
        }else{
            $routingkey = null;
        }
        if(!in_array($exchangeName,array('amq.default','amq.direct','amq.topic','amq.fanout')) && !isset($this->_exchangeList[$exchangeName])){
            $return = array();
            foreach($d as $k=>$v){
                $return[]=array('data'=>$v['Body'],'p'=>$this->_p,'code'=>104,'routingkey'=>$routingkey,'exchange'=>$exchangeName,'msg'=>'exchange:'.$exchangeName .' not exist');
            }
            return array('error'=>$return);
        }
        
        if($this->_confirm_select){
            $this->_startConfirm();
            $mandatory = $this->_mandatory;
        }else{
            $mandatory = false;
        }
        $p = $this->_p;
        foreach($d as $k=>$v){
            $data = $this->encodeData($v['Body'],$this->_config['format']);
            if($v['MsgId']){
                $p['message_id']=$this->getMsgId($v['MsgKey'],$v['MsgId']);
            }else{
                unset($p['message_id']);
            }
            $msg = new AMQPMessage($data,$p);
            $msg->exchange=$exchangeName;
            $msg->routingKey=$routingkey;
            if($this->_confirm_select){
                $this->setSendLog($msg);
            }
            $this->_channel->batch_basic_publish($msg, $exchangeName, $routingkey,$mandatory);
        }
        $this->_getRecvCount = count($d);
        return $this->waitConfirm();
    }
    
    public function setSendLog($dataObj){
        if ($this->_logCallFunc){
            call_user_func($this->_logCallFunc,array('body'=>$dataObj->getBody(),'properties'=>$dataObj->get_properties(),'vhost'=>$this->_vhost,'exchange'=>$dataObj->exchange,'route'=>$dataObj->routingKey,'confirm'=>$this->_confirm_select),1);
        }
    }

    public function ackHandler($dataObj){
        $this->_getRecvCount--;
        $this->setSendLog($dataObj);
    }
    
    public function SetLogFun($callback){
        if (is_callable($callback)){
            $this->_logCallFunc = $callback;
            return true;
        }else{
            $this->_logCallFunc = '';
        }
        return false;
    }

    public function setTrailLogFun($callback){
        if (is_callable($callback)){
            $this->_trailCallFun = $callback;
            return true;
        }else{
            $this->_trailCallFun = null;
        }
        return false;
    }

    //批量写入
    public function waitConfirm($write_timeout=null){
        $this->_channel->publish_batch();
        //$this->_getRecvCount = 1;
        if($write_timeout==null){
            $write_timeout = $this->_writeTimeout;
        }
        if($this->_confirm_select){
            $beReGetChannel = false;
            try{
                while($this->_getRecvCount>0){
                    $this->_channel->wait(null,false,$write_timeout);
                    //throw new \Exception('The connection timed out 10s'); 
                }
            }catch (\Exception $e){
                if( false !== strpos($e->getMessage(),'The connection timed out') ){
                    $beReGetChannel = true;
                }else{
                    throw new \Exception($e->getMessage()); 
                }
            }
            
            $return = $this->_sendNoFindQueue;
            $this->_sendNoFindQueue = array();
            if($r = $this->_channel->getNoAckMessages()){
                foreach($r as $k=>$msg){
                    $return[]=array('data'=>$this->decodeData($msg->body,$this->_config['format']),'p'=>$msg->get_properties(),'code'=>102,'vhost'=>$this->_vhost,'exchange'=>$msg->exchange,'route'=>$msg->routingKey,'msg'=>'timeout or server no response');
                }
            }
            if($beReGetChannel){
                $this->reGetChannel();
            }
            if($return){
                return array('error'=>$return);
            }
        }
        //$this->_channel->getNoAckMessages();
        return true;
    }
    
    /**
    * 往以确认形式并且批量的往队列中写数据
    * return bool
    */
    public function setDataByBatch($data,$routingkey=null,$exchangeName='amq.direct',$msgKey='',$msgId=''){
        $this->_initQueueByRoutingKey($exchangeName,$routingkey);
        $data = $this->encodeData($data,$this->_config['format']);
        if( $routingkey ){
            if(isset($this->_routKeyQueue[$routingkey])){
                $exchangeName = $this->_routKeyQueue[$routingkey][0]['exchange_name'];
            }
        }else{
            $routingkey = null;
        }
        $p = $this->_p;
        if($msgId){
            $p['message_id'] = $this->getMsgId($msgKey,$msgId);
        }
        $msg = new AMQPMessage($data,$p);
        $msg->exchange=$exchangeName;
        $msg->routingKey=$routingkey;
        if($this->_confirm_select){
            $mandatory = $this->_mandatory;
        }else{
            $mandatory = false;
            $this->setSendLog($msg);
        }
        $this->_channel->batch_basic_publish($msg, $exchangeName, $routingkey,$mandatory);
        $this->_getRecvCount++;
        return true;
    }
    
    
    /**
    * 往队列中写入数据
    * return bool
    */
    public function setData($data,$routingkey=null,$exchangeName='amq.direct',$msgKey='',$msgId=''){
        if(!$routingkey && $exchangeName == 'amq.direct'){
            throw new \Exception('请传入routingkey 或者非direct模式的交换机'); 
        }
        if($this->_batch_publish){
            return $this->setDataByBatch($data,$routingkey,$exchangeName,$msgKey,$msgId);
        }
        $data = $this->encodeData($data,$this->_config['format']);
        $this->_initQueueByRoutingKey($exchangeName,$routingkey);
        
        if( $routingkey ){
            if(!isset($this->_routKeyQueue[$routingkey])){
                return array('error'=>array('data'=>$data,'p'=>$this->_p,'code'=>103,'routingkey'=>$routingkey,'exchange'=>$exchangeName,'msg'=>'routingkey:'.$routingkey.' not exist'));
            }
            $routKeyArr[$routingkey] = '';
            $exchangeName = $this->_routKeyQueue[$routingkey][0]['exchange_name'];
        }else{
            $routingkey = null;
        }
        if(!in_array($exchangeName,array('amq.default','amq.direct','amq.topic','amq.fanout')) && !isset($this->_exchangeList[$exchangeName])){
            return array('error'=>array('data'=>$data,'p'=>$this->_p,'code'=>104,'routingkey'=>$routingkey,'exchange'=>$exchangeName,'msg'=>'exchange:'.$exchangeName.' not exist'));
        }

        $p = $this->_p;
        if($msgId){
            $p['message_id'] = $this->getMsgId($msgKey,$msgId);
        }
        
        if($this->_confirm_select){
            $beReGetChannel = false;
            $this->_startConfirm();
            $c = count($this->_sendNoFindQueue);
            $msg = new AMQPMessage($data,$p);
            $msg->exchange=$exchangeName;
            $msg->routingKey=$routingkey;
            $this->_channel->basic_publish($msg, $exchangeName, $routingkey,$this->_mandatory);
            $this->_getRecvCount = 1;
            while($this->_getRecvCount>0){
                try{
                    $this->_channel->wait(null,false,$this->_writeTimeout);
                    //throw new \Exception('The connection timed out 10s');
                }catch (\Exception $e){
                    if( false !== strpos($e->getMessage(),'The connection timed out') ){
                        $beReGetChannel = true;
                    }else{
                        throw new \Exception($e->getMessage()); 
                    }
                }
            }
            if(count($this->_sendNoFindQueue)>$c){
                $msg = array_pop($this->_sendNoFindQueue);
                return array('error'=>$msg);
            }

            if($this->_channel->getNoAckMessages()){
                if($beReGetChannel){
                    $this->reGetChannel();
                }
                return array('error'=>array('data'=>$data,'p'=>$this->_p,'code'=>102,'routingkey'=>$routingkey,'exchange'=>$exchangeName,'msg'=>'timeout or server no response'));
            }
            
        }else{
            $msg = new AMQPMessage($data,$p);
            $msg->exchange=$exchangeName;
            $msg->routingKey=$routingkey;
            $this->_channel->basic_publish($msg, $exchangeName, $routingkey,false);
            $this->setSendLog($msg);
        }
        //$this->_channel->getNoAckMessages();
        return true;
    }

    /**
    * 获取到队列信息后回调方法 
    */
    public function _callbackFunction($msg){
        $p = $msg->get_properties();
        $t = array();
        if(isset($p['message_id']) && $p['message_id']){
            $t = explode('#',$p['message_id']);
            if (isset($t[1])){
                $this->_consumerMsgId = $t[1];
                call_user_func($this->_trailCallFun,$t[1],$this->_consumerQueueName,40);
            }
        }
        if(!$this->_auto_ack){
            $this->delivery_tag = $msg->delivery_info['delivery_tag'];
            if (isset($t[1])){
                call_user_func($this->_trailCallFun,$t[1],$this->_consumerQueueName,46);
                $this->_consumerMsgId = '';
            }
        }
        $data = $this->decodeData($msg->body,$this->_config['format']);
        if ($this->_logCallFunc){
            call_user_func($this->_logCallFunc,array('body'=>$data,'properties'=>$p,'vhost'=>$this->_vhost,'queue'=>$this->_consumerQueueName),2);
        }
        call_user_func($this->_callable,$data,$this);
    }

    /**
    * 消费队列中的信息
    * param $queue    队列名称
    * param $callback 回调方法
    * return array    消费过程中连接等断开异常信息
    */    
    public function getData($queue,$callback){
        if(!$callback){
            return array( 'status'=>false,'msg'=>'callback 为空' );
        }
        $this->_callable  = $callback;
        $b = $this->_initGetInfo($queue);
        if(!is_bool($b)){
            $this->_consumerQueueName = $b;
        }else{
            $this->_consumerQueueName = $queue['queue_name'];
        }
        if(isset($queue['auto_ack'])){
            $this->_auto_ack = $queue['auto_ack']?true:false;
        }
        unset($queue);
        
        $_channel_timeout = (isset($this->_config['connect_timeout'])  && is_numeric($this->_config['connect_timeout']) )?$this->_config['connect_timeout']:0;
        $prefetchSize = isset($this->_config['prefetchSize'])?$this->_config['prefetchSize']*1:0;
        $prefetchCount = isset($this->_config['prefetchCount'])?$this->_config['prefetchCount']*1:2;
        $prefetchSize = isset($v['prefetchSize'])?$v['prefetchSize']*1:$prefetchSize;
        $prefetchCount = isset($v['prefetchCount'])?$v['prefetchCount']*1:$prefetchCount;
        $this->_channel->basic_qos($prefetchSize,$prefetchCount,false);
        
        $this->_channel->basic_consume($this->_consumerQueueName, '', false, $this->_auto_ack, false, false, array($this,'_callbackFunction'));
        $startTime = time();
        while(count($this->_channel->callbacks)) {
            $this->_channel->wait(null,false,$_channel_timeout);
        }
    }
    
    public function begin(){
        $this->_channel->tx_select();
        return true;
    }
    
    public function commit(){
        $this->_channel->tx_commit();
        return true;
    }
    
    public function rollback(){
        $this->_channel->tx_rollback();
        return true;
    }
    
    public function ack(){
        if(!$this->_auto_ack){
            $this->_channel->basic_ack($this->delivery_tag);
            if($this->_consumerMsgId){
                call_user_func($this->_trailCallFun,$this->_consumerMsgId,$this->_consumerQueueName,46);
                $this->_consumerMsgId = '';
            }
        }
        return true;
    }

    public function nack(){
        if(!$this->_auto_ack){
            $this->_channel->basic_nack($this->delivery_tag,false,true);
            if($this->_consumerMsgId){
                call_user_func($this->_trailCallFun,$this->_consumerMsgId,$this->_consumerQueueName,42);
                $this->_consumerMsgId = '';
            }
            return true;
        }
        return false;
    }
    
    public function close(){
        try{
            if($this->_conn){
                $this->_channel->close();
                $this->_conn->close();
            }
        }catch (\Exception $e){
            
        }
    }
    
    public function __destruct(){
        try{
            if(isset($this->_conn) && $this->_conn){
                $this->_channel->close();
                $this->_conn->close();
            }
        }catch (\Exception $e){
            
        }
    }
}
?>