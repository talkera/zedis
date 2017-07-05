<?php
class SubmitController extends Controller
{
	/**将dev环境变更的代码提交到product的代码库中
	 * 并记录到数据库中，记录更新后的product仓库version，以及更新的文件列表
	 * record中记录product的version
	 * @param $pId
	 * @throws CHttpException
	 */
	public function actionChanged($pId){
		if(!Yii::app()->user->getState('isAdmin') && -1!=Yii::app()->user->getState('projects')){
			$rights = Yii::app()->user->getState('pRights');
			if(empty($rights[$pId]['sub'])) throw new CHttpException(403, '没有操作权限');
		}
		$this->pageTitle = '提交变更';
		$project = Project::model()->findByPk($pId);
		if(!$project) throw new CHttpException(404, '项目不存在');

		$error = '';
		if(Yii::app()->request->isPostRequest){
			$cmt = trim(strip_tags($_POST['comment']));
			if(empty($_POST['file'])) $error = '请选择你需要提交的文件!';
			elseif(!$cmt) $error = '请填写注释!';
			if(!$error) try{
				$vcProd = $project->getVC('prod');
				$dirProd = VC::workDir($project['markPath'], 'prod');
				if(!VC::checkVCDir($dirProd)) throw new CException('product目录被破坏，需要重置！');

				$log = Log::getInstance('C2P', $project['markPath']);

				$fList = $fListDetail = $toAdd = array();
				foreach($_POST['file'] as $item){
					if(empty($item['file'])) continue;
					if(!$tmp = escapeshellarg($item['file'])) continue;

					if($item['action']=='?') $toAdd[] = $tmp;
					$fList[] = $tmp;
					$fListDetail[] = $item;
				}
				if($toAdd && !$vcProd->add($dirProd, $fList)){
					$log->log('add file error:'.var_export($fList,true));
					throw new CException('product目录添加文件失败，需要重置！');
				}
				$res = $vcProd->commit($dirProd, $_POST['comment'], $fList);
				if(!$res) throw new CException('product目录commit失败，需要重置！');

				if(!VC::updateChangeList($project)){
					throw new CException('检查变更失败，请查看日志处理后手动检查变更。日志目录:'.$log->cPath());
				}

				//get svn info
				$svnInfoOfRepo = $vcProd->lastVersionInfo($project['pRepo']);
				if(!$svnInfoOfRepo){
					$log->log('获取 product last version info error.解决后请重新提交代码');
					throw new CException('获取 product last version info error，详情请查看日志:'.$log->cPath());
				}

				$now = date('Y-m-d H:i:s');
				//online_svn_log add
				Yii::app()->db->createCommand()->insert('product_record', array(
					'pId' => $project['id'],
					'version' => $svnInfoOfRepo['commitRevision'],//生产环境代码库版本号
					'comment' => $cmt,
					'author' => Yii::app()->user->name,
					'cTime' => $now,
				));
				$lastId = Yii::app()->db->getLastInsertID();

				foreach($fListDetail as $item){
					Yii::app()->db->createCommand()->insert('product_detail', array(
						'pId' => $project['id'],
						'recordId' => $lastId,
						'version' => $svnInfoOfRepo['commitRevision'],//生产环境代码库版本号
						'file' => $item['file'],
						'author' => $item['author'],
						'action' => $item['action'],
						'cTime' => $now,
					));
				}
				$this->redirect(['product','pId'=>$project['id']]);return;
			}catch(Exception $e){
				$error = $e->getMessage();
			}
		}

		$statusLog = VC::workDir($project['markPath']) . '/status.log';
		if(!file_exists($statusLog)){
			$error = '变更获取有误，请在svn中重新提交或手动检查变更!';
			$statusInfo = array('fList'=>array());
		}else{
			$statusInfo = json_decode(file_get_contents($statusLog), true);
		}

		$user = Yii::app()->request->getQuery('type');
		if($user!=='all') $user = Yii::app()->user->getState('svnName');
		if(!$user) $user = 'all';

		$fList = array();
		if($statusInfo['fList']){
			foreach($statusInfo['fList'] as $file=>$info){
				if($user!='all' && $user!=$info['author']) continue;
				$fList[] = array(
					'file' => $file,
					'fileUEncode' => urlencode($file),
					'action' => $info['status'],
					'type' => $info['type'],
					'author' => $info['author'],
					'date' => date('Y-m-d H:i:s',strtotime($info['date'])),
					'version' => $info['version'],
				);
			}
			usort($fList, array(__CLASS__, '_sortFile'));
		}

		$this->render('changed',array(
			'user' => $user,
			'msg' => $statusInfo['msg'],
			'error' => $error,
			'fList' => $fList,
			'project' => $project,
		));
	}

	public function actionProduct($pId){
		$this->pageTitle = '代码部署';
		$project = Project::model()->findByPk($pId);
		if(!$project) throw new CHttpException(404, '项目不存在');

		$pSize = 20;
		$cPage = Yii::app()->request->getParam('p', 1); $cPage = max(1, (int)$cPage);
		$db = Yii::app()->db;
		$count = $db->createCommand('SELECT count(1) FROM product_record where pId=:pId')->queryScalar(array(':pId'=>$pId));

		$s = ($cPage-1) * $pSize;
		$logData = $db->createCommand("select * from product_record where pId=:pId order by id desc limit {$s},{$pSize}")
			->queryAll(1,array(':pId'=>$pId));

		$this->render('product',array(
			'project' => $project,
			'data' => $logData,
			'totalCount' => $count,
			'cPage' => $cPage,
			'pSize' => $pSize,
		));
	}

