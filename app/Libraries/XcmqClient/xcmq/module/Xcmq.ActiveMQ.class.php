<?php 
namespace xcmq\module;
use \mq\Activemq as Activemq;
/**
 *  Xcmq_ActiveMQ 类
 *
 * @package xcmq
 * @subpackage xcmq.module
 * @author  谢<jc3wish@126.com>
 * 
 * $Id$
 */
class Xcmq_ActiveMQ extends Xcmq_Abstract
{
    /**
    * @var 连接对象
    */
    protected $_conn = NULL;

    //连接及配置参数
    protected $_config        = array();
    
    //事务id
    private $_transaction_id=0;

    // queue routingkey 关系数组
    private $_routKeyQueue = array();
    
    /**
    * 构造函数
    */
    public function __construct() 
    {
    }

    //重新连接
    public function reConnect(){
        
        $this->connect();
    }
    
    public function connect(){
        $this->_conn=Activemq::getInstance($this->_config['base'])->getConnect();
    }
    
    public function setQueInfo($config){
        unset($config['mq']);
        $this->_config = $config;
        
        foreach( $config['queue'] as $k=>$v ){
            if($v['queue_name'] && $v['routingkey'] ){
                $this->_routKeyQueue[$v['routingkey']][] = $v['queue_name'];
            }
        }
        
        return $this;
    }
    
    public function setData($data,$routingkey=''){
        if( !$data ){
            return false;
        }
        
        $data = $this->encodeData($data,$this->_config['format']);
        
        // 假如要往多个队列中写入数据，并且没有开启事务的情况下，将自动开启事务
        if( $routingkey ){
            if(!isset($this->_routKeyQueue[$routingkey])){
                return false;
            }
            $routKeyArr[$routingkey] = $this->_routKeyQueue[$routingkey];
            if(!$this->_transaction_id && 1 < count($routKeyArr)){
                $startTransaction = true;
                $this->begin();
            }
        }else{
            $routKeyArr = $this->_routKeyQueue;
            $startTransaction = true;
            $this->begin();
        }
        
        if( isset($this->_config['persistent']) & $this->_config['persistent'] === true ){
            $p = array('persistent' => 'true');
        }else{
            $p = array('persistent' => 'false');
        }
        
        if( $this->_transaction_id ){
            $p['transaction']=$this->_transaction_id;
        }
        foreach( $routKeyArr as $k=>$v ){
            foreach ( $v as $queue ){
                try{
                    //echo $queue."\r\n";
                    $r = $this->_conn->send($queue, $data, $p);
                    if( !$r ){
                        if( $startTransaction == true ){
                            $this->rollback();
                        }
                        return false;
                    }
                }catch (\Exception $e){
                    if( $startTransaction == true ){
                        $this->rollback();
                    }
                    return $e;
                }
            }
        }
        if( $startTransaction == true ){
            $this->commit();
        }
        return true;
    }
    
    public function getData($queue,$callback){
        if(!$queue || !$callback){
            return array( 'status'=>false,'msg'=>'queue 或者 callback 为空' );
        }
        $queue = $queue['queue_name'];
        try{
            if( $this->_config['prefetchSize'] ){
                $this->_conn->subscribe($queue, array('activemq.prefetchSize' => $this->_config['prefetchSize']*1));
            }else{
                $this->_conn->subscribe($queue);
            }
            
            while (true)
            {
                if(TRUE === $this->_conn->hasFrame()){
                    try{
                        $this->frame = $this->_conn->readFrame();
                    }catch (\StompException $e){
                        return array( 'status'=>false,'msg'=>$e->getMessage() );
                    }
                    if (false !== $this->frame)
                    {
                        $data = $this->decodeData($this->frame->body,$this->_config['format']);
                        
                        call_user_func($callback,$data,$this);

                    } else {
                        continue;
                    }
                }
            }
        }catch (\Exception $e){
            return array( 'status'=>false,'msg'=>$e->getMessage() );
        }finally {
            return array( 'status'=>false,'msg'=>'finally error' );
        }
    }
    
    
    public function begin(){
        $this->_transaction_id = time().'.'.mt_rand();
        $this->_conn->begin($this->_transaction_id);
        return true;
    }
    
    public function commit(){
        if( !$this->_transaction_id ){
            return;
        }
        $this->_conn->commit($this->_transaction_id);
        $this->_transaction_id=0;
        return;
    }
    
    public function rollback(){
        $this->_transaction_id = 0;
        $this->_conn->abort($this->_transaction_id);
        return true;
    }
    
    public function ack(){
        try{
             $this->_conn->ack($this->frame);
        }catch (\Exception $e){
            return false;
        }
        return true;
    }
    
    public function __destruct(){

    }
}
?>