<?php
$this->breadcrumbs=array(
	'Project Svns'=>array('index'),
	'Create',
);

$this->pageTitle = '创建项目';

$this->menu=array(
	array('label'=>'项目列表','url'=>array('admin')),
);

$this->beginWidget('bootstrap.widgets.TbWarp', array(
	'icon' =>'icon-file',
	'title'=>'创建项目',
));

echo $this->renderPartial('_form', array('model'=>$model));
$this->endWidget();