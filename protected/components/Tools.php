<?php 
class Tools {
	public static function minExec($cmdList, $max=10, $timeout=30){
		if(!$cmdList || !is_array($cmdList)) return false;

		$tStart = time();
		$processList = array();
		$result = array();
		for($i=0; $i<$max && $cmdList; $i++){
			$handle = proc_open(array_shift($cmdList), array(
				0 => array("pipe", "r"),
				1 => array("pipe", "w"),
				2 => array("pipe", "w"),
			), $pipes);
			stream_set_blocking($pipes[1], 0);
			$processList[] = array('handle' => $handle, 'pipes' => $pipes);
			$result[] = array();
		}

		while($processList){
			foreach($processList as $key => $process){
				if(!is_resource($process['handle']) || feof($process['pipes'][1])){
					$result[$key]['exit_code'] = proc_close($process['handle']);
					unset($processList[$key]);
					if($cmdList){
						$handle = proc_open(array_shift($cmdList), array(
							0 => array("pipe", "r"),
							1 => array("pipe", "w"),
							2 => array("pipe", "w")
						), $pipes);
						stream_set_blocking($pipes[1], 0);
						$processList[] = array('handle' => $handle, 'pipes' => $pipes);
						$result[] = array();
					}
					continue;
				}
				$result[$key]['result'] .= fgets($process['pipes'][1], 1024);
			}
			if($timeout && (time() - $tStart > $timeout)) break;//进程完成或超时
		}
		//对超时的处理
		if($processList) foreach($processList as $key=>$process){
			$status = proc_get_status($process['handle']);
			posix_kill($status['pid'], 9);
			$result[$key]['exit_code'] = proc_close($process['handle']);
		}
		return $result;
	}

	public static function benchRSync($from, $targets, $fList=array()){
		$cmdList = array();
		if( is_array($fList) ) $fList = implode(' ', $fList);
		if(!trim($fList)) $fList = './';

		$exclude = is_dir("{$from}/.git") ? '.git' : '.svn';//git只有根目录有，svn低版本到处都有
		foreach($targets as $to){
			$cmdList[] = "cd {$from} && ".RSYNC_BIN." -avzRr --timeout=15  --exclude='{$exclude}' {$fList} {$to};";
		}
		$num = count($cmdList);
		$log = Log::getInstance();
		$log->log(implode("\n", $cmdList));
		$log->log("同步开始，总计同步服务器数量为：{$num}台");
		$res = self::minExec($cmdList, 10, 30);
		$success = $retry = $fail = 0; $retryCmd = array();
		foreach($res as $k=>$value){
			$k2 = $k++;
			switch($value['result']){
				case 0:
					$success++;
					$log->log("No.{$k2} success");
					$log->log($cmdList[$k]);
					$log->log($value['result']);
					break;
				case 9:
					$retry++; $retryCmd[$k2] = $cmdList[$k];
					$log->log("No.{$k2} need retry");
					$log->log($cmdList[$k]);
					$log->log($value['result']);
					break;
				default:
					$fail++;
					$log->log("No.{$k2} fail!!");
					$log->log($cmdList[$k]);
					$log->log($value['result']);
			}
		}
		$msg = "总共发送{$num}台服务器，成功{$success}台，失败{$fail}台，重试{$retry}台";
		$log->log($msg);

		if($retry) foreach($retryCmd as $k =>$value){
			$log->log("No.{$k} start to retry");
			exec($value, $result, $status);
			$log->log(implode("\n", $result));
		}
		return array('status'=>$fail, 'msg'=>$msg);
	}

	/**
	 * 分页函数
	 *
	 * $totalCount			总条目数
	 * $currentPage			当前页数
	 * $options.pageSize	每页条目数
	 * $options.items		当前页两边可点击的页码数最小为2
	 * $options.maxItems	当总页数不超过该数值时显示所有的页码
	 * $options.params		分页参数
	 * 需要在页面设置样式 .anxin-pages{text-align:center;margin:20px auto;}.anxin-pages span,.anxin-pages a{padding:5px 10px}.anxin-pages .current{background-color:#7eacd3;color:white;}
	 */
	public static function pages($totalCount, $currentPage, $options=[]){
		$options = array_merge([
			'pageSize'=>5,
			'items'=>4,
			'maxItems' => 7,
			'params' => [],
		], $options);
		$pageSize = max(1, (int)$options['pageSize']);
		$params = http_build_query($options['params']);
		$items = (int)$options['items'] + 1; $items = max($items, 3);

		$pages = ceil($totalCount/$pageSize);
		$currentPage = min($currentPage, $pages);
		$currentPage = max($currentPage, 1);

		$str = '';

		if($pages <= $options['maxItems']){
			for($i=1; $i<=$pages; $i++){
				if($currentPage == $i) $str .= "<li class='active'><a>{$i}</a></li>";
				else $str .= "<li><a href='?{$params}&p={$i}'>{$i}</a></li>";
			}
		}else{
			for($i=$currentPage-1; $i>0&&$i>($currentPage-$items); $i--) $str = "<li><a href='?{$params}&p={$i}'>{$i}</a></li>" . $str;
			if($currentPage>$items+1) $str = '<li><a>...</a></li>' . $str;
			if($currentPage>$items) $str = "<li><a href='?{$params}&p=1'>1</a></li>" . $str;

			$str .= "<li class='active'><a>{$currentPage}</a></li>";

			for($i=$currentPage+1; $i<=$pages&&$i<($currentPage+$items); $i++) $str .= "<li><a href='?{$params}&p={$i}'>{$i}</a></li>";
			if($currentPage<$pages-$items) $str .= '<li><a>...</a></li>';
			if($currentPage<$pages-$items+1) $str .= "<li><a href='?{$params}&p={$pages}'>{$pages}</a></li>";
		}
		if($currentPage>1) $str = "<li><a class='prevPage' href='?{$params}&p=".($currentPage-1)."'>上一页</a></li>" . $str;
		if($currentPage<$pages) $str .= "<li><a class='nextPage' href='?{$params}&p=".($currentPage+1)."'>下一页</a></li>";

		return $pages>1 ? "<div class='pagination'>{$str}</div>" : '';
	}

	public static function arrayExtract($data, $keys){
		if(!is_array($keys) || !$keys) return array();
		$res = array();
		foreach ($keys as $k=>$key){
			if(is_numeric($k)) $k = $key;
			if(!isset($data[$k])) continue;
			$res[$key] = $data[$k];
		}
		return $res;
	}
}