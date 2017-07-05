<?php 
class SVN {
	public static $cmdPre = ' export LC_CTYPE=en_US.UTF-8; export LC_ALL=en_US.UTF-8; export LANG=zh_CN.UTF-8; ';

	private $_repo;
	private $_user;
	private $_pwd;

	public function __construct($repo, $user, $pwd){
		$this->_repo = $repo;
		$this->_user = $user;
		$this->_pwd = $pwd;
	}

	public function checkout($path, $repo, $version=0){
		$log = Log::getInstance();
		$version = (int)$version;
		$version = $version > 0 ? " -r {$version} " : '';
		$user = $this->_user;
		$pwd = $this->_pwd;
		$cmd = self::$cmdPre . SVN_BIN ." checkout {$repo} {$path} {$version} ".
				"--username {$user} --password {$pwd} --no-auth-cache --non-interactive --trust-server-cert 2>&1;";
		$log->log($cmd);

		exec($cmd, $result, $status);
		$log->log(implode("\n", $result));

		return 0==$status;
	}

	public function update($path, $fList='', $version=0){
		$log = Log::getInstance();
		$version = (int)$version;
		$version = $version > 0 ? " -r {$version} " : '';
		if( is_array($fList) ) $fList = implode(' ', $fList);

		$user = $this->_user;
		$pwd = $this->_pwd;

		$cmd = self::$cmdPre . " cd {$path} && ".SVN_BIN." update {$fList} --force {$version} ".
			"--username {$user} --password {$pwd} --no-auth-cache --non-interactive --trust-server-cert 2>&1;";
		$log->log($cmd);

		exec($cmd, $result, $status);
		$log->log(implode("\n", $result));

		return 0==$status;
	}

	public function add($codeDir, $fList){
		if(!is_array($fList) || empty($fList)) return false;
		$log = Log::getInstance();
		$fList = implode(' ', $fList);
		$user = $this->_user;
		$pwd = $this->_pwd;
		$cmd = self::$cmdPre . "cd {$codeDir}; ".SVN_BIN." add --force --parents {$fList} ".
				"--username {$user} --password {$pwd} --no-auth-cache --non-interactive  --trust-server-cert 2>&1;";
		$log->log($cmd);

		exec($cmd, $result, $status);
		$log->log(implode("\n", $result));

		return 0==$status;
	}

	public function del($codeDir, $fList){
		if(!is_array($fList) || empty($fList)) return false;
		$log = Log::getInstance();
		$fList = implode(' ', $fList);
		$user = $this->_user;
		$pwd = $this->_pwd;
		$cmd = self::$cmdPre . "cd {$codeDir}; ".SVN_BIN." delete --force --parents {$fList} ".
			"--username {$user} --password {$pwd} --no-auth-cache --non-interactive  --trust-server-cert 2>&1;";
		$log->log($cmd);

		exec($cmd, $result, $status);
		$log->log(implode("\n", $result));

		return 0==$status;
	}

	public function status($path){
		$log = Log::getInstance();
		if(!is_dir("{$path}/.svn")){
			$log->log("{$path} is not a svn dir");
			return;
		}

		$user = $this->_user;
		$pwd = $this->_pwd;
		$cmd = self::$cmdPre . " cd {$path} && ".SVN_BIN." status --xml ".
			"--username {$user} --password {$pwd} --no-auth-cache --non-interactive  --trust-server-cert 2>&1;";
		$log->log($cmd);

		exec($cmd, $result, $status);
		$result = implode("\n", $result);
		$log->log($result);

		$xml = simplexml_load_string($result);
		if(!$xml || $status !== 0){
			$log->log($path.' svn status error');return;
		}
		$svnStatus = array('unversioned'=>'?', 'added'=>'A', 'deleted'=>'D', 'modified'=>'M');
		$a='wc-status';//此key无法直接获取，只能先放到变量中
		$fList = []; $msg = '';
		foreach($xml->target->entry as $value){
			$file = (string)$value->attributes()->path;
			$status = (string)$value->$a->attributes()->item;
			if(isset($svnStatus[$status])){
				$fList[$file] = array(
					'file'=>$file, 'status'=>$svnStatus[$status],
					'type'=>is_dir("{$path}/{$file}") ? 'dir' : 'file',
				);
			}else{
				$msg .= "{$file}:{$status}\t";
			}
		}
		return array('fList'=>$fList, 'msg'=>$msg);
	}

	/** 获取列表中文件的具体修改信息
	 * @param $path svn目录
	 * @param $user
	 * @param $pwd
	 * @param $fList
	 * @return array
	 */
	public function fileInfo($path, $fList){
		$log = Log::getInstance();
		if( is_array($fList) ) $fList = implode(' ', $fList);

		$user = $this->_user;
		$pwd = $this->_pwd;
		$cmd = self::$cmdPre . " cd {$path} && ".SVN_BIN." info --xml {$fList} ".
			"--username $user --password $pwd --no-auth-cache --non-interactive --trust-server-cert 2>&1;";
		$log->log($cmd);

		exec($cmd, $result, $status);
		$result = implode("\n", $result);
		$log->log($result);

		//svn info 有时候出错，最后一行是错误提示
		if($status !== 0){
			$error = array_pop($result);
			if(strpos($error,'E155037') && strpos($error,'cleanup')){
				$cmd2 = self::$cmdPre . " cd {$path} && ".SVN_BIN." cleanup";
				$log->log($path.' svn need clean up:'.$cmd2);

				exec($cmd2, $result, $status);
				if($status==0){//cleanup成功 再次获取fileInfo
					$log->log('cleanup successfully, try to get fileInfo again.');
					exec($cmd, $result, $status);
				}
				if($status!==0) return;
			}else{
				$log->log('get svn info failed:'.$path);
				return;
			}
		}

		$xml = simplexml_load_string($result);
		if(!$xml){
			$log->log($path.' fileInfo is not xml object');return;
		}
		$res = array();
		foreach($xml->entry as $item){
			$file = (string)$item->attributes()->path;
			$res[$file] = array(
				//'file'=>$file,
				//'kind'=>(string)$item->attributes()->kind,
				'author'=>(string)$item->commit->author,
				'version'=>(string)$item->commit->attributes()->revision,
				'date'=>(string)$item->commit->date,
			);
		}
		return $res;
	}

