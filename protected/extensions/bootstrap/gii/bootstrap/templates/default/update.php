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
	\$model->{$nameColumn}=>array('view','id'=>\$model->{$this->tableSchema->primaryKey}),
	'Update',
);\n";
?>

$this->pageTitle = '管理后台-<?php echo $this->modelClass; ?>更新 #'.<?php echo "\$model->{$this->tableSchema->primaryKey}"; ?>;

$this->menu=array(
	array('label'=>'创建 <?php echo $this->modelClass; ?>','url'=>array('create')),
	array('label'=>'查看 <?php echo $this->modelClass; ?>','url'=>array('view','id'=>$model-><?php echo $this->tableSchema->primaryKey; ?>)),
	array('label'=>'管理 <?php echo $this->modelClass; ?>','url'=>array('admin')),
);

$this->beginWidget('bootstrap.widgets.TbWarp', array(
	'icon' =>'icon-edit',
	'title'=>'更新 <?php echo $this->modelClass?> '.<?php echo "\$model->{$this->tableSchema->primaryKey}"; ?>,
));

<?php echo "echo \$this->renderPartial('_form',array('model'=>\$model));"; ?>


$this->endWidget();