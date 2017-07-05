<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property string $id
 * @property string $name
 * @property string $pwd
 * @property integer $type
 * @property string $projects
 * @property string $rights
 * @property string $cTime
 */
class User extends CActiveRecord
{
	public static $typeConf = array(1=>'普通用户', 2=>'管理员');
	public static $statusConf = array(0=>'无效',1=>'有效');

	/*设置密码*/
	public function setPassword($pwd){
		$this->salt = substr(uniqid(), -6);
		$this->pwd = self::pwdEncrypt($pwd, $this->salt);
	}

	//用户的密码明文转换成密文
	public static function pwdEncrypt($pwd, $code){
		return md5(substr(md5($pwd.$code), 1,-1));
	}

	public static function loadRights($user){
		if($user['type']==2){
			$isAdmin = 1;
			$rights = -1;
			$projects = -1;
		}else{
			$isAdmin = 0;
			$projects = $user['projects'];
			$rights = json_decode($user['rights'], true);
		}
		Yii::app()->user->setState('isAdmin', $isAdmin);
		Yii::app()->user->setState('projects', $projects);
		Yii::app()->user->setState('pRights', $rights);
		Yii::app()->user->setState('svnName', $user['svnName']);
	}
	public static function hasRights($pId, $right){
		if(Yii::app()->user->getState('isAdmin') || -1==Yii::app()->user->getState('projects'))
			return true;
		$rights = Yii::app()->user->getState('pRights');
		return !empty($rights[$pId][$right]);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name,svnName', 'required'),
			array('type,status', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>30),
			array('pwd', 'length', 'max'=>32),
			array('projects, rights, salt,cTime,mTime', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, pwd, type, projects, rights, cTime', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => '用户名',
			'pwd' => '密码',
			'type' => '用户类型',//1普通用户2管理员
			'projects' => '项目',
			'rights' => '权限',
			'status' => '状态',
			'cTime' => '创建时间',
			'mTime' => '修改时间',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('pwd',$this->pwd,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('projects',$this->projects,true);
		$criteria->compare('rights',$this->rights,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('cTime',$this->cTime,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
