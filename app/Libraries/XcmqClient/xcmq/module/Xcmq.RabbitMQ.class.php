<?php 
namespace xcmq\module;
use \mq\Rabbitmq as Rabbitmq;
/**
 *  Xcmq_Rabbitmq 类
 *
 * @package xcmq
 * @subpackage xcmq.module
 * @author  谢<jc3wish@126.com>
 * 
 * $Id$
 */
//define('AMQP_DURABLE', 2);
//define('AMQP_AUTODELETE', 16);
class Xcmq_RabbitMQ extends Xcmq_Abstract
{
    /**
    * @var 连接对象
    */
    protected $_conn          = NULL;
    
    //连接及配置参数
    protected $_config        = array();
    
    //消息队列载体对象池
    
    private $_queueArr      = array();

    // 当前队列名称
    private $_currentQueueName = '';
    
    // 当前消息交换机 对象
    private $_exchange      = null;
    
    // 当前获取的队列的消息对象
    private $_message       = null;
    
    // 消息持久化
    private $_delivery_mode = 2;
    
    //回调函数
    private $_callable = '';

    // queue routingkey 关系数组
    private $_routKeyQueue = array();
    
    //是否已经写入队列信息exchange_declare ，queue_declare 等操作初始化
    private $_isInitSetInfo = false;
    
    //交换机类型
    private $_exchange_type = '';
    
    //连接多少强制断连接,默认不会强制断开连接
    private $_channel_timeout = 0;
    
    /**
    * 构造函数
    */
    public function __construct() 
    {
        
    }
    
    //重新连接
    public function reConnect(){
       try{
           $r = $this->_conn->Close();
       }catch (\Exception $e){
           return $this->connect();
       }
       $r= $this->connect();
        return $r;
    }
    
    public function connect(){
        unset($this->_conn,$this->_channel,$this->_exchange,$this->_queueArr);
        $this->_config['base']['write_timeout'] = (isset($this->_config['write_timeout']) && is_numeric($this->_config['write_timeout']) )?$this->_config['write_timeout']:3;
        $this->_config['base']['read_timeout'] = (isset($this->_config['read_timeout']) && is_numeric($this->_config['read_timeout']))?$this->_config['read_timeout']:30;
        $this->_channel_timeout = (isset($this->_config['connect_timeout'])  && is_numeric($this->_config['connect_timeout']) )?$this->_config['connect_timeout']:0;        
        
        $this->_conn=Rabbitmq::getInstance($this->_config['base']);
        unset($this->_config['base']['write_timeout'],$this->_config['base']['read_timeout'],$this->_config['base']['hearbeat'],$this->_config['connect_timeout']);
        if( !$this->_conn->getConnect() ){
            throw new Exception('_conn not success'); 
            return false;
        }

        unset($this->_config['base']['connect_timeout']);
        
        if( $this->_config['delivery_mode'] ){
            $this->_delivery_mode = $this->_config['delivery_mode'];
        }
        
        // 消息交换机
        $this->_channel = new \AMQPChannel($this->_conn->getConnect());

        if(isset($this->_config['prefetchSize'])){
            $this->_channel->setPrefetchSize($this->_config['prefetchSize']*1);
        }
        if(isset($this->_config['prefetchCount'])){
            $this->_channel->setPrefetchCount($this->_config['prefetchCount']*1);
        }
        
        return $this;
        
    }

    /**
    * exchange_declare 交换机创建
    */
    private function _exchange_declare(){
        $exchangeName = $this->_config['exchange']['exchange_name'];
        $exchangeNameAutoDelete = $this->_config['exchange']['auto_delete']?$this->_config['exchange']['auto_delete']:true;
        
        $exchangeNameDurable = $this->_config['exchange']['durable']===true?2:0;
        $exchangeNameAutoDelete = $exchangeNameAutoDelete === true?16:0;
        
        $this->_exchange = new \AMQPExchange($this->_channel);
  
        $this->_exchange->setName($exchangeName);
        
        $this->_exchange_type = $this->_config['exchange']['exchange_type']?$this->_config['exchange']['exchange_type']:$this->_config['amqp_ex_type'];
        $this->_exchange->setType($this->_exchange_type);
        //$this->_exchange->setFlags(AMQP_DURABLE | AMQP_AUTODELETE);
        
        //$this->_exchange->setFlags(AMQP_DURABLE | 1);
        $this->_exchange->setFlags($exchangeNameDurable | $exchangeNameAutoDelete);
        
        $this->_exchange->declareExchange();
    }    
    
    /**
    * 队列写的绑定routingkey ，exchange_declare,queue_declare 等操作初始化
    * return bool
    */
    private function _initSetInfo(){
        if($this->_isInitSetInfo){
            return true;
        }
        
        $this->_exchange_declare();
        
        if( $this->_config['delivery_mode'] ){
            $this->_delivery_mode = $this->_config['delivery_mode'];
        }

        if( !isset($this->_config['queue']) || !is_array($this->_config['queue'])  ){
            $this->_isInitSetInfo = true;
            return true;
        }
        //创建队列，让exchange通过和路由关键字 ，队列进行关联上
        foreach( $this->_config['queue'] as $k=>$v ){
            if(isset($v['is_tmp']) && $v['is_tmp']==true){
                continue;
            }
            $queueName = $v['queue_name'];
            $AMQP_DURABLE =$v['durable']===true?2:0;
            $AMQP_AUTODELETE = $v['auto_delete']===true?16:0;
            
            $q = new \AMQPQueue($this->_channel);
            $q->setName($queueName);
            $q->setFlags($AMQP_DURABLE | $AMQP_AUTODELETE);
            $q->declareQueue();
            $this->_queueArr[$queueName] = $q;
            if(isset($v['routingkey'])){
                $q->bind($this->_config['exchange']['exchange_name'], $v['routingkey']);
                $this->_routKeyQueue[$v['routingkey']][] = $queueName;
            }
        }
        $this->_isInitSetInfo = true;
        return true;
    }

