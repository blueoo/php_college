<?php 
namespace xcmq;
use xlogger\module\XLogger as XLogger;
use xcmq\module\Xcmq_ActiveMQ as Xcmq_ActiveMQ;
use xcmq\module\Xcmq_RabbitMQ as Xcmq_RabbitMQ;
use xcmq\module\Xcmq_RabbitMQORG as Xcmq_RabbitMQORG;
/**
 *  Xcmq 类
 *
 * @package module
 * @author  谢<jc3wish@126.com>
 * 
 * $Id$
 */
class Xcmq
{

    const METADATA_FILE = '/tmp/metadata_tags';//该文件内容记录了服务器标识名(由运维人员维护)
    private static $_instance = array(); 
    
    private $configKey;
    
    // 配置内容
    private static $_configArr = array();
    
    // 判断文件是否加载
    private static $_configLoad=0;
    
    //配置文件中的 key 
    private $_configKey=null;
    
    //mq实例化对象
    private $_mq_conn = null;
    
    //xlogger日志模块
    private $_xlogger_module = 'xcmq';
    
    //xlogger 实例化对象
    private $_xlogger = null;
    
    //是否开启事务 
    private $_isTransaction = false;
    
    //写入数据list,用于人为开启事务
    private $_tranDataList = array();
    
    private $_lastSendError = array();

    //第几个消费者
    private $queueReceiveNum = null;

    //服务器标识
    private $_ServerName = '';

    //开始事务的情况下,轨迹消息先进入这个list
    private $_trailMsgList = array();

    //msg key 
    private $_msgKey = null;

    //当前连接vhost
    private $_vhost = '';

    private $_isBactchPush = false;
    
    //版本
    private static $_version = 'v3.0-beta.03';
    
    public function getVersion(){
        return self::$_version;
    }
    
    public static function singleton($configKey,$hearbeat=null)  
    {  
        if(!isset(self::$_instance[$configKey]))  
        {  
            $c=__CLASS__;  
            self::$_instance[$configKey] =new $c($configKey,$hearbeat);  
        }
        return self::$_instance[$configKey];  
    }
    
    function __construct($configKey,$hearbeat=null) 
    {
        $serverName = '';
        if (is_readable(self::METADATA_FILE)) {
            $fileInfo = file_get_contents(self::METADATA_FILE);
            $fileInfo = trim($fileInfo);
            $serverName = str_replace('name:', '', $fileInfo);
        }
        $this->_ServerName= strtolower($serverName);

        $this->configKey = $configKey;
        // 配置文件加载
        if( self::$_configLoad == 0 ){
            if(defined('XCMQ_CONF_FILE')){
                $file = XCMQ_CONF_FILE;
            }else{
                $file=XCMQ_API_PATH.'/config/mq_config.php';
            }
            self::$_configArr=include_once($file);
            self::$_configLoad = 1;
        }
        $m = self::$_configArr[$configKey]['mq'];
        
        if(!isset(self::$_configArr[$configKey]['reconnect']['read']['count'])){
            self::$_configArr[$configKey]['reconnect']['read']['count'] = 0;
        }
        if(!isset(self::$_configArr[$configKey]['reconnect']['read']['sleep'])){
            self::$_configArr[$configKey]['reconnect']['read']['sleep'] = 0;
        }
        if(!isset(self::$_configArr[$configKey]['reconnect']['write']['count'])){
            self::$_configArr[$configKey]['reconnect']['write']['count'] = 0;
        }
        if(!isset(self::$_configArr[$configKey]['reconnect']['write']['sleep'])){
            self::$_configArr[$configKey]['reconnect']['write']['sleep'] = 0;
        }

        // 初始化xlogger        
        try{
            $this->_xlogger = XLogger::getInstance($this->_xlogger_module);
        }catch (\Exception $e){
            return false;
        }

        switch ($m){
            case 'rabbitmq':
                $this->_mq_conn = new Xcmq_RabbitMQ();
                throw new \Exception('error:当前只支持 rabbitmqorg 配置');
                break;
            case 'rabbitmqorg':
                $this->_mq_conn = new Xcmq_RabbitMQORG();
                break;
            default:
                $this->_xlogger->setM('connect_error')->setData(array('msg'=>'mq配置有问题,应是 rabbitmq,activemq,rabbitmqorg 其中一个','base'=>self::$_configArr[$this->_configKey] ));
                throw new \Exception('error:当前只支持 rabbitmqorg 配置');
        }

        $this->_configKey = $configKey;
        $err = '';
        try{
            $this->_vhost = self::$_configArr[$configKey]['base']['vhost'];
            $this->_mq_conn->setHeartbeat($hearbeat);
            $this->_mq_conn->setTrailLogFun(array($this,'ConsumeAndAckTrailMsg'));

            if (isset(self::$_configArr[$configKey]['write_read_log']) && self::$_configArr[$configKey]['write_read_log']==true){
                $this->_mq_conn->SetLogFun(array($this,'writeSuccessLog'));
            }
            
            $this->_mq_conn->setQueInfo(self::$_configArr[$configKey]);
            $this->_mq_conn->connect();
        }catch (\Exception $e){
            $err = $e->getMessage();
            $this->_xlogger->setM('connect_error')->setData(array('msg'=>$err,'base'=>self::$_configArr[$this->_configKey] ));
            throw new \Exception('error:'.$err.' 请检查配置mq,base等配置是否正确'.' base:'.json_encode(self::$_configArr[$this->_configKey]['base']));
        }
    }
    
