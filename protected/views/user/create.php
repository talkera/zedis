<?php
$this->breadcrumbs=array(
	'Users'=>array('index'),
	'Create',
);

$this->pageTitle = '管理后台-User创建';

$this->menu=array(
	array('label'=>'管理 User','url'=>array('admin')),
);

$this->beginWidget('bootstrap.widgets.TbWarp', array(
	'icon' =>'icon-file',
	'title'=>'创建 User',
));

echo $this->renderPartial('_form', array('model'=>$model));
$this->endWidget();