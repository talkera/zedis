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
	'Manage',
);\n";
?>

$this->pageTitle = '管理后台-<?php echo $this->modelClass; ?>列表页';

$this->menu=array(
	array('label'=>'创建 <?php echo $this->modelClass; ?>','url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('<?php echo $this->class2id($this->modelClass); ?>-grid', {
		data: $(this).serialize()
	});
	return false;
});
");


$this->beginWidget('bootstrap.widgets.TbWarp', array(
	'icon' =>'icon-list-alt',
	'title'=>'管理 <?php echo $this->pluralize($this->class2name($this->modelClass)); ?>',
));

<?php echo "echo CHtml::link('高级搜索','#',array('class'=>'search-button btn'));"; ?>
?>

<div class="search-form" style="display:none">
<?php echo "<?php \$this->renderPartial('_search',array(
	'model'=>\$model,
)); ?>\n"; ?>
</div><!-- search-form -->

<?php echo "<?php"; ?> $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'<?php echo $this->class2id($this->modelClass); ?>-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
<?php
$count=0;
$maxCount = 7;
foreach($this->tableSchema->columns as $column)
{
	if(++$count==$maxCount)
		echo "\t\t/*\n";
	echo "\t\t'".$column->name."',\n";
}
if($count>=$maxCount)
	echo "\t\t*/\n";
?>
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
		),
	),
));

$this->endWidget();