    // 对象连接，如果连接出错则写入错误日志
    public function reconnect(){
        $err = '';
        try{
            $this->_mq_conn->reConnect();
        }catch (\Exception $e){
            $err = $e->getMessage();
        }
        if( $err ){
            $this->_xlogger->setM('connect_error')->setData(array('msg'=>$err,'base'=>self::$_configArr[$this->_configKey] ));
            return false;
        }
        return true;
    }

    public function setWriteReadLog($bool){
        if($bool === true){
            $this->_mq_conn->SetLogFun(array($this,'writeSuccessLog'));
        }else{
            $this->_mq_conn->SetLogFun('');
        }
    }

    public function setMandatory($bool){
        $this->_mq_conn->setMandatory($bool);
        return $this;
    }
    
    public function setAttributes($p){
        $this->_mq_conn->setAttributes($p);
        $this->_p = $p;
    }
    
    public function setBatchPublish($bool){
        $this->_mq_conn->setBatchPublish($bool);
        $this->_isBactchPush = true;
        return $this;
    }
    
    public function setConfirm($bool=false){
        $this->_mq_conn->setConfirm($bool);
        return $this;
    }
    
    public function setWriteTimeout($timeout){
        $this->_mq_conn->setWriteTimeout($timeout);
        return $this;
    }
    
    public function waitConfirm(){
        $r = $this->_mq_conn->waitConfirm();
        if(is_array($r)){
            $tmp = array();
            foreach($r['error'] as $k=>$v){
                $this->_xlogger->setM('sendMQ')->setData($v);
                $tmp[md5(serialize($v['data']))] = 1;
            }
            foreach($this->_trailMsgList as $k=>$v){
                if (!isset( $tmp[md5(serialize($v['Data']))] ) ){
                    $this->writeTrailLog($v,1);
                }
            }
        }
        $this->_isBactchPush = false;
        //全部成功的情况下，全部写入日志
        foreach($this->_trailMsgList as $k=>$v){
            $this->writeTrailLog($v,1);
        }
        return $r;
    }

    //批量写入
    public function batchPublish($d,$routingKey='',$exchange='amq.direct'){
        $data = array();
        foreach($d as $k=>$v){
            if (is_array($v) && isset($v['Body'])){
                $data[$k] = array(
                    'Body'=>$v['Body'],
                    'MsgKey'=>isset($v['MsgKey'])?$v['MsgKey']:'',
                    'MsgId'=>isset($v['MsgKey'])?$this->_getUUID():'',
                );
            }else{
                $data[$k]=array(
                    'Body'=>$v,
                    'MsgKey'=>'',
                    'MsgId'=>'',
                );
            }
        }
        unset($d);

        $r = $this->_mq_conn->batchPublish($data,$routingKey,$exchange);
        if(is_array($r)){
            $tmp = array();
            foreach($r['error'] as $k=>$v){
                $v['routingkey'] = $routingKey;
                $v['exchange'] = $exchange;
                $this->_xlogger->setM('sendMQ')->setData($v);
                $tmp[md5(serialize($v['data']))] = 1;
            }
            foreach($data as $k=>$v){
                if (!isset( $tmp[md5(serialize($v['Body']))] ) ){
                    if($v['MsgKey']){
                        $this->writeTrailLog($this->_getSendMQTrailMsg($v['Body'],$v['MsgKey'],$v['MsgId'],$exchange,$routingKey),1);
                    }
                }
            }
        }else{
            foreach($data as $k=>$v){
                if($v['MsgKey']){
                    $this->writeTrailLog($this->_getSendMQTrailMsg($v['Body'],$v['MsgKey'],$v['MsgId'],$exchange,$routingKey),1);
                }
            }
        }
        return $r;
    }

