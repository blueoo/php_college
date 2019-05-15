<?php
namespace orm;
class Mysql_DB{
	
	private static $_instance=array();
	private $_sql='';
	private $_dataValues='';
	private $_tableName='';
	private $_where='';
	private $_limit=array();
	
	public function __construct($params){
		 try{
			$this->_connect = mysql_connect($params['host'],$params['user'],$params['password']);
			mysql_select_db($params['dbname'],$this->_connect);
			$params['charset']=$params['charset']?$params['charset']:'UTF8';
			mysql_query('SET NAMES '.$params['charset']);
		 }catch(Exception $e){
			//throw($e->getMessage()); 
		 }
		
	}
	
    public static function getInstance($data) {
		if(!$data){
			return false;
		}
		$key=md5(implode(',',$data));
        if (is_null(self::$_instance[$key])) {
            self::$_instance[$key] = new self($data);
        }
		return self::$_instance[$key];
    }
	
	public function table($tableName){
		$this->_tableName=$tableName;
		return $this;
	}
	
	public function insert(){
		
		if( !$this->_dataValues || $this->_tableName ){
			return false;
		}
		$sql = 'INSERT INTO ' . $this->_tableName;
		$key='';
		$val='';
		foreach( $this->_dataValues as $k=>$v ){
			$key .= $k.',';
			$key .= "'".$v."',";		
		}
		$sql .= ' ('.substr($key,0,-1).' ) VALUES ('.substr($val,0,-1).')';
		
		return $this->query($sql);
	}
	
	public function replace(){
		
		if( !$this->_dataValues || $this->_tableName ){
			return false;
		}
		$sql = 'REPLACE INTO' . $this->_tableName;
		$key='';
		$val='';
		foreach( $this->_dataValues as $k=>$v ){
			$key .= $k.',';
			$key .= "'".$v."',";		
		}
		$sql .= ' ('.substr($key,0,-1).' ) VALUES ('.substr($val,0,-1).')';
		
		return $this->query($sql);	
	}

	public function insertKeyUpdate(){
		if( !$this->_dataValues || $this->_tableName ){
			return false;
		}
		$sql = 'INSERT INTO ' . $this->_tableName;
		$key='';
		$val='';
		$updateValues='';
		foreach( $this->_dataValues as $k=>$v ){
			$key .= $k.',';
			$val .= "'".$v."',";
			$updateValues .= "$k='$v',";
		}
		$sql .= ' ('.substr($key,0,-1).') VALUES ('.substr($val,0,-1).')';
		$sql .= ' ON DUPLICATE KEY UPDATE '.substr($updateValues,0,-1);
		return $this->query($sql);			
	}
	
	public function addWhere($data){
		if( !is_array($data) ){
			return false;
		}
		$this->_where +=$data;
	}
	
	public function select( $fileds='*' ){
		$this->_fileds=$fileds;	
	}
	
	public function groupby($filed){
		if( !$filed ){
			return false;
		}
		$this->_groupby=$filed;
		return $this;
	}
	
	public function orderby($filed){
		if( !$filed ){
			return false;
		}
		$this->_orderby=$filed;	
		return $this;
	}
	
	public function getCount($filed='*'){
		$this->_fileds="COUNT($filed)";
		$sql='SELECT '.$this->_fileds.' FROM '.$this->_tableName ;
		if( $this->_where ){
			$sql .= ' WHERE ';
			foreach( $this->_where as $k=>$v ){
				$sql .= $v;
			}
		}
		
		if( $this->_groupby ){
			$sql .= ' GROUP BY '.$this->_groupby;
		}

		if( $this->_having ){
			$sql .= ' HAVING '.$this->_having;
		}
		$r = $this->getOne($sql);
		return $r;	
	}
	
	public function setValues($data){
		if(!is_array($data)){
			return false;
		}
		$this->_dataValues += $data;
		return $this;
	}
	
	public function limit($limit,$start=0){
		$this->_limits=array(
			'limit'=>$limit?$limit*1:1000,
			'start'=>$start,	
		);
		return $this;
	}
	
	public function delete(){
		$sql='DELETE FROM '.$this->_tableName ;
		if( $this->_where ){
			$sql .= ' WHERE ';
			foreach( $this->_where as $k=>$v ){
				$sql .= $v;
			}
		}
		return $this->query($sql);	
	}
	
	public function update(){
		if( !$this->_dataValues || $this->_tableName ){
			return false;
		}
		$updateValues='';
		foreach( $this->_dataValues as $k=>$v ){
			$updateValues .= "$k='$v',";
		}
		$sql = 'UPDATE ' . $this->_tableName .' SET '.substr($updateValues,0,-1);
		
		if( $this->_where ){
			$sql .= ' WHERE ';
			foreach( $this->_where as $k=>$v ){
				$sql .= $v;
			}
		}
		return $this->query($sql);		
	}
	
	public function getLastId(){
		return mysql_insert_id();
	}

	public function query($sql){
		return mysql_query($sql);	
	}
	
	// 受影响行数
	public function getAffectRows(){
		return mysql_affected_rows($this->connect);
	}
	
	private function _getSql(){
		$sql="SELECT $this->fileds FROM ".$this->_tableName ;
		if( $this->_where ){
			$sql .= ' WHERE '.implode('AND',$this->_where);
		}
	
		if( $this->_orderby ){
			$sql .= ' ORDER BY '.$this->_orderby;
		}
		
		if( $this->_groupby ){
			$sql .= ' GROUP BY '.$this->_groupby;
		}

		if( $this->_having ){
			$sql .= ' HAVING '.$this->_having;
		}
		
		if( $this->_limits ){
			$sql .= ' LIMIT '.$this->_limits['start'].','.$this->_limits['limit'];		
		}
		return $sql;
	}
	
	public function getAll($key=null,$sql='',$fetchMode=_ORM_FETCH_ASSOC){
		if( !$sql ){
			$sql=$this->_getSql();
		}
		$result=mysql_query($sql);
		while($d=mysql_fetch_assoc($result)){
			if( $key ){
				$data[$key]=$d;
			}else{
				$data[]=$d;
			}
		}
		$this->_unset();
		return $data;	
	}

	public function getOne($sql=null){
		if(!$sql){
			$sql=$this->_getSql();
		}
		$this->_unset();
		$result=mysql_query($sql);
		$d=mysql_fetch_row($result);
		return $d;
	}
	
	private _unset(){
		$this->_groupby='';
		$this->_orderby='';
		$this->_having='';
		$this->_limits=array();
		return $this;
	}
	
}