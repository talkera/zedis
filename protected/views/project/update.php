<?php
$this->breadcrumbs=array(
	'Project Svns'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->pageTitle = '更新项目 #'.$model->id;

$this->menu=array(
	array('label'=>'创建项目','url'=>array('create')),
	array('label'=>'查看项目','url'=>array('view','id'=>$model->id)),
	array('label'=>'项目列表','url'=>array('admin')),
);

$this->beginWidget('bootstrap.widgets.TbWarp', array(
	'icon' =>'icon-edit',
	'title'=>'更新项目 '.$model->id,
));

echo $this->renderPartial('_form',array('model'=>$model));

$this->endWidget();