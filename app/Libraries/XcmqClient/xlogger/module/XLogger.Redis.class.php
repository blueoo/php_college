<?php 
namespace xlogger\module;
use \nosql\Redis_Cache as Redis_DB;
/**
 *  XLogger_Redis 类
 *
 * @package xlogger
 * @subpackage xlogger.module
 * @author  谢<jc3wish@126.com>
 * 
 * $Id$
 */
class XLogger_Redis
{
	/**
	 * @var Mongodb 连接对象
	 */
	private $connect = NULL;
	
	var $_format='json';
        
	/**
	 * 构造函数
	 * @param array $param host,port 服务器的主机名或IP地址或者为服务器组相关信息
	 */
	public function __construct($param=array()) 
	{
		$this->connect=Redis_DB::getInstance($param)->getConnect();
    }
	
	public function setData($param=array(),$dataSource=array()){
		if(!$param || !is_array($param)){
			return false;
		}
		$data = json_encode($param);
		if( is_numeric($dataSource['dataBase']) ){
			$this->connect->select($dataSource['dataBase']);
		}
		return $this->connect->rPush($dataSource['dataTable'],$data);		
	}
	
	public function setFormat($format){
		return $this;
	}
	
	public function getData($param=array(),$dataSource=array()){
		return array('msg'=>'暂不支持读取');
	}
	
	

	
}
?>