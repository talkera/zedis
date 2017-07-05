<?php
class DiffController extends Controller
{
	public function actionIndex($f, $pId){
		$project = $this->loadModel($pId);
		$dir = VC::workDir($project['markPath'], 'prod');
		$vcDev = $project->getVC('prod');

		$version = array();
		if(($to=Yii::app()->request->getParam('v', 0)) && ($id=Yii::app()->request->getParam('id', 0))){
			//todo 只支持svn
			$sql = "SELECT version FROM  product_detail WHERE `pId`=:p AND id<:id AND `file`=:f order by id desc limit 1";
			$preV = Yii::app()->db->createCommand($sql)->queryScalar(array(':p'=>$pId, ':id'=>$id,':f'=>$f));
			$version = array('from'=>(int)$preV, 'to'=>(int)$to);
		}

		$log = Log::getInstance('tmp', $project['markPath']);

		$res = $vcDev->diff("{$dir}/{$f}", $version);
		$this->render('/submit/diff', array(
			'diff' => $res ? $res['diff'] : '',
			'error' => $res ? '' : $log->get(),
			'fileArray' => $res['fileArray'],
			'cmd' => $res ? $res['cmd'] : '',
		));
	}

	public function loadModel($id)
	{
		$pIds = Yii::app()->user->getState('projects');
		if(!Yii::app()->user->getState('isAdmin') && -1!=$pIds && false===strpos($pIds, "{$id},")){
			throw new CHttpException(404,'The requested page does not exist.');
		}

		$model=Project::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
}
