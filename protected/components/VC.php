<?php
class VC
{
	/**
	 * @param Project $project
	 * @param array $actionList
	 * @return bool
	 */
	public static function changeList($project, $actionList=array()){
		$log = Log::getInstance();
		$vcDev = $project->getVC('dev');
		$vcProd = $project->getVC('prod');
		$dirDev = self::workDir($project['markPath'], 'dev');
		$dirProd = self::workDir($project['markPath'], 'prod');

		if(!$vcDev->update($dirDev)){
			$log->log('update dev code error:'.$dirDev);return;
		}

		if(! self::sync($dirDev, $dirProd) ){
			$log->log('sync to product dir error:'.$dirDev);return;
		}
		$toAdd = $toDel = array();
		if($actionList) foreach($actionList as $item){
			$item['file'] = escapeshellarg($item['file']);
			switch($item['action']){
				case 'A': $toAdd[] = $item['file']; break;
				case 'D': $toDel[] = $item['file'];	break;
			}
		}
		if($toAdd && !$vcProd->add($toAdd, $toAdd)){
			$log->log('product code add error:'.$dirProd); return;
		}
		if($toDel && !$vcProd->del($toDel, $toDel)){
			$log->log('product code delete error:'.$dirProd); return;
		}
		return self::updateChangeList($project);
	}

	/**
	 * @param string $from
	 * @param string $to
	 * @param string $exclude
	 * @param string|array $fList
	 * @return bool
	 */
	public static function sync($from, $to, $fList=''){
		$log = Log::getInstance();
		if( is_array($fList) ) $fList = implode(' ', $fList);
		if(!trim($fList)) $fList = './';
		$exclude = is_dir("{$from}/.git") ? '.git' : '.svn';//git只有根目录有，svn低版本到处都有
		$cmd = "cd {$from} && ".RSYNC_BIN." -avzRr --delete --timeout=15  --exclude='{$exclude}' {$fList} {$to};";
		$log->log($cmd);

		exec($cmd, $result, $status);
		$log->log(implode("\n", $result));

		return 0==$status;
	}

	/**
	 * @param Project $project
	 * @return bool
	 */
	public static function updateChangeList($project){
		$log = Log::getInstance();
		$markPath = $project['markPath'];

		$vcDev = $project->getVC('dev');
		$vcProd = $project->getVC('prod');
		$dirDev = self::workDir($project['markPath'], 'dev');
		$dirProd = self::workDir($project['markPath'], 'prod');

		$modifyStatus = $vcProd->status($dirProd);
		if(!$modifyStatus){ $log->log('get product status error:'.$dirProd); return; }
		$fList = array();
		foreach($modifyStatus['fList'] as $item){
			if($item['status']=='A' || $item['status']=='M')
				$fList[] = escapeshellarg($item['file']);
		}
		if($fList){
			$fileInfo = $vcDev->fileInfo($dirDev, $fList);
			if(!$fileInfo){ $log->log('file info error:'.$dirDev); return; }
			foreach($fileInfo as $file=>$item){
				$modifyStatus['fList'][$file] = array_merge($modifyStatus['fList'][$file], $item);
			}
		}

		$statusDir = self::workDir($markPath);
		if(!is_dir($statusDir) && !mkdir($statusDir, 0777, true)){
			$log->log('status log path is not writable:'.$statusDir); return;
		}
		$svnStatusLog = "{$statusDir}/status.log";
		//{'file1.php':{'file':'file1.php','date':'2017-05-05T04:47:50.504199Z','version':'1', 'author':'abc', 'status':'M', 'type':'file'}}
		if(!file_put_contents($svnStatusLog, json_encode($modifyStatus))){
			$log->log('save status log error:'.$svnStatusLog); return;
		}
		return true;
	}

	public static function factory($type, $repo, $user, $pwd){
		if($type == 1)
			return new SVN($repo, $user, $pwd);
		else
			throw new CException('unsupported type');
	}

	/** 目前日志都在项目下，如果需要可以对日志单独处理
	 * @param $markPath
	 * @param $type string dev|prod|beta
	 * @return bool|string
	 */
	public static function workDir($markPath, $type=''){
		if(!in_array($type, array('prod','dev','beta'))) $type='';
		$markPath = trim($markPath,'/');
		$markPath = str_replace(array(' ','/'),array('','_'),$markPath);
		return WORK_PATH."/{$markPath}/{$type}";
	}
	public static function checkVCDir($path){
		return is_dir("{$path}/.svn") || is_dir("{$path}/.git");
	}
}