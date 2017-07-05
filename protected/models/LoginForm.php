<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginForm extends CFormModel
{
	public $username;
	public $password;
	public $rememberMe;
	public $verifyCode;

	private $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('username, password, verifyCode', 'required'),
			// rememberMe needs to be a boolean
			array('rememberMe', 'boolean'),
			array('verifyCode', 'captcha'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'username'=>'用户名',
			'password'=>'密码',
			'verifyCode' => '验证码',
			'rememberMe'=>'Remember me next time',
		);
	}

	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login()
	{
		if(empty($this->password)) $this->addError('password', '密码不能为空');
		elseif(empty($this->username)) $this->addError('password', '用户名不能为空');

		if(!$this->hasErrors()){
			if($this->_identity===null)
				$this->_identity=new UserIdentity($this->username,$this->password);
			if( $this->_identity->authenticate() ){
				$duration=$this->rememberMe ? 3600*24*30 : 0; // 30 days
				Yii::app()->user->login($this->_identity,$duration);
				return true;
			}

			$msg = $this->_identity->errorInfo ? $this->_identity->errorInfo : '用户名或密码有误';
			$this->addError('password', $msg);
		}
		return false;
	}
}