	public function commit($path, $comment, $fList=''){
		$log = Log::getInstance();
		if( is_array($fList) ) $fList = implode(' ', $fList);
		if($fList) $fList = " --force-log {$fList} ";
		$user = $this->_user;
		$pwd = $this->_pwd;
		$cmd = self::$cmdPre . " cd {$path} && ".SVN_BIN." commit {$fList} -m '{$comment}' ".
			"--username $user --password $pwd --no-auth-cache --non-interactive --trust-server-cert 2>&1;";
		$log->log($cmd);

		exec($cmd, $result, $status);
		$log->log(implode("\n", $result));

		return 0==$status;
	}

	public function lastVersionInfo($repo){
		$log = Log::getInstance();
		$user = $this->_user;
		$pwd = $this->_pwd;
		$cmd = self::$cmdPre . " ".SVN_BIN." info {$repo} --xml ".
			"--username $user --password $pwd --no-auth-cache --non-interactive --trust-server-cert 2>&1;";
		$log->log($cmd);
		exec($cmd, $result,$status);
		$result = implode("\n", $result);
		$log->log($result);
		$xml = simplexml_load_string($result);
		if(!$xml){
			$log->log('fileInfo is not xml object:'.$repo);return;
		}
		return array(
			'kind' => (string)$xml->entry->attributes()->kind,
			'path' => (string)$xml->entry->attributes()->path,
			'revision' => (string)$xml->entry->attributes()->revision,
			'commitRevision' => (string)$xml->entry->commit->attributes()->revision,
			'author' => (string)$xml->entry->attributes()->author,
			'date' => (string)$xml->entry->attributes()->date,
		);
	}

	public function diff($file, $versions=array()){
		$user = $this->_user;
		$pwd = $this->_pwd;
		$log = Log::getInstance('tmp');
		if($versions){
			$cmd = self::$cmdPre . " ".SVN_BIN." diff {$file} -r{$versions['from']}:{$versions['to']} "
				."--username $user --password $pwd --no-auth-cache --non-interactive";
			exec($cmd, $results, $status);
			$log->log($cmd);
			if(0!==$status){
				$log->log($results);
				return false;
			}
			$cmd = self::$cmdPre . " ".SVN_BIN." cat {$file} -r{$versions['to']} "
				."--username $user --password $pwd --no-auth-cache --non-interactive";
			exec($cmd, $fileArray, $status);
		}else{
			$cmd = self::$cmdPre . " ".SVN_BIN." diff --username $user --password $pwd --no-auth-cache --non-interactive $file";
			exec($cmd, $results, $status);
			$log->log($cmd);
			if(0!==$status){
				$log->log($results);
				return false;
			}
			$fileArray = file($file);
		}

		$pure = array();
		foreach($results as $k=>$item){
			if($k>3 && $item != '\ No newline at end of file') //去掉头四行和无用行
				$pure[] = $item;
		}

		$result = array();//diff结果保存于此
		$blockIndex = -1;//diff块索引
		$counter = 0;//原文件变更计数器
		foreach($pure as $item){
			if(preg_match('|^@@ -(\d+),(\d+) \+(\d+),(\d+) @@$|', $item, $match)){
				/*处理新块之前先对之前的块进行平账*/
				if($counter>0) while($counter-- > 0) $result[$blockIndex]['new']['code'][$newLN++] = null;
				if($counter<0) while($counter++ < 0) $result[$blockIndex]['org']['code'][$orgLN++] = null;

				$blockIndex++;
				$result[$blockIndex] = array(
					'org' => array('begin'=>$match[1], 'count'=>$match[2], 'code'=>array()),
					'new' => array('begin'=>$match[3], 'count'=>$match[4], 'code'=>array()),
				);
				$orgLN = $match[1];//原文件变更起始行
				$newLN = $match[3];//新文件变更起始行
				$counter = 0;
				continue;
			}
			if($item[0] == ' '){//无改动
				/*处理无改动之前先对之前差异进行平账*/
				if($counter>0) while($counter-- > 0) $result[$blockIndex]['new']['code'][$newLN++] = null;
				if($counter<0) while($counter++ < 0) $result[$blockIndex]['org']['code'][$orgLN++] = null;

				$item = substr($item, 1);
				$result[$blockIndex]['org']['code'][$orgLN++] = $item;
				$result[$blockIndex]['new']['code'][$newLN++] = $item;
			}
			elseif($item[0] == '+'){//新文件的行
				$result[$blockIndex]['new']['code'][$newLN++] = $item;
				$counter--;//原文件落后一行
			}
			elseif($item[0] == '-'){//原文件有改动或被删除的行
				$result[$blockIndex]['org']['code'][$orgLN++] = $item;
				$counter++;//原文件多出一行
			}
		}
		/*对最后一批差异进行平账*/
		if($counter>0) while($counter-- > 0) $result[$blockIndex]['new']['code'][$newLN++] = null;
		if($counter<0) while($counter++ < 0) $result[$blockIndex]['org']['code'][$orgLN++] = null;
		return array('diff'=>$result, 'fileArray'=>$fileArray, 'cmd'=>$cmd);
	}
}