    /**
    * 队列消费，exchange_declare,queue_declare 等操作初始化
    * return bool
    */
    private function _initGetInfo($v){
        $this->_exchange_declare();
        $queueName = $v['queue_name'];
        if(isset($v['is_tmp']) && $v['is_tmp']==true){
            $strPol = 'abcdefghijklmnopqrstuvwxyz';
            $queueName = str_replace('.*',$strPol[rand(0,25)].'-'.microtime(true),$queueName);
            $q = new \AMQPQueue($this->_channel);
            $q->setName($queueName);
            $q->setFlags(0 | 16);
            $q->declareQueue();
            $this->_queueArr[$queueName] = $q;
            return $queueName ;
        }
        //通过queue_name判断是不是当前要进行 queue_declare 的队列，如果是的话，则进行queue_declare ，否则跳过
        $AMQP_DURABLE =$v['durable']===true?2:0;
        $AMQP_AUTODELETE = $v['auto_delete']===true?16:0;
        $q = new \AMQPQueue($this->_channel);
        $q->setName($queueName);
        $q->setFlags($AMQP_DURABLE | $AMQP_AUTODELETE);
        $q->declareQueue();
        $this->_queueArr[$queueName] = $q;
        return true;
    }
    
    //获取消息队列载体对象
    private function _getQueueObj($queueName){
        if( $this->_queueArr[$queueName] ){
            return $this->_queueArr[$queueName];
        }
        return false;
    }
    
    public function setData($data,$routingkey=''){
        if( !$data ){
            return false;
        }
        $data = $this->encodeData($data,$this->_config['format']);
        $this->_initSetInfo();
        if($this->_exchange_type == 'fanout'){
            try{
                $r = $this->_exchange->publish($data,null,0,array('delivery_mode'=>$this->_delivery_mode));
            }catch (\Exception $e){
                return $e;
            }
            return true;
        }
        if( $routingkey ){
            if(!isset($this->_routKeyQueue[$routingkey])){
                return false;
            }
            $routKeyArr[$routingkey] = '';
        }else{
            $routKeyArr = $this->_routKeyQueue;
        }
        try{
            foreach ( $routKeyArr as $k=>$v ){
                $r = $this->_exchange->publish($data, $k,0,array('delivery_mode'=>$this->_delivery_mode));
            }
        }catch (\Exception $e){
            return $e;
        }
        return true;
    }
    
    public function getData($queue,$callback){
        if(!$queue || !$callback){
            return array( 'status'=>false,'msg'=>'queue 或者 callback 为空' );
        }
        $b = $this->_initGetInfo($queue);
        if(!is_bool($b)){
            $queueName = $b;
        }else{
            $queueName = $queue['queue_name'];
        }
        $this->_callable  = $callback;
        $this->_currentQueueName = $queueName;
        $startTime = time();
        while(true){
            try{
                $this->_getQueueObj($queueName)->consume(array($this,'callback'),false);
            }catch (\Exception $e){
                if( false !== strpos($e->getMessage(),'Library error')){
                    return array( 'status'=>false,'msg'=>$e->getMessage() );
                }else if( false !== strpos($e->getMessage(),'ACCESS_REFUSED') ){ //ACCESS_REFUSED 为拒绝访问，没有权限
                    return false;
                }else{
                    if($this->_channel_timeout < (time()-$startTime)){
                        return array( 'status'=>false,'msg'=>'The connection timed out after '.$this->_channel_timeout .'s');
                    }
                    continue;
                }
            }
        }
    }
    
    public function callback($envelope, $queueObj){
        $this->delivery_tag = $envelope->getDeliveryTag();
        
        $data = $this->decodeData($envelope->getBody(),$this->_config['format']);

        call_user_func($this->_callable,$data,$this);
    }

    public function begin(){
        $this->_channel->startTransaction();
        return true;
    }
    
    public function commit(){
        $this->_channel->commitTransaction();
        return true;
    }
    
    public function rollback(){
        $this->_channel->rollbackTransaction();
        return true;
    }
    
    public function ack(){
        try{
             $this->_getQueueObj($this->_currentQueueName)->ack($this->delivery_tag);
        }catch (\Exception $e){
            return false;
        }
        return true;
    }
    
    public function close(){
        try{
            if($this->_conn){
                $this->_conn->getConnect()->disconnect();
            }
        }catch (\Exception $e){
            
        }
    }
    
    public function __destruct(){
        try{
            if(isset($this->_conn) && $this->_conn){
                $this->_conn->getConnect()->disconnect();
            }
        }catch (\Exception $e){
            
        }
    }    
    
}
?>