<?php 
namespace xlogger\rule;
/**
 *  XLogger_Rule_Long 类
 *
 * @package xlogger
 * @subpackage xlogger\rule
 * @author  谢<jc3wish@126.com>
 * 
 * $Id$
 */
class XLogger_Rule_Long
{
	private $_key='';

	public function __construct() 
	{

    }
	
	public function getSource(){
		if(!$this->_key){
			return false;
		}
		if( $this->tableCount ){
			$k = ($this->_key) % ($this->tableCount);
		}elseif( $this->tableLength ){
			$k = ceil($this->_key / $this->tableLength);
		}
		$tableName=$this->prefix.'_'.($k-1);
		return array(
			'dataTable'		=>$tableName,
			'dataBase'		=>$this->database,
		);
	}
	
	public function setKey($val){
		if(is_numeric($val)){
			$this->_key=$val;
			return $this;
		}
		return false;
	}
	
	public function setParam($key,$val){
		if($key){
			$this->$key=$val;
		}
		return $this;
	}

}