    public function writeSuccessLog(array $data,$type){
        switch ($type) {
            //写入日志
            case 1:
                $this->_xlogger->setM('sendMQ')->setData($data);
                break;
            //消费日志
            case 2:
                $this->_xlogger->setM('receiveMQ')->setData($data);
                break;
        }
    }

    public function setMsgKey($key){
        $this->_msgKey = $key;
        return $this;
    }

    private function _getUUID(){
        return md5($this->_ServerName.uniqid(mt_rand(), true)); 
    }

    public function getDateTime($is_timestamp=false,$date='NOW'){
        $date = new \DateTime($date);
        $date->setTimezone(new \DateTimeZone('UTC'));
        if($is_timestamp){
            return strtotime($date->format('Y-m-d H:i:s'));
        }
        return $date->format('Y-m-d H:i:s');
    }

    private function writeTrailLog(array $data,$type){
        $data['Timestamp']=$this->getDateTime(true);
        $data['ServerName'] = $this->_ServerName;
        switch ($type) {
            case 1:
                $data['Type'] = 1;
                $this->_xlogger->setM('trail')->setData($data);
                break;
            //消费
            case 40:
                $data['Type'] = 40;
                $this->_xlogger->setM('trail')->setData($data);
                break;
            //unack
            case 44:
                $data['Type'] = 44;
                $this->_xlogger->setM('trail')->setData($data);
                break;
            //ack
            case 46:
                $data['Type'] = 46;
                $this->_xlogger->setM('trail')->setData($data);
                break;
        }
    }

    //写入时候的轨迹消息体
    private function _getSendMQTrailMsg($c,$MsgKey,$MsgId,$exchange,$routingkey){
        return array(
            'Data'=>$c,
            'MsgKey'=>$MsgKey,
            'MsgId'=>$MsgId,
            'Vhost'=>$this->_vhost,
            'Exchange'=>$exchange,
            'Routingkey'=>$routingkey,
            'QueueName'=>'',
            'CTraceId'=>'',
            'PTraceId'=>'',
        );
    }

