<?php

class ProjectController extends Controller
{
	public function beforeAction($action){
		parent::beforeAction($action);
		if(!Yii::app()->user->getState('isAdmin') && in_array($action->id, array('create', 'update', 'delete')))
			throw new CHttpException(403, '无权访问次页面！如已授权，请重新加载权限后尝试');

		return true;
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Project;

		if(isset($_POST['Project']))
		{
			$model->attributes=$_POST['Project'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Project']))
		{
			$model->attributes=$_POST['Project'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$project = $this->loadModel($id);
			$project->status = 0;
			$project->save();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$this->redirect(['admin']);
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Project('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Project']))
			$model->attributes=$_GET['Project'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$pIds = Yii::app()->user->getState('projects');
		if(!Yii::app()->user->getState('isAdmin') && -1!=$pIds && false==strpos("{$id},", $pIds)){
			throw new CHttpException(404,'The requested page does not exist.');
		}

		$model=Project::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='project-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	/**初始化
	 * @param $pId
	 */
	public function actionInit($pId){
		$project = $this->loadModel($pId);
		$path = VC::workDir($project->markPath);
		is_dir($path) || mkdir($path, 0777, true) || $this->jsonOut(1001, '目录不可写:'.$path);

		$log = Log::getInstance('tmp', $project->markPath);
		$parts = Project::getParts();
		foreach($parts as $type){
			$res = $project->doInit($type);
			if($res[0] !== 0) $this->jsonOut(1001, $res[1], array('log'=>$log->get('string')));
		}
		$project->status = 2;
		$project->save();
		$this->jsonOut($res[0],  '初始化成功');
	}

	/**重置工作目录
	 * @param $pId
	 * @param $type
	 */
	public function actionReset($pId, $type){
		$allType = Project::getParts();
		if($type!=='all' && !in_array($type, $allType)){
			Yii::app()->user->setFlash('resetInfo', '重置失败：操作类型错误');
			$this->redirect(['view', 'id'=>$pId]);
		}
		$project = $this->loadModel($pId);
		$types = $type=='all' ? $allType : array($type);

		try{
			Log::getInstance('tmp', $project->markPath);
			foreach($types as $type){
				$project->resetPath($type);
			}
			Yii::app()->user->setFlash('resetInfo', '重置成功！');
		}catch (Exception $e){
			Yii::app()->user->setFlash('resetInfo', $e->getMessage());
		}
		$this->redirect(['view', 'id'=>$pId]);
	}

	public function actionLog($pId){
		$project = $this->loadModel($pId);
		$this->render('log', array(
			'project' => $project,
		));
	}
}
