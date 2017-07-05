<?php

/**
 * This is the model class for table "project".
 *
 * The followings are the available columns in table 'project':
 * @property string $id
 * @property string $name
 * @property string $markPath
 * @property string $pRepo
 * @property integer $pType
 * @property string $pUser
 * @property string $pPwd
 * @property string $dRepo
 * @property integer $dType
 * @property string $dUser
 * @property string $dPwd
 * @property string $betaServers
 * @property string $prodServers
 * @property integer $status
 */
class Project extends CActiveRecord
{
	//由于依赖关系，必须先创建日志目录 status必须在dev和prod后面
	private static $_parts = array('log','dev','prod','beta','status');
	public static function getParts(){return self::$_parts;}

	public static $vcConf = array(1=>'SVN');

	private $_VCDev;
	private $_VCProd;
	public function getVC($type){
		if($this->isNewRecord)
			throw new CException('this is a new project');
		if($type == 'dev'){
			if(!$this->_VCDev){
				$this->_VCDev = VC::factory($this->dType, $this->dRepo, $this->dUser, $this->dPwd);
			}
			return $this->_VCDev;
		}else{
			if(!$this->_VCProd){
				$this->_VCProd = VC::factory($this->pType, $this->pRepo, $this->pUser, $this->pPwd);
			}
			return $this->_VCProd;
		}
	}

	public static $statusConf = array(
		0 => '已删除',
		1 => '需要初始化',
		2 => '正常',
	);
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'project';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, markPath, pRepo, pUser, pPwd, dRepo, dUser, dPwd', 'required'),
			array('pType, dType, status', 'numerical', 'integerOnly'=>true),
			array('name, pUser, pPwd, dUser, dPwd', 'length', 'max'=>30),
			array('markPath, pRepo, dRepo', 'length', 'max'=>100),
			array('betaServers, prodServers', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, markPath, pRepo, pType, pUser, pPwd, dRepo, dType, dUser, dPwd, betaServers, prodServers, status', 'safe', 'on'=>'search'),
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
			'name' => '项目名称',
			'markPath' => '路径标识',
			'pRepo' => '生产环境仓库',
			'pType' => '生产环境仓库类型',
			'pUser' => '生产环境账户',
			'pPwd' => '生产环境密码',
			'dRepo' => '开发环境仓库',
			'dType' => '开发环境仓库类型',
			'dUser' => '开发环境账户',
			'dPwd' => '开发环境密码',
			'betaServers' => '仿真环境服务器',
			'prodServers' => '生产环境服务器',
			'status' => '状态',
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
		$criteria->compare('markPath',$this->markPath,true);
		$criteria->compare('pRepo',$this->pRepo,true);
		$criteria->compare('pType',$this->pType);
		$criteria->compare('pUser',$this->pUser,true);
		$criteria->compare('pPwd',$this->pPwd,true);
		$criteria->compare('dRepo',$this->dRepo,true);
		$criteria->compare('dType',$this->dType);
		$criteria->compare('dUser',$this->dUser,true);
		$criteria->compare('dPwd',$this->dPwd,true);
		$criteria->compare('betaServers',$this->betaServers,true);
		$criteria->compare('prodServers',$this->prodServers,true);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
				'defaultOrder'=>'status desc, id DESC',
			),
			'pagination'=>array(
				'pageSize'=>20,
			),
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Project the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function beforeSave(){
		if($this->isNewRecord){
			$this->status = 1;
		}
		$path = VC::workDir($this->markPath);
		if(!is_dir($path) && !mkdir($path, 0777, true)){
			$this->addError('markPath', '无法为该项目创建工作目录：'.$path);
			return false;
		}
		return parent::beforeSave();
	}

	/**重置项目工作目录：删除数据 重新checkout
	 * @param $type
	 * @return bool
	 * @throws CException
	 */
	public function resetPath($type){
		if(!in_array($type, self::$_parts)) throw new CException('错误的操作类型');
		if($type == 'status'){
			$path = VC::workDir($this->markPath).'/status.log';
			return unlink($path) && SVN::changedList($this->attributes);
		}elseif($type == 'log'){
			//todo 日志不应当reset
		}else{
			$path = VC::workDir($this->markPath, $type);
			$cmd = "cd {$path} && rm -rf ./*";
			exec($cmd, $result, $status);
			if(0!==$status) return false;

			$res = $this->doInit($type);
			if($res[0]===0) return true;
			else throw new CException($res[1], $res[0]);
		}
	}

	/**创建目录，并checkout
	 * @param $type
	 * @throws CException
	 */
	public function doInit($type){
		$path = VC::workDir($this->markPath, $type);
		if(!is_dir($path) && !mkdir($path, 0777, true))
			return array(1001, '无法为该项目创建工作目录：'.$path);

		$vcDev = $this->getVC('dev');
		$vcProd = $this->getVC('prod');

		switch($type){
			case 'dev':
				return $vcDev->checkout($path, $this->dRepo)
					? array(0, '开发环境临时代码仓库创建成功：'.$path)
					: array(1001, '开发环境临时代码仓库创建失败:'.$path);
			case 'prod':
				return $vcProd->checkout($path, $this->pRepo)
					? array(0, '生产环境临时代码仓库创建成功：'.$path)
					: array(1001, '生产环境临时代码仓库创建失败:'.$path);
			case 'beta':
				return $vcProd->checkout($path, $this->pRepo)
					? array(0, '仿真环境临时代码仓库创建成功：'.$path)
					: array(1001, '生产环境临时代码仓库创建失败:'.$path);
			case 'log':
				return array(0, '日志目录创建成功：'.$path);
			case 'status':
				return VC::changeList($this)
					? array(0, '检查变更成功：'.$path)
					: array(1001, '检查变更失败：'.$path);

		}
	}
}
