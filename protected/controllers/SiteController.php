<?php

class SiteController extends Controller
{
	public $layout = '//layouts/column2';
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
				'height' => 40,
				'width' => 90,
				'maxLength'=> 6,
				'minLength'=> 6,
				'offset' => -3,
				'padding' => 0,
				'testLimit' => 1,
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		$this->redirect(['/project/admin']);
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		//不能重复登录
		if(Yii::app()->user->id){
			$this->redirect(Yii::app()->homeUrl);
		}

		$this->layout = '//layouts/column2';
		$model=new LoginForm;
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}

	/** 用户修改自己的密码 */
	public function actionResetPassword(){
		$error = '';

		if(isset($_POST['User'])){
			$user = User::model()->findByPk(Yii::app()->user->id);
			if( $user['pwd'] != User::pwdEncrypt($_POST['User']['password'],$user['salt']) ){
				$error = '原密码不正确';
			}elseif($_POST['User']['newPassword'] != $_POST['User']['newPasswordRepeat']){
				$error = '两次输入不一致';
			}else{
				$user->setPassword($_POST['User']['newPassword']);
				if($user->save()){
					Yii::app()->user->logout();
					$this->redirect(Yii::app()->homeUrl);
				}else{
					$error = '修改失败，请稍后再试';
				}
			}
		}

		$this->render('resetPassword',array(
			'error' => $error,
		));
	}
}