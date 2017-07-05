<?php
class InstallController extends Controller
{
	public $layout='//layouts/column2';
/*
svn --version
rsync --version
is_dir(workpath) or mkdir(workpath)
if(!file_put_contents(workpath))
	ask for 777
db connect
*/
	public function actionIndex(){
		$workPath = WORK_PATH;
		$installLock = WORK_PATH.'/install.lock';
		$installed = file_exists($installLock);
		$error = '';
		if(Yii::app()->request->isPostRequest && !$installed){
			if(!is_dir($workPath) && !mkdir($workPath, 0777, true)){
				$error = '安装失败：创建工作目录失败！';
			}elseif(!file_put_contents($installLock, date('Y-m-d H:i:s'))){
				$error = '安装失败：请保证对工作目录有写权限！';
			}else{
				$installed = true;
			}
		}

		$this->render('/layouts/install', array(
			'installed' => $installed,
			'workPath' => $workPath,
			'error' => $error,
		));
	}
}
