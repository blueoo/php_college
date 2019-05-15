<?php
namespace mq;
class Activemq{
	
	private static $_instance   = array();
    
    private $_stomp             = null;
	
	private function __construct($params){
         try {
             $this->_stomp = new \Stomp($params);
          } catch (\StompException $e) {
             throw new \Exception($e->getMessage());
             //die('Connection failed: '.$e->getMessage());
          }
		
	}
	
    public static function getInstance($data) {
		if(!$data){
			return false;
		}
		$key=md5($data);
        if (is_null(self::$_instance[$key])) {
            self::$_instance[$key] = new self($data);
        }
		return self::$_instance[$key];
    }

	
    public function Close(){
        $this->_stomp->disconnect();
        
    }
    
    public function getConnect(){
        return $this->_stomp;
    }
	
}