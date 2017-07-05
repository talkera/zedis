<?php
$this->breadcrumbs=array(
	'Users'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->pageTitle = '管理后台-User更新 #'.$model->id;

$this->menu=array(
	array('label'=>'创建 User','url'=>array('create')),
	array('label'=>'查看 User','url'=>array('view','id'=>$model->id)),
	array('label'=>'管理 User','url'=>array('admin')),
);

$this->beginWidget('bootstrap.widgets.TbWarp', array(
	'icon' =>'icon-edit',
	'title'=>'更新 User '.$model->id,
));

echo $this->renderPartial('_form',array('model'=>$model));

$this->endWidget();