<?php 
namespace xlogger\module;
/**
 *  XLogger 类
 *
 * @package module
 * @author  谢<jc3wish@126.com>
 * 
 * $Id$
 */
class XLogger
{

	// 配置内容
	private $configArr = array();
	
    /**
     * 单例池
     *
     * @var Array
     */

    private static $_instance=array();
	
    /**
     * 存储对类类池
     *
     * @var Array
     */
	private $_objModule=array();
	
	//日志模块
	private $m='';
	
	private function __construct($file) 
	{
		$this->configArr=include($file);
    }
	

    public static function getInstance($project) {
		if(!$project){
			return false;
		}
        if( defined('XLOGGER_CONF_PATH') ){
            $file=XLOGGER_CONF_PATH.'/'.$project.'.php';
        }else{
            $file=XLOGGER_API_PATH.'/config/'.$project.'.php';
        }
		if( !file_exists($file) ){
			return false;
		}
		if(!$project){
			return false;
		}
		$key=md5($project);
        if (empty(self::$_instance[$key])) {
            self::$_instance[$key] = new self($file);
        }
		return self::$_instance[$key];
    }

	private function _getObj($db,$source){
		$key=$this->m.''.$db;
		if(isset($this->_objModule[$key])){
			return $this->_objModule[$key];
		}
		//print_r($source);
		switch ($db){
			case 'redis':
				$this->_objModule[$key] = new XLogger_Redis($source);
				break;
			case 'file':
				$this->_objModule[$key] = new XLogger_File($source);
				break;
			case 'mongodb':
				$this->_objModule[$key] = new XLogger_Mongodb($source);
				break;
		}
		
		return $this->_objModule[$key];
	}
	
	private function _getDataSoure(&$data){	
		$dataSrouce=array(
			'dataBase'		=>'',
			'dataTable'		=>'',
		);
		if ( @$class=$this->configArr[$this->m]['rule']['class'] ){
			$obj=new $class();
			$key=$this->configArr[$this->m]['rule']['key'];
			$obj->setKey($data[$key]);
			foreach($this->configArr[$this->m]['rule']['param'] as $k=>$v ){
				$obj->setParam($k,$v);
			}
			$dataSrouce=$obj->getSource();
		}
		return $dataSrouce;
	}
	
	//获取存储连接地址
	public function getSource($data){
		if( isset($this->configArr[$this->m]['enable']) &&  !$this->configArr[$this->m]['enable']){
			return false;
		}
		$data['addTime']=isset($data['addTime'])?$data['addTime']:date('Y-m-d H:i:s');
		$dataSrouce = $this->_getDataSoure($data);
		if( $this->configArr[$this->m]['db']=='file' ){
			$sourceInfo['base'] = $this->configArr[$this->m]['base'].'/'.$dataSrouce['dataTable'].'.txt';
		}else{
			$sourceInfo['base']=$this->configArr[$this->m]['base'];
			$sourceInfo['base'] +=$dataSrouce;
		}
		return $sourceInfo;
	}
	
	public function setData($data){
		if( isset($this->configArr[$this->m]['enable']) &&  !$this->configArr[$this->m]['enable']){
			return false;
		}
		$data['addTime']=isset($data['addTime'])?$data['addTime']:date('Y-m-d H:i:s');
		$module=$this->m;
		if( !$module || !$data)
		{
			return false;
		}
        restore_error_handler();
		$dataSource=$this->_getDataSoure($data);
		$source=$this->configArr[$this->m]['base'];
		if( $db=$this->configArr[$module]['db'] ){
			$obj=$this->_getObj($db,$source);
			return $obj->setFormat($this->configArr[$this->m]['format'])->setData($data,$dataSource);
		}
		return false;
	}
	
	
	public function setM($m){
		$this->m=$m;
		return $this;
	}
	
	public function getData($data){
		if( isset($this->configArr[$this->m]['enable']) &&  !$this->configArr[$this->m]['enable']){
			return false;
		}
		$data['addTime']=isset($data['addTime'])?$data['addTime']:date('Y-m-d H:i:s');
		$dataSource=$this->_getDataSoure($data);
		$obj=$this->_getObj($this->configArr[$this->m]['db'],$this->configArr[$this->m]['base']);
		return $obj->getData(json_encode($data),$dataSource);	
	}
	
}
?>