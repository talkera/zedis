<?php
/**
 * The following variables are available in this template:
 * - $this: the BootCrudCode object
 */
?>
<?php
echo "<?php\n";
$label=$this->pluralize($this->class2name($this->modelClass));
echo "\$this->breadcrumbs=array(
	'$label'=>array('index'),
	'Create',
);\n";
?>

$this->pageTitle = '管理后台-<?php echo $this->modelClass; ?>创建';

$this->menu=array(
	array('label'=>'管理 <?php echo $this->modelClass; ?>','url'=>array('admin')),
);

$this->beginWidget('bootstrap.widgets.TbWarp', array(
	'icon' =>'icon-file',
	'title'=>'创建 <?php echo $this->modelClass; ?>',
));

<?php echo "echo \$this->renderPartial('_form', array('model'=>\$model));"; ?>

$this->endWidget();