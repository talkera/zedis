<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	private $_id;
	public $errorInfo;

	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		$user = Yii::app()->db->createCommand('select * from user where name=:n')->queryRow(true, [':n'=>$this->username]);
		if($user && User::pwdEncrypt($this->password, $user['salt'])==$user['pwd']){
			if($user['status'] == 0){
				$this->errorInfo = '账号已被冻结，请联系管理员！';
				return false;
			}
			$this->username = $user['name'];
			$this->_id = $user['id'];
			User::loadRights($user);
			return true;
		}
		return false;
	}

	public function getId()
	{
		return $this->_id;
	}
}