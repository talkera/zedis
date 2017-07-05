<?php

class UserController extends Controller
{
	public function beforeAction($action){
		parent::beforeAction($action);

		$actions = array('reloadRights');
		if(!in_array($action->id, $actions) && !Yii::app()->user->getState('isAdmin'))
			throw new CHttpException(404,'The requested page does not exist.');
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

	public function actionReloadRights(){
		$user = $this->loadModel(Yii::app()->user->id);
		User::loadRights($user);
		$this->jsonOut(0);
	}

	public function actionRights($id){
		if(!Yii::app()->user->getState('isAdmin')) throw new CHttpException(403, '只有管理员可以访问！');

		$user = $this->loadModel($id);
		if($user->type ==2 ) throw new CHttpException(403, '该用户已经是管理员，不可编辑权限！');
		$projectList = Yii::app()->db->createCommand('select * from project where status=2')->queryAll();

		if(Yii::app()->request->isPostRequest){
			if(isset($_POST['all']) && $_POST['all']=='-1'){
				$user->projects = -1;
				$user->rights = -1;
				$user->save();
			}elseif(isset($_POST['project'])){
				$rights = $projects = array();
				foreach($_POST['project'] as $pId=>$item){
					if(empty($item['sub']) && empty($item['dis'])) continue;
					$projects[] = $pId;
					$rights[$pId] = $item;
				}
				$user->projects = implode(',', $projects).',';
				$user->rights = json_encode($rights);
				$user->save();
			}
		}
		if($user->rights!=-1) $rights = json_decode($user->rights,true);

		$this->render('rights',array(
			'model'=>$user,
			'isAll' => $user->projects==-1,
			'rights'=>$rights,
			'projectList' =>$projectList,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new User;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			$model->setPassword($_POST['User']['pwd']);
			$model->cTime = date('Y-m-d H:i:s');
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));

			$model->pwd = $_POST['User']['pwd'];
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
		$model->pwd = '';

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			$model->setPassword($_POST['User']['pwd']);
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
			$model->pwd = $_POST['User']['pwd'];
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
			$model = $this->loadModel($id);
			$model->status = 0;
			$model->save();

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
		$model=new User('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['User']))
			$model->attributes=$_GET['User'];

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
		$model=User::model()->findByPk($id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='user-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
