<?php
$this->breadcrumbs=array(
	'Project Svns'=>array('index'),
	$model->name,
);

$this->pageTitle = '查看项目 #'.$model->id;

$isAdmin = Yii::app()->user->getState('isAdmin');
$this->menu=array(
	array('label'=>'创建项目','url'=>array('create'),'visible'=>$isAdmin),
	array('label'=>'更新项目','url'=>array('update','id'=>$model->id),'visible'=>$isAdmin),
	array('label'=>'删除项目','url'=>'#',
		'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?'),
		'visible'=>$isAdmin,
	),
	array('label'=>'项目列表','url'=>array('admin')),
	array('label'=>'工作目录初始化','url'=>'#', 'linkOptions'=>array('id'=>'doInit'), 'visible'=>$model->status==1),
	array('label'=>'重置Dev目录','url'=>'#',
		'linkOptions'=>array('submit'=>array('reset','pId'=>$model->id, 'type'=>'dev'), 'confirm'=>'确定重置dev目录吗？')),
	array('label'=>'重置beta目录','url'=>'#',
		'linkOptions'=>array('submit'=>array('reset','pId'=>$model->id, 'type'=>'beta'), 'confirm'=>'确定重置beta目录吗？')),
	array('label'=>'重置Product目录','url'=>'#',
		'linkOptions'=>array('submit'=>array('reset','pId'=>$model->id, 'type'=>'prod'), 'confirm'=>'确定重置prod目录吗？')),
);


$this->beginWidget('bootstrap.widgets.TbWarp', array(
	'icon' =>'icon-eye-open',
	'title'=>'查看项目 #'.$model->id,
));

if($model->status == 1) echo <<<EOF
<div class="alert alert-danger">
	警告：项目尚未初始化工作目录，初始化后才可用。
</div>
EOF;
if($resetInfo = Yii::app()->user->getFlash('resetInfo')) echo <<<EOF
<div class="alert alert-info" id="resetInfo">{$resetInfo}</div>
EOF;
echo <<<EOF
<div class="alert alert-info" id="infoWrap" style="display:none;"></div>
EOF;

$this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'markPath',
		'pRepo',
		'pUser',
		'pPwd',
		'dRepo',
		'dUser',
		'dPwd',
		'betaServers',
		'prodServers',
	),
));

$this->endWidget();
?>
<script>
	var doing = false;
	var pId = <?php echo $model['id'];?>;
	$(function(){
		$('#doInit').click(function(){
			if(doing) return false;
			$('#infoWrap').show().html('初始化中...');
			$.ajax({
				url:'/project/init/pId/'+pId,
				success:function(data){
					doing = 0;
					if(typeof data != 'object'){
						alert('未知错误...');
						$('#infoWrap').hide();
						return;
					}
					if(data.status!=0){
						alert('初始化失败：'+data.msg);
						if(data.data.log) $('#infoWrap').html('<pre>'+data.data.log+'</pre>').show();
					}else{
						alert('初始化成功!');
						location.reload();
					}
				},error:function(){
					doing = 0;alert('未知错误...');
				}
			});
		});
	});
</script>