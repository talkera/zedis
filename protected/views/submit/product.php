<style>
	.table th, .table td{text-align:center;}
	.status{color:blue;font-weight:bold;}
	.status.M{color:#ff6600;}
	.status.A{color:#660099;}
	.status.D{color:red;}
	tr.checked td{background-color:#fcf8e3!important;}
	i.running{color:#F30;}
</style>
<div class="widget">
	<div class="widget-header">
		<h3 class="">当前产品:<?php echo $project['name'];?></h3>
		<strong class="right"></strong>
	</div>
	<div class="widget-content">
		<?php if(Yii::app()->user->hasFlash('changed')):?>
			<div class="alert alert-block">
				<?php echo Yii::app()->user->getFlash('changed');?>
			</div>
		<?php endif;?>
		<?php if($error):?><div class="alert alert-danger"><?php echo $error;?></div><?php endif;?>
		<div class="alert alert-block" id="logWrap" style="display:none;"></div>
		<div style="padding-bottom:20px;">
			<a class="btn btn-primary" href="<?php echo $this->createUrl('changed',array('pId'=>$project['id'])); ?>">返回变更列表</a>
			<a class="btn btn-danger right" href="<?php echo $this->createUrl('distribute',array('pId'=>$project['id'])); ?>">部署当前仿真机版本到线上</a>
		</div>

		<table class="table table-striped" style="text-align:center">
			<tr>
				<th>ID</th>
				<th>版本号</th>
				<th>描述</th>
				<th>变更提交人</th>
				<th>提交时间</th>
				<th>仿真机版本(全量)</th>
				<th>仿真机版本(增量)</th>
				<th>产品机版本(全量)</th>
				<th>产品机版本(增量)</th>
			</tr>
			<?php foreach($data as $k=>$item): ?>
			<tr>
				<td><?php echo $item['id'];?></td>
				<td><a href="<?php echo $this->createUrl('detail',array('id'=>$item['id']));?>"
					   target="_blank" title="查看详细"><?php echo $item['version'];?></a></td>
				<td><?php echo $item['comment'];?></td>
				<td><?php echo $item['author'];?></td>
				<td><?php echo $item['cTime'];?></td>
				<td>
					<?php if($item['isBeta']): ?>
						<a href="<?php echo "/submit/updateBeta/pId/{$project['id']}/submitId/{$item['id']}/version/{$item['version']}"?>"
						   title="重新部署此版本"><i class="icon-ok running"></i></a>
					<?php else: ?>
						<a href="<?php echo "/submit/updateBeta/pId/{$project['id']}/submitId/{$item['id']}/version/{$item['version']}"?>"
						   title="点击使用此版本"><i class="icon-play stop"></i></a>
					<?php endif; ?>
				</td>
				<td>
					<?php if($item['isPartBeta']): ?>
						<a href="<?php echo "/submit/updateBeta/pId/{$project['id']}/submitId/{$item['id']}/version/{$item['version']}/type/part"?>"
						   title="重新部署此版本"><i class="icon-ok running"></i></a>
					<?php else: ?>
						<a href="<?php echo "/submit/updateBeta/pId/{$project['id']}/submitId/{$item['id']}/version/{$item['version']}/type/part"?>"
						   title="点击使用此版本"><i class="icon-play stop"></i></a>
					<?php endif; ?>
				</td>
				<td>
					<?php if($item['isOnline']): ?>
						<i class="icon-ok running"></i>
					<?php endif; ?>
				</td>
				<td>
					<?php if($item['isPartOnline']): ?>
						<i class="icon-ok running"></i>
					<?php endif; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</table>

		<?php echo Tools::pages( $totalCount, $cPage, array('pageSize'=>$pSize, 'params'=>array('pId'=>$project['id'])) ); ?>
	</div>
</div>