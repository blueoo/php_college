<?php 
namespace xlogger\module;
use \nosql\Mongodb as Mongodb;
/**
 *  XLogger_Mongodb 类
 *
 * @package lib
 * @subpackage xlogger.module
 * @author  谢<jc3wish@126.com>
 * 
 * $Id$
 */
class XLogger_Mongodb
{
	/**
	 * @var Mongodb 连接对象
	 */
	private $connect = NULL;
		
	private $_format='json';
	    
	/**
	 * 构造函数
	 * @param string $param 服务器的主机名或IP地址或者为服务器组相关信息
	 */
	public function __construct($param) 
	{
		$this->connect=Mongodb::getInstance($param);
    }
	
	public function setData($param=array(),$dataSource=array()){
		if(!$param || !is_array($param)){
			return false;
		}
		if( $dataSource['dataBase'] ){
			$this->connect->selectDb($dataSource['dataBase']);
		}
		return $this->connect->insert($dataSource['dataTable'],$param,false);		
	}
	
	public function setFormat($format){
		//$this->_format=$format;
		return $this;
	}
	
	public function getData($param=array(),$dataSource=array()){
		return array('msg'=>'暂不支持读取');
	}
	
	

	
}
?>