<?php
$this->breadcrumbs=array(
	'Users'=>array('index'),
	$model->name,
);

$this->pageTitle = '管理后台-User查看 #'.$model->id;

$this->menu=array(
	array('label'=>'创建 User','url'=>array('create')),
	array('label'=>'更新 User','url'=>array('update','id'=>$model->id)),
	array('label'=>'删除 User','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'管理 User','url'=>array('admin')),
);


$this->beginWidget('bootstrap.widgets.TbWarp', array(
	'icon' =>'icon-eye-open',
	'title'=>'查看 User #'.$model->id,
));

$this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'svnName',
		array('name'=>'type', 'value'=>User::$typeConf[$model->type]),
		array('name'=>'status', 'value'=>User::$statusConf[$model->status]),
		'cTime',
		'mTime',
	),
));

$this->endWidget();
