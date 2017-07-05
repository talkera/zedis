<?php
class WikiController extends Controller
{
	public $menu = array(
		array('label'=>'使用', 'url'=>array('index','page'=>'index')),
		array('label'=>'安装', 'url'=>array('index','page'=>'install')),
		array('label'=>'权限', 'url'=>array('index','page'=>'rights')),
		array('label'=>'其他', 'url'=>array('index','page'=>'others')),
	);
	public function actionIndex(){
		$conf = array('index','install','rights','others');
		$page = Yii::app()->request->getParam('page','index');
		if(!in_array($page, $conf)) $page = 'index';
		$this->render($page);
	}
}