	public function actionUpdateBeta($pId, $submitId, $version){
		if(!Yii::app()->user->getState('isAdmin') && -1!=Yii::app()->user->getState('projects')){
			$rights = Yii::app()->user->getState('pRights');
			if(empty($rights[$pId]['beta'])) throw new CHttpException(403, '没有操作权限');
		}

		$project = Project::model()->findByPk($pId);
		if(!$project) throw new CHttpException(404, '项目不存在');
		$record = Yii::app()->db->createCommand('select * from product_record where id=:id')
			->queryRow(1,array(':id'=>$submitId));
		if($record['pId']!=$project['id']) throw new CHttpException(403, 'error request');

		$log = Log::getInstance('BS', $project['markPath']);
		$returnUrl = $this->createUrl('product', array('pId'=>$project['id']));//成功或失败都返回部署页

		try{
			$betaDir = VC::workDir($project['markPath'], 'beta');
			if(!VC::checkVCDir($betaDir)) throw new CException('beta目录被破坏，需要重置！');
			if(Yii::app()->request->getParam('type', 'all') == 'part'){//增量更新
				Yii::app()->db->createCommand()->update('product_record',array(
					'isPartBeta'=>1,
				),'id=:id',array(':id'=>$submitId));
				$fList = Yii::app()->db->createCommand('select file from product_detail where recordId=:rId')
					->queryColumn(array(':rId'=>$submitId));
			}else{
				Yii::app()->db->createCommand()->update('product_record',array(
					'isBeta'=>0,'isPartBeta'=>0,
				),'pId=:pId',array(':pId'=>$project['id']));//清掉所有增量备份
				Yii::app()->db->createCommand()->update('product_record',array(
					'isBeta'=>1,
				),'id=:id and pId=:pId',array(':id'=>$submitId, ':pId'=>$project['id']));
				$fList = '';
			}

			$vcProd = $project->getVC('prod');
			if(!$vcProd->update($betaDir, $fList, $version))
				throw new CException('beta临时仓库 update error！请重置beta目录！');
			if($project['betaServers']){
				$log->changePath('BS', $project['markPath']);
				$servers = str_replace("\r\n","\n",trim($project['betaServers']));
				$servers = explode("\n", $servers);

				foreach($servers as $k=>$s) if(!trim($s)){unset($servers[$k]);}
				$res = Tools::benchRSync($betaDir, $servers);
				Yii::app()->user->setFlash('changed', $res['msg']);
			}

			$this->redirect($returnUrl);
		}catch (Exception $e){
			$this->msgOut($e->getMessage(), 'error', array('url'=>$returnUrl, 'label'=>'返回'));
		}
	}

	//将beta代码发布到线上
	public function actionDistribute($pId){
		if(!Yii::app()->user->getState('isAdmin') && -1!=Yii::app()->user->getState('projects')){
			$rights = Yii::app()->user->getState('pRights');
			if(empty($rights[$pId]['prod'])) throw new CHttpException(403, '没有操作权限');
		}

		$project = Project::model()->findByPk($pId);
		if(!$project) throw new CHttpException(404, '项目不存在');

		$betaDir = VC::workDir($project['markPath'], 'beta');
		if(!VC::checkVCDir($betaDir)) throw new CException('beta目录被破坏，需要重置！');

		Log::getInstance('PS', $project['markPath']);

		if($project['prodServers']){
			$servers = str_replace("\r\n","\n",trim($project['prodServers']));
			$servers = explode("\n", $servers);

			foreach($servers as $k=>$s) if(!trim($s)){unset($servers[$k]);}
			$res = Tools::benchRSync($betaDir, $servers);
			Yii::app()->user->setFlash('changed', $res['msg']);
		}

		Yii::app()->db->createCommand('update product_record set isOnline=isBeta, isPartOnline=isPartBeta where pId=:pId')
			->execute(array(':pId'=>$pId));

		$this->redirect(array('product', 'pId'=>$pId));
	}

	public function actionDetail($id){
		$record = Yii::app()->db->createCommand('select * from product_record where id=:id')
			->queryRow(1, array(':id'=>$id));
		if(!$record) throw new CHttpException(404, '项目不存在');

		$pId = $record['pId'];
		if(!Yii::app()->user->getState('isAdmin') && -1!=Yii::app()->user->getState('projects')){
			$rights = Yii::app()->user->getState('pRights');
			if(empty($rights[$pId])) throw new CHttpException(403, '没有操作权限');
		}

		$project = Project::model()->findByPk($pId);
		$detail = Yii::app()->db->createCommand('select * from product_detail where recordId=:rId')
			->queryAll(1, array(':rId'=>$id));
		$this->render('detail',array(
			'project' => $project,
			'data' => $detail,
			'record' => $record,
		));
	}

	private static function _sortFile($a, $b){
		if($a['action'] < $b['action']) return 1;
		if($a['action'] > $b['action']) return -1;
		if ($a['file'] == $b['file']) return 0;
		return ($a['file'] < $b['file']) ? -1 : 1;
	}
}
