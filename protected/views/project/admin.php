<?php
$this->breadcrumbs=array(
	'Project Svns'=>array('index'),
	'Manage',
);

$this->pageTitle = '项目列表';

if(Yii::app()->user->getState('isAdmin')) echo <<<EOF
<a class="btn btn-info" href="/project/create" style="margin-bottom:20px;">+创建项目</a>
EOF;


$rights = Yii::app()->user->getState('pRights');
$pIds = trim(Yii::app()->user->getState('projects'),', ');
if($pIds){
	$where = (-1 == $pIds) ? '': "where id in ({$pIds})";
	$projects = Yii::app()->db->createCommand("select * from project {$where} order by status desc, id desc")->queryAll();
}else{
	$projects = array();
}
?>
<div class="widget">
	<div class="widget-header">
		<i class="icon-list-alt"></i><h3 class="">项目列表</h3>
	</div>
	<div class="widget-content">
		<table class="items table">
			<thead>
			<tr>
				<th>ID</th>
				<th>项目名称</th>
				<th>路径标识</th>
				<th>状态</th>
				<th>操作</th>
			</tr>
			</thead>
			<tbody>
			<?php foreach($projects as $item): ?>
				<tr>
					<td><?php echo $item['id'];?></td>
					<td><?php echo $item['name'];?></td>
					<td><?php echo $item['markPath'];?></td>
					<td><?php echo Project::$statusConf[$item['status']];?></td>
					<td class="button-column">
						<a href="/project/view/id/<?php echo $item['id'];?>"><i class="icon-eye-open"></i>查看</a>
						<a href="/project/update/id/<?php echo $item['id'];?>"><i class="icon-edit"></i>编辑</a>
						<a href="/project/log/pId/<?php echo $item['id'];?>"><i class="icon-tasks">日志</i></a>
						<?php if($pIds==-1 || !empty($rights[$item['id']]['sub'])) :?>
							<br><a href="/submit/changed/pId/<?php echo $item['id'];?>"><i class="icon-random">提交变更</i></a>
						<?php endif; ?>
						<?php if($pIds==-1 || !empty($rights[$item['id']]['beta'])|| !empty($rights[$item['id']]['prod'])) :?>
							<br><a href="/submit/product/pId/<?php echo $item['id'];?>"><i class="icon-legal">部署代码</i></a>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>