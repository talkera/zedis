<style>
	.main{border:none;padding-top:60px;}
</style>
<div class="main">
	<div class="main-inner">
		<div class="container">
			<div class="row">
				<div class="widget">
					<?php if($error):?>
						<div class="alert alert-danger">
							<?php echo $error;?>
						</div>
					<?php endif;?>

					<?php if($installed):?>
						<div class="alert alert-success">
							<h3>已安装！</h3><br>
							<h4>目录</h4>
							<p>运行目录：<?php echo RUNTIME_PATH; ?><br>系统运行生成的一些中间文件存放处，需有写权限！</p>
							<p>工作目录：<?php echo WORK_PATH; ?><br>
								系统的操作都将在该目录下进行，需有写权限！<br>
								另外请保留该目录下的 install.lock
							</p>

							<h4>数据库</h4>
							<p>Host：<?php echo DB_HOST;?></p>
							<p>Port：<?php echo DB_PORT;?></p>
							<p>Name：<?php echo DB_NAME;?></p>
							<p>User：<?php echo DB_USER;?></p>
							<p>Pwd ：<?php echo DB_PWD;?></p>

							<h4>其他</h4>
							<p>Svn bin文件 ：<?php echo SVN_BIN;?></p>
							<p>rsync bin文件 ：<?php echo RSYNC_BIN;?></p>
							<hr/>
							<a class="btn" href="/">去使用</a>
						</div>
					<?php else: ?>
						<div class="alert alert-info">
							<h3>即将安装！</h3><br>
							<h4>目录</h4>
							<p>运行目录：<?php echo RUNTIME_PATH; ?><br>系统运行生成的一些中间文件存放处，需有写权限！</p>
							<p>工作目录：<?php echo WORK_PATH; ?><br>系统的操作都将在该目录下进行，需有写权限！</p>

							<h4>数据库</h4>
							<p>
								Host：<?php echo DB_HOST;?><br>
								Port：<?php echo DB_PORT;?><br>
								Name：<?php echo DB_NAME;?><br>
								User：<?php echo DB_USER;?><br>
								Password：<?php echo DB_PWD;?>
							</p>

							<h4>其他</h4>
							<p>Svn bin文件 ：<?php echo SVN_BIN;?></p>
							<p>rsync bin文件 ：<?php echo RSYNC_BIN;?></p>
							<hr/>
							<p>如有问题，请修改 <?php echo ROOT_PATH.'/config.php'; ?></p>
							<form method="post" id="install">
								<button type="button" onclick="return doConfirm()"
										class="btn btn-large btn-success btn-support-ask">确认安装</button>
							</form>
						</div>
					<?php endif;?>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	function doConfirm(){
		if(!confirm('确定安装到此目录吗？')) return;
		$('#install').submit();
	}
</script>
