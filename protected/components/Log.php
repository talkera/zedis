<?php 
class Log {
	private static $_instance;

	//logs for projects
	private static $_pLog = array(
		'tmp' => 'tmp.log',
		'CL'=>'changedList.log',
		'C2P'=>'commitToProduct.log',
		'BS'=>'betaRSync.log',
		'PS'=>'productRSync.log',
	);

	//logs for system
	private static $_sLog = array(
		'PC'=>'postCommit.log',
	);

	private $_logs = array();
	private $_logPath = '';

	private function __construct(){}
	public static function getInstance($type='', $markPath=''){
		if(!self::$_instance){
			if(!$type) throw new CException('unknown log type');
			$path = self::getPath($type, $markPath);
			self::$_instance = new self;
			self::$_instance->changePath($type, $markPath);
		}
		return self::$_instance;
	}
	public static function logConf($type='p'){
		return $type=='p' ? self::$_pLog: self::$_sLog;
	}

	/**更换日志目录
	 * @param $type 日志类型
	 * @param $markPath 项目目录
	 * @throws CException
	 */
	public function changePath($type, $markPath=false){
		$path = self::getPath($type, $markPath);
		if($this->_logs) $this->_doWrite(true);
		$this->_logPath = $path;
		if($type == 'tmp') file_put_contents($this->_logPath, ''); //临时目录，只保存上一次记录
	}
	public static function getPath($type, $markPath=false){
		if($markPath){
			$path = VC::workDir($markPath, 'log');
			if(empty(self::$_pLog[$type])) throw new CException('unknown project log type');
			$path .= '/' . self::$_pLog[$type];
		}
		else{
			$path = WORK_PATH;
			if(empty(self::$_sLog[$type])) throw new CException('unknown system log type');
			$path .= '/' . self::$_sLog[$type];
		}
		return $path;
	}
	public function cPath(){return $this->_logPath;}

	public function log($log){
		if(!is_string($log)) $log = var_export($log, true);
		$this->_logs[] = date('Y-m-d H:i:s')."\n".$log."\n";
	}

	public function clean(){
		file_put_contents($this->_logPath, '');
	}

	private function _doWrite($append=true){
		if($append)
			file_put_contents($this->_logPath, implode('', $this->_logs), FILE_APPEND);
		else
			file_put_contents($this->_logPath, implode('', $this->_logs));
		$this->_logs = [];
	}

	public function __destruct(){
		$this->_doWrite(true);
	}

	public function get($type='string'){
		return $type=='string' ? implode('', $this->_logs): $this->_log;
	}
}
