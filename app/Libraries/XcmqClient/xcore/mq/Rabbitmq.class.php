<?php
namespace mq;
class Rabbitmq{
	
	private static $_instance   = array();
    
    private $_AMQPConn             = null;
	
	private function __construct($params){
         try {
             $p = array( 'host'=>$params['host'] , 'port'=> $params['port'], 'login'=>$params['login'] , 'password'=> $params['password'],'vhost' =>$params['vhost']);
             
             $this->_AMQPConn = new \AMQPConnection($p);
             if(isset($params['read_timeout'])){
                 $this->_AMQPConn->setReadTimeout($params['read_timeout']*1);
             }
             if(isset($params['write_timeout'])){
                 $this->_AMQPConn->setWriteTimeout($params['write_timeout']*1);
             }
             
             $this->_AMQPConn->connect();
          } catch (\Exception $e) {
             throw new \Exception($e->getMessage());
             $this->_AMQPConn = null;
          }
	}
	
    public static function getInstance($data) {
		if(!$data){
			return false;
		}
        //return new self($data);
		$key=md5(json_encode($data));
        if (!isset(self::$_instance[$key]) || is_null(self::$_instance[$key])) {
            self::$_instance[$key] = new self($data);
            return self::$_instance[$key];
        }
        try{
            //return new self($data);
            $r = self::$_instance[$key]->_AMQPConn->isConnected();
            if( !$r ){
                self::$_instance[$key] = new self($data);
            }
        }catch  (\Exception $e) {
            self::$_instance[$key] = new self($data);
            return self::$_instance[$key];
        }
		return self::$_instance[$key];
    }
    
	
    public function Close(){
        $this->_AMQPConn->disconnect();
    }
    
    public function getConnect(){
        return $this->_AMQPConn;
    }
	
}