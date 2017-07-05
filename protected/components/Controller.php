<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();

	public function json($arr=array()){
		header("Content-Type: application/json;charset=utf-8");
		echo json_encode($arr);
		Yii::app()->end();
	}

	public function jsonOut($status, $msg='', $data=array()){
		$this->json(array('status'=>$status, 'msg'=>$msg, 'data'=>$data));
	}

	public function beforeAction($action){
		$installLock = WORK_PATH.'/install.lock';
		if(!file_exists($installLock) && $this->id!='install' && $this->id!='wiki'){
			$this->redirect('/install');
		}
		$guestControllers = array('wiki', 'install', 'hook');
		$guestActions = array('login', 'captcha', 'error');

		if(!Yii::app()->user->id && !in_array($action->id, $guestActions) && !in_array($this->id, $guestControllers)){
			$this->redirect('/site/login');
		}

		return parent::beforeAction($action);
	}

	public function msgOut($msg, $type='error', $target=''){
		if(Yii::app()->request->isAjaxRequest) $this->jsonOut(1001, $msg);

		if($target && isset($target['url'])) $target = array($target);
		$this->render('//site/notice',['type'=> $type, 'msg' => $msg, 'targets'=> $target]);
		Yii::app()->end();
	}
}