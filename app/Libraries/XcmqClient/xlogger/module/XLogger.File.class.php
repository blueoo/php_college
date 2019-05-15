<?php 
namespace xlogger\module;
/**
 * XLogger_File 类
 *
 * @package xlogger
 * @subpackage xlogger.module
 * @author  谢<jc3wish@126.com>
 * 
 * $Id$
 */
class XLogger_File
{
	
	private $_format='json';
	
    
	private $_dir ='';
    
    private $_cacheLogCount = 0;
  
	
    /**
     * 文件fopen资源缓冲池
     *
     * @var Array
     */
    private $_fileSource=array();
	
	/**
	 * 构造函数
	 * @param string $dir 文件存放文件夹路径
	 */
	public function __construct($dir) 
	{
		$this->_dir=$dir;
    }

	/**
	 * 保存数据函数
	 * @param array $param 要保存的数据
	 * @param array $dataSource 文件名称信息
	 */
	public function setData($param=array(),$dataSource=array()){
		if(!$param || !is_array($param)){
			return false;
		}
		if(!$dataSource['dataTable']){
			return false;
		}
        fwrite($this->_getFileSource($dataSource['dataTable']),json_encode($param)."\n");
        $this->_cacheLogCount++;
        if( $this->_cacheLogCount % 10 == 0){
            if( $this->_fileSource ){
                foreach( $this->_fileSource as &$v  ){
                    fclose($v);
                    unset($this->_fileSource[$dataSource['dataTable']]);
                    //$this->_getFileSource($dataSource['dataTable']);
                }
            }       
        }
			
	}

	/**
	 * 获取fopen资源信息
	 * @param string $fileName 文件名称
	 */		
	private function _getFileSource($fileName){
		if( isset($this->_fileSource[$fileName]) ){
			return $this->_fileSource[$fileName];
		}
        @mkdir($this->_dir,'0755',true);
		$file=$this->_dir.'/'.$fileName.'.txt';
		$this->_fileSource[$fileName] = fopen($file,'a');
		return $this->_fileSource[$fileName];
	}
	
	/**
	 * 设置格式函数
	 * @param string $format 格式
	 * @return $this
	 */		
	public function setFormat($format){
		//$this->_format=$format;
		return $this;
	}
	
	public function getData($param=array(),$dataSource=array()){
		return array('msg'=>'暂不支持读取');
	}
	
	public function __destruct(){
		if( $this->_fileSource ){
			foreach( $this->_fileSource as &$v  ){
				fclose($v);
			}
		}	
	}
	

	
}
?>