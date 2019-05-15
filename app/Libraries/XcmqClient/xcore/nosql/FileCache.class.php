<?php 
/**
 *  File_Cache 类
 *
 * @package nosql
 * @author 谢<jc3wish@126.com>
 */
class File_Cache{
	/**
	 * @var string $cachePath 缓存文件目录
	 * @access public
	 */
	var $cachePath;
		

	/*
	*	单例池
	*/
	private static $_instance = array(); 
	
	/**
	 * 构造函数
	 * @param string $path 缓存文件目录
	 */
	private function __construct($path = './') {
		($path[strlen($path)-1] != '/') && $path .= '/';
		(!empty($path)) && $this->cachePath = $path;
		(!is_dir($this->path)) && FileSystem::makeDir($this->cachePath);
    }


	public static function getInstance($path = './')
	{	
		$a = md5($path);
		if(!self::$_instance[$a])
		{
			self::$_instance[$a] = new self($path);
		}
		return self::$_instance[$a];
	}
	
	/**
	 * 在cache中设置键为$key的项的值，如果该项不存在，则新建一个项
	 * @param string $key 键值
	 * @param mix $var 值
	 * @param int $expire 到期秒数, 0 无限期, 也可以用标准日期时间描述(strtotime)到期时间, 由用户自己来维护
	 * @param int $flag 标志位
	 * @return boolean 如果成功则返回 true，失败则返回 false。
	 * @access public
	 */
    function set($key, $var, $expire = 0, $flag = 0) {
		$fp = fopen($this->makeFilename($key), 'w');
		if (gettype($expire) == 'string') {
			$expire = strtotime($expire);
		} elseif ($expire > 0) {
			$expire = time() + $expire;
		} else {
			$expire = 0;
		}
		$value = array('timeout' => $expire);
		if (in_array(gettype($var), array('boolean', 'integer', 'double', 'string', 'array', 'NULL'))) {
			$value['var'] = $var;
		} else {
			$value['serialize'] = serialize($var);
		}
		$result = fwrite($fp, '<?php return ' . var_export($value, true) . ';?>');
		fclose($fp);
		return $result;
	}
	
	/**
	 * 在cache中获取键为$key的项的值
	 * @param string $key 键值
	 * @return mixed 如果该项不存在，则返回 NULL
	 * @access public
	 */
    function get($key) {
    	$result = NULL;
		$file = $this->makeFilename($key);
		if (is_file($file)) {
			$value = include($file);
			if ($value['timeout'] == 0 || time() <= $value['timeout']) {
				$result = isset($value['var']) ? $value['var'] : unserialize($value['serialize']);
			}
		}
		return $result;
	}
	
	/**
	 * 清空cache中所有项
	 * @return 如果成功则返回 TRUE，失败则返回 FALSE。
	 * @access public
	 */
    function flush() {
		$fileList = FileSystem::ls($this->cachePath, array(), 'ASC', true);
		return FileSystem::rm($fileList);
	}
	
	/**
	 * 删除在cache中键为$key的项的值
	 * @param string $key 键值
	 * @return 如果成功则返回 true，失败则返回 false。
	 * @access public
	 */
    function delete($key) {
		return FileSystem::rm($this->makeFilename($key));
	}
	
	/**
	 * 获取缓存文件路径及文件名
	 * @param string $key 键名
	 * @return string
	 */
	function makeFilename($key) {
		$pos = strrpos($key, '/');
		$path = $this->cachePath;
		if ($pos !== false) {
			$path .= substr($key, 0, $pos);
			$key = substr($key, $pos + 1);
		}
		(!is_dir($path)) && FileSystem::makeDir($path);
		return $path . '/' . urlencode($key) . '.cache.php';
	}
}
?>