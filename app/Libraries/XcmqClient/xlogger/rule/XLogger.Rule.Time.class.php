<?php 
namespace xlogger\rule;
/**
 *  XLogger_Rule_Time 类
 *
 * @package xlogger\rule
 * @subpackage xlogger\rule
 * @author  谢<jc3wish@126.com>
 * 
 * $Id$
 */
class XLogger_Rule_Time
{
	private $_key='';

	public function __construct() 
	{

    }
	
	public function getSource(){
		
		if( !$this->partition ){
			$this->partition='Y-m';
		}
		$tableName=$this->prefix.'_'.date($this->partition,strtotime($this->_key));
		
		return array(
			'dataTable'		=>$tableName,
			'dataBase'		=>$this->database,
		);
	}
	
	public function setKey($val){
		if($val){
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