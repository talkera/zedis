<?php
/**
 * Hook
 */
class HookController extends Controller
{
	//检查变更
	public function actionDoDiff($pId){
		$project = Project::model()->findByPk($pId);
		if(!$project||$project->status!=2) $this->jsonOut(1001, '项目不存在');

		$log = Log::getInstance('CL', $project['markPath']);
		if(VC::changeList($project)){
			$this->jsonOut(0, '完成');
		}else{
			$this->jsonOut(1001, '失败，详情请查看日志:'.$log->cPath(), array('log'=>$log->get('string')));
		}
	}

	/**
	 * svn post-commit hook
	 */
	public function actionSvnPostCommit(){
		set_time_limit(0);
		$log = Log::getInstance('PC', false);//no project, system log
		$log->log($_POST);

		$items = str_replace("\r\n", "\n", $_POST['changed']);
		$items = explode("\n", $items);

		//get all svn projects
		$projects = Project::model()->findAllByAttributes(array('status'=>2, 'dType'=>1));
		if(!$projects) return ;

		$actionList = [];
		foreach($items as $item){
			list($action, $file) = preg_split('/\s+/', trim($item));
			foreach($projects as $p){
				if(strpos($file, $p['markPath'])!==0) continue;

				$k = $p['id'];
				if(empty($actionList[$k]))
					$actionList[$k] = array('changed'=>true, 'aList'=>array());
				$actionList[$k]['aList'][] = array('file'=>str_replace($p['markPath'],'',$file), 'action'=>$action);
			}
		}
		foreach($projects as $p){
			if(empty($actionList[$p['id']])) continue;
			$log->changePath('CL', $p['markPath']);
			VC::changeList($p, $actionList[$p['id']]['aList']);
		}
	}
	public function actionGitPostCommit(){}
}