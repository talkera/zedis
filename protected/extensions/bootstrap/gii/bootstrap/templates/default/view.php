<?php
/**
 * The following variables are available in this template:
 * - $this: the BootCrudCode object
 */
?>
<?php
echo "<?php\n";
$nameColumn=$this->guessNameColumn($this->tableSchema->columns);
$label=$this->pluralize($this->class2name($this->modelClass));
echo "\$this->breadcrumbs=array(
	'$label'=>array('index'),
	\$model->{$nameColumn},
);\n";
?>

$this->pageTitle = '管理后台-<?php echo $this->modelClass; ?>查看 #'.<?php echo "\$model->{$this->tableSchema->primaryKey}"; ?>;

$this->menu=array(
	array('label'=>'创建 <?php echo $this->modelClass; ?>','url'=>array('create')),
	array('label'=>'更新 <?php echo $this->modelClass; ?>','url'=>array('update','id'=>$model-><?php echo $this->tableSchema->primaryKey; ?>)),
	array('label'=>'删除 <?php echo $this->modelClass; ?>','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model-><?php echo $this->tableSchema->primaryKey; ?>),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'管理 <?php echo $this->modelClass; ?>','url'=>array('admin')),
);


$this->beginWidget('bootstrap.widgets.TbWarp', array(
	'icon' =>'icon-eye-open',
	'title'=>'查看 <?php echo $this->modelClass; ?> #'.<?php echo "\$model->{$this->tableSchema->primaryKey}"; ?>,
));

$this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
<?php
foreach($this->tableSchema->columns as $column)
	echo "\t\t'".$column->name."',\n";
?>
	),
));

$this->endWidget();
