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
		<div style="padding-bottom:20px;">
			<a class="btn btn-primary" href="<?php echo $this->createUrl('product',array('pId'=>$project['id'])); ?>">返回部署列表</a>
			<a class="btn" href="<?php echo $this->createUrl('changed',array('pId'=>$project['id'])); ?>">返回变更列表</a>
		</div>

		<table class="table table-striped" style="text-align:center">
			<tr>
				<th>文件名</th>
				<th>版本号</th>
				<th>代码提交人</th>
				<th>变更提交人</th>
				<th>变更提交时间</th>
				<th>查看变化</th>
			</tr>
			<?php foreach($data as $k=>$item): ?>
			<tr>
				<td><?php echo $item['file'];?></td>
				<td><?php echo $item['version'];?></td>
				<td><?php echo $item['author'];?></td>
				<td><?php echo $record['author'];?></td>
				<td><?php echo $item['cTime'];?></td>
				<td><a href="/diff/index?<?php echo http_build_query(array(
						'pId'=>$item['pId'],'v'=>$item['version'],'id'=>$item['id'],'f'=>$item['file'],
					));?>" target="_blank" title="查看变化">Diff</a></td>
			</tr>
			<?php endforeach; ?>
		</table>
	</div>
</div>