    public function ConsumeAndAckTrailMsg($MsgId,$QueueName,$type){
        $this->writeTrailLog(
            array(
                'Data'=>'',
                'MsgKey'=>'',
                'MsgId'=>$MsgId,
                'Vhost'=>$this->_vhost,
                'Exchange'=>'',
                'Routingkey'=>'',
                'QueueName'=>$QueueName,
                'CTraceId'=>'',
                'PTraceId'=>'',
            ),$type
        );
    }
    
    
    public function sendMQ($data,$routingKey='',$exchange='amq.direct'){
        if( !$data ){
            $this->_lastSendError = array('error'=>array('data'=>$data,'msg'=>'$data is empty','code'=>105));
            return false;
        }
        
        if( !$this->_mq_conn ){
            throw new \Exception('连接不正常;'); 
            //$this->_lastSendError = array('error'=>array('data'=>$data,'msg'=>'connect is error','code'=>106));
            return false;
        }
        $msgId ='';
        if ($this->_msgKey){
            $msgId = $this->_getUUID();
        }
        //如果开启了事务的情况下 先写入数据池中，待commit的时候再执行写入
        if( $this->_isTransaction ){
            if ($this->_msgKey){
                $this->_trailMsgList[] = $this->_getSendMQTrailMsg($data,$this->_msgKey,$msgId,$exchange,$routingKey);
            }
            $this->_tranDataList[] = array($data,$routingKey,$exchange,$this->_msgKey,$msgId);
            return true;
        }
        try{
            $r =  $this->_mq_conn->setData($data,$routingKey,$exchange,$this->_msgKey,$msgId);
        }catch (\Exception $e){
            $r = array('error'=>array('data'=>$data,'msg'=>$e->getMessage(),'code'=>$e->getCode()));
        }
        
        if($r === true){
            if ($this->_msgKey){
                if ($this->_isBactchPush == false){
                    $this->writeTrailLog($this->_getSendMQTrailMsg($data,$this->_msgKey,$msgId,$exchange,$routingKey),1);
                }else{
                    $this->_trailMsgList[] = $this->_getSendMQTrailMsg($data,$this->_msgKey,$msgId,$exchange,$routingKey);
                }
            }
            $this->_msgKey = '';
            return true;
        }else{
            //$err = $r->getMessage();
            $this->_lastSendError = $r;
            $this->_xlogger->setM('sendMQ')->setData($r);
            
            //假如没有人工开启事务的情况下，自动重连接操作
            if(!$this->_isTransaction){
                
                $c = self::$_configArr[$this->_configKey]['reconnect']['write']['count']*1;
                $t = self::$_configArr[$this->_configKey]['reconnect']['write']['sleep'];
                $t = $t?$t:0.1;
                $t *= 1;
                
                //当设置每次写入失败重连次数大于0的情况下，默认为1，则进行重连写入
                while( $c > 0 ){
                    $c--;
                    // 假如连接操作存在异常或者返回false 则进行下一次循环
                    $r = $this->reconnect();
                    if( !$r ){
                        sleep($t);
                        continue;
                    }
                    //假如写入返回值非bool值 true 参数则进行直接返回
                    try{
                        if( true === $this->_mq_conn->setData($data,$routingKey,$exchange,$this->_msgKey,$msgId) ){
                            if ($this->_msgKey){
                                if ($this->_isBactchPush == false){
                                    $this->writeTrailLog($this->_getSendMQTrailMsg($data,$this->_msgKey,$msgId,$exchange,$routingKey),1);
                                }else{
                                    $this->_trailMsgList[] = $this->_getSendMQTrailMsg($data,$this->_msgKey,$msgId,$exchange,$routingKey); 
                                }
                            }
                            return true;
                        }else{
                            sleep($t);
                            continue;
                        }
                    }catch (\Exception $e){
                        
                    }
                }
            }
            if(isset($e)){
                throw $e; 
            }
            return false;
        }
    }
    
    public function getLastSendError(){
        return $this->_lastSendError;
    }
    
    //事务提交的时候，统一把数据写入到mq中
    private function _saveData(){
        try{
            $this->_mq_conn->begin();
            foreach( $this->_tranDataList as $k=>$v ){
                $r = $this->_mq_conn->setData($v[0],$v[1],$v[2],$v[3],$v[4]);
                if( $r !== true ){
                    $this->_mq_conn->rollback();
                    return false;
                }
            }
            $this->_mq_conn->commit();
            
        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
            return false;
        }
        
        //提交成功,清空事务队列数据
        $this->_tranDataList = array();
        return true;
    }
    
    
    public function begin(){
        $this->_isTransaction = true;
        return true;
    }
    
    public function commit(){
        if( !$this->_isTransaction ){
            return false;
        }
        
        $c = self::$_configArr[$this->_configKey]['reconnect']['write']['count']*1;
        $t = self::$_configArr[$this->_configKey]['reconnect']['write']['sleep'];
        $t = $t?$t:0.1;
        $t *= 1;
        $c = $c?$c:1;
        
        $do = false;
        echo '$c='.$c."\r\n";
        while( $c > 0 ){
            $c--;
            try{
                $r = $this->_saveData();
            }catch (\Exception $e){
                print_r($e->getMessage());
            }
            if( $r ){
                $do = true;
                break;
            }
            //假如连接操作存在异常或者返回false 则进行下一次循环
            $r = $this->reconnect();
            if( !$r ){
                sleep($t);
                continue;
            }
        }
        if( $do ){
            $this->_isTransaction = false;
            foreach($this->_trailMsgList as $k=>$v){
                $this->writeTrailLog($v,1);
            }
            $this->_trailMsgList = array();
            return true;
        }
        throw new \Exception($e->getMessage(),$e->getCode()); 
        return false;
    }  
    
    public function rollback(){
        //清空事务队列数据
        $this->_tranDataList = array();
        
        $this->_isTransaction = false;
        
        return true;
    }
    
    public function pcntl_signal_signo($signo){
        switch ($signo) {
            case SIGTERM:
            case SIGINT:
                @$this->_xlogger->setM('receiveMQ')->setData(array('msg'=>self::$_configArr[$this->_configKey]['base']['vhost'].'-'.$this->queuekey.' receiveMQ end.'));
                exit();
        }
    }

