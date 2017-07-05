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
?>
<style>
	.text-center{text-align:center;}
	form{display:none;}
	form.active{display:block;}
</style>
<div class="widget">
	<div class="widget-header">
		<h3>权限管理</h3>
	</div>

	<div class="widget-content form-horizontal">
		<div class="control-group">
			<label class="control-label">分配方式</label>
			<div class="controls">
				<button class="btn <?php if(!$isAll) echo 'btn-success' ?>" id="btnDIY" type="button">自定义分配</button>
				<button class="btn <?php if($isAll) echo 'btn-success' ?>" id="btnAll" type="button">分配所有</button>
			</div>
		</div>

		<form id="DIYWarp" method="post" class="control-group <?php if(!$isAll) echo 'active';  ?>">
			<table class="table table-striped">
				<tr>
					<th>项目名称</th>
					<th>操作</th>
				</tr>
				<?php foreach($projectList as $p): ?>
					<tr>
						<td><?php echo $p['name'];?></td>
						<td>
							<label class="checkbox inline">
								<input name="project[<?php echo $p['id'];?>][sub]" value="1" type="checkbox"
									<?php if(isset($rights[$p['id']]['sub'])) echo 'checked'; ?>> 提交变更
							</label>
							<label class="checkbox inline">
								<input name="project[<?php echo $p['id'];?>][beta]" value="1" type="checkbox"
									<?php if(isset($rights[$p['id']]['beta'])) echo 'checked'; ?>> 部署仿真机
							</label>
							<label class="checkbox inline">
								<input name="project[<?php echo $p['id'];?>][prod]" value="1" type="checkbox"
									<?php if(isset($rights[$p['id']]['prod'])) echo 'checked'; ?>> 部署产品机
							</label>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<div class="text-center">
				<button class="btn btn-primary" onclick="return confirm('确定提交吗？')">确认</button>
			</div>
		</form>

		<!--分配所有-->
		<form id="allWrap" method="post" class="<?php if( $isAll ) echo 'active';?>" >
			<div class="alert">*当前及以后新增项目都将分配给该用户</div>
			<input name="all" value="-1" type="hidden">
			<div class="text-center">
				<button class="btn btn-primary" onclick="return confirm('确定提交吗？')">确认</button>
			</div>
		</form>
	</div>
</div>

<script type="text/javascript">
	$('#btnDIY').click(function(){
		$('button.btn-success').removeClass('btn-success');
		$(this).addClass('btn-success');
		$('#DIYWarp').show();
		$('#allWrap').hide();
	});
	$('#btnAll').click(function(){
		$('button.btn-success').removeClass('btn-success');
		$(this).addClass('btn-success');
		$('#allWrap').show();
		$('#DIYWarp').hide();
	});
</script>


