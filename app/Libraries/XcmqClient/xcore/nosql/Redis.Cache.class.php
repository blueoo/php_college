<?php 
namespace nosql;
/**
 *  Redis 类
 *
 * @package lib
 * @subpackage plugins.cache
 * @author  谢<jc3wish@126.com>
 * 
 * $Id$
 */
class Redis_Cache
{
	/**
	 * @var Memcache $memcache Memcached 缓存连接对象
	 * @access public
	 */
	var $redis = NULL;
	
	/**
	 * @var string $prefix 变量前缀
	 */
	var $prefix = '';
	
	
    /**
     * 数据查询的统计
     *
     * @var Array
     */
    static public $querys = array();
    
    
    /**
     * 数据缓存沲
     *
     * @var Array
     */
    static public $data = array();

	/*
	*	单例池
	*/
	private static $_instance = array();    
    
	/**
	 * 构造函数
	 * @param string $host Redis 服务器的主机名或IP地址或者为服务器组相关信息
	 * @param int $port 端口号
	 * @param int $timeout 超时时间
	 */
	private function __construct($host = 'localhost', $port = 11211, $timeout = 60) 
	{
    	$this->redis = new \redis();
		if( is_array($host) ){
			$this->redis->connect($host['host'], $host['port']);
		}else{
			$this->redis->connect($host, $port);
		}
    }

	public static function getInstance($host = 'localhost', $port = 11211, $timeout = 60)
	{	
		$a = md5(serialize($host).$port);
		if(!self::$_instance[$a])
		{
			self::$_instance[$a] = new self($host, $port, $timeout);
		}
		return self::$_instance[$a];
	}
	
	public function getConnect(){
		return $this->redis;
	}
	
    
	/**
	 * 析构函数
	 
	function __destruct() 
	{
    #	$this->redis->close();
    }
	*/
    
	/**
	 * 在cache中设置键为$key的项的值，如果该项不存在，则新建一个项
	 * @param string $key 键值
	 * @param mix $var 值
	 * @param int $expire 到期秒数
	 * @param int $flag 标志位
	 * @return bool 如果成功则返回 TRUE，失败则返回 FALSE。
	 * @access public
	 */
    function set($key, $var, $expire = 0, $flag = 0) 
    {
		$key = $this->prefix . $key;
		
	    if (DEBUG)
	       self::$querys[] = "set " . $key . ' ' . $var;	  

	    if (isset(self::$data[$key]))
			self::$data[$key] = '';
	    		
		return $this->redis->set($key, $var, $flag, $expire);
	}
	
	
	/**
	 * 在cache中获取键为$key的项的值
	 * @param string $key 键值
	 * @return string 如果该项不存在，则返回false
	 * @access public
	 */
    function get($key) 
    {
		global $global;
		$key = (empty($this->prefix)) ? $key : $this->prefix . $key;
		
		$s_key = is_array($key) ? serialize($key) : $key;

		if (empty(self::$data[$s_key]))
		{
		    if (DEBUG)
		       self::$querys[] = "get " . (is_array($key) ? implode(',', $key) : $key);
		       		
			self::$data[$s_key] = $this->redis->get($key);			
		}
			
		return self::$data[$s_key];
	}
	
	
	/**
	 * 在MC中获取为$key的自增ID
	 *
	 * @param string $key	 自增$key键值
	 * @param integer $count 自增量,默认为1
	 * @return 				 成功返回自增后的数值,失败返回false
	 */
	function increment($key, $count = 1) 
	{
		return $this->redis->incr($key, $count);
	}
	
	
	/**
	 * 清空cache中所有项
	 * @return 如果成功则返回 TRUE，失败则返回 FALSE。
	 * @access public
	 */
    function flush() 
    {
		return $this->redis->flush();
	}
	
	
	/**
	 * 删除在cache中键为$key的项的值
	 * @param string $key 键值
	 * @return 如果成功则返回 TRUE，失败则返回 FALSE。
	 * @access public
	 */
    function delete($key) 
    {
		return $this->redis->delete($this->prefix . $key);
	}
}
?>