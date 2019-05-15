<?php 
namespace xcmq\module;

/**
 *  Xcmq_Abstract 类
 *  对外提供的方法
 * @package xcmq
 * @subpackage xcmq.module
 * @author  谢<jc3wish@126.com>
 * 
 * $Id$
 */
class Xcmq_Abstract
{
    /**
    * @var 连接对象
    */
    protected $_conn          = NULL;
    
    //连接及配置参数
    protected $_config        = array();

    /**
    * 构造函数
    */
    public function __construct() 
    {

    }
    
    //重新连接
    public function reConnect(){

    }
    
    public function connect(){
        
    }
    
    /**
    * 初始化队列配置信息
    * return $this
    */    
    public function setQueInfo($config){
        unset($config['mq']);
        $this->_config = $config;
        return $this;
    }
    
    public function setData($data,$routingkey=''){
        
    }

    public function getData($queue,$callback){
        
    }
    
    public function begin(){

    }
    
    public function commit(){

    }
    
    public function rollback(){

    }
    
    public function ack(){

    }
    
    public function __destruct(){
        
    }

    /**
    * 通过配置对数据进行处理
    * return string
    */
    public function encodeData($data,$format){
        switch ($format){
            case 'serialize':
                $data = serialize($data);
                break;
            case 'json':
                $data = json_encode($data);
                break;
        }
        return $data;
    }

    /**
    * 通过配置对数据进行处理
    * return 
    */
    public function decodeData($data,$format){
        switch ($format){
            case 'serialize':
                $data = unserialize($data);
                break;
            case 'json':
                $data = json_decode($data,true);
                break;
        }
        return $data;
    }
}
?>