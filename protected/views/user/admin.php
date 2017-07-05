<?php
$this->breadcrumbs=array(
	'Users'=>array('index'),
	'Manage',
);

$this->pageTitle = '管理后台-User列表页';

$this->menu=array(
	array('label'=>'创建 User','url'=>array('create')),
);

$this->beginWidget('bootstrap.widgets.TbWarp', array(
	'icon' =>'icon-list-alt',
	'title'=>'管理 Users',
));

$this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'user-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'name',
		'svnName',
		array('name'=>'type', 'value'=>'User::$typeConf[$data->type]','filter'=>User::$typeConf),
		array('name'=>'status', 'value'=>'User::$statusConf[$data->status]','filter'=>User::$statusConf),
		/*
		'cTime',
		*/
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
			'template'=>'{view} {update}<br>{rights} {delete}',
			'buttons' => array(
				'rights' => array(
					'label' => '权限',
					'url' => 'Yii::app()->createUrl("user/rights", array("id"=>$data->id))',
					'options' => array('class'=>'icon-link'),
					'visible' => '$data->type==1',
				),
			),
		),
	),
));

$this->endWidget();