    //当前队列第几个消费
    //$i 第几个消费者
    //$force 是否强制启动
    public function setReceiveNum($i,$force=false){
        $this->queueReceiveNum = $i;
        $this->queueReceiveForce = $force;
    }
    
    public function receiveMQ($queuekey,$callback){
        if(!$callback ){
            return false;
        }
        if( !$this->_mq_conn ){
            return false;
        }
        if(!is_callable($callback)){
            throw new \Exception('callback 参数不正确,不是合法的可调用结构' );
            return false;
        }
        $this->queuekey = $queuekey;
        $this->_xlogger->setM('receiveMQ')->setData(array('msg'=>self::$_configArr[$this->_configKey]['base']['vhost'].'-'.$queuekey.' receiveMQ start...'));
        if(function_exists('pcntl_signal')){
            pcntl_signal(SIGTERM, array($this,'pcntl_signal_signo'));
            pcntl_signal(SIGINT, array($this,'pcntl_signal_signo'));
        }
        
        $c = self::$_configArr[$this->_configKey]['reconnect']['read']['count'];
        $c = $c?$c:0;
        $c *= 1;
        //假如$c 值 ，即最大循环次数为1，则默认为2，因为要加上第一次连接操作次数
        $c = ($c==0)?1:$c;
        
        $t = self::$_configArr[$this->_configKey]['reconnect']['read']['sleep'];
        $t *= 1;
        $this->queueReceiveForce = false;
        $this->receiveStart($queuekey);
        register_shutdown_function(array($this,'receiveShutdown'));
        $thec = $c;
        while( $thec > 0 ){
            $thec = $thec-1;
            // 假如连接成功，下次连接失败 可重连接次数恢复
            try{
                $this->_mq_conn->getData(self::$_configArr[$this->_configKey]['queue'][$queuekey],$callback);
            }catch (\Exception $e){
                $this->_xlogger->setM('receiveMQ')->setData(array('msg'=>$e->getMessage(),'code'=>$e->getCode()));
            }
            sleep($t);
            //假如连接操作存在异常或者返回false,如果要重连的次数大于1，那则进行下一次循环
            if ( $thec > 0 ){
                $r = $this->reconnect();
                if( !$r ){
                    continue;
                }else{
                    $thec = $c;
                }
            }
        }
        $this->_xlogger->setM('receiveMQ')->setData(array('msg'=>self::$_configArr[$this->_configKey]['base']['vhost'].'-'.$queuekey.' receiveMQ end.'));
        throw new \Exception($e->getMessage(),$e->getCode()); 
        return false;
    }
    
    public function close(){
        if($this->_mq_conn){
            $this->_mq_conn->close();
        }
        unset(self::$_instance[$this->configKey]);
        unset($this->_mq_conn,$this->_xlogger);
    }
    
    public function __destruct(){
        if(isset($this->_mq_conn)){
            $this->_mq_conn->close();
            unset($this->_mq_conn,$this->_xlogger);
            unset(self::$_instance[$this->configKey]);
        }
    }

    public function receiveStart($queuekey){
        if ($this->queueReceiveNum == null){
            return;
        }
        if(!isset($this->queueReceiveForce)){
            return false;
        }
        if(self::$_configArr[$this->_configKey]['base']['vhost']=='/'){
            $a = '';
        }else{
            $a = self::$_configArr[$this->_configKey]['base']['vhost'];
        }

        $this->queueReceiveTag = $a.'-'.self::$_configArr[$this->_configKey]['queue'][$queuekey]['queue_name'].'-'.$this->queueReceiveNum;
        $dir = strtoupper(substr(PHP_OS,0,3))==='WIN'?'./':'/tmp/';
        $file = $dir.'rabbit_rec_'.$this->queueReceiveTag.'.lock';
        if(is_file($file) && !$this->queueReceiveForce){
            throw new \Exception($this->queueReceiveTag.' 已经在运行中，如果确认没有运行，请删除文件:'.$file);
            return false;
        }
        $this->queueReceiveTagStatus = true;
        file_put_contents($file,getmypid());
        return true;
    }

    public function receiveShutdown(){
        if (!isset($this->queueReceiveTagStatus) || !$this->queueReceiveTagStatus){
            return false;
        }
        $dir = strtoupper(substr(PHP_OS,0,3))==='WIN'?'./':'/tmp/';
        $file = $dir.'rabbit_rec_'.$this->queueReceiveTag.'.lock';
        @unlink($file);
    }

}
?>