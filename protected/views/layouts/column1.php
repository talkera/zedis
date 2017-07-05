<?php /*带导航模板*/ $this->beginContent('//layouts/main'); ?>
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				<a class="brand" href="javascript:;"><?php echo CHtml::encode(Yii::app()->name); ?></a>

				<div class="nav-collapse">
					<ul class="nav pull-right">
						<!-- BEGIN INBOX DROPDOWN --
						<li class="dropdown" id="header_inbox_bar">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<i class="icon-envelope"></i>
								<span class="badge">0</span>
							</a>
							<ul class="dropdown-menu extended inbox">
								<li>
									<p>您有<b>0</b>条新的消息通知</p>
								</li>
								<li>
									<a href="/>">
										<span class="subject"></span>
										<span class="message">--</span>
									</a>
								</li>
							</ul>
						</li>
						<!--END-->
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<i class="icon-user"></i>
								<?php echo Yii::app()->user->name; ?>
								<b class="caret"></b>
							</a>
							<ul class="dropdown-menu">
								<li><a onclick="jQuery.ajax({url: '/user/reloadRights',cache: false,success:function(){location.reload();}});return false;"
									   href="javascript:;">重新加载权限</a></li>
								<li><a href="/site/resetPassword">修改密码</a></li>
								<li><a href="/site/logout">退出登录</a></li>
							</ul>
						</li>
					</ul>
					<!--<form class="navbar-search pull-right"><input type="text" class="search-query" placeholder="Search"></form>-->
				</div>
			</div>
		</div>
	</div>

	<div class="subnavbar">
		<div class="subnavbar-inner">
			<div class="container">
				<?php $this->widget('zii.widgets.CMenu',array(
					'items'=>array(
						array('label'=>'<i class="icon-globe"></i><span>项目管理</span></a>', 'url'=>array('/project/admin')),
						array('label'=>'<i class="icon-user"></i><span>用户管理</span></a>', 'url'=>array('/user/admin'),
							'visible'=>Yii::app()->user->getState('isAdmin')),
						array('label'=>'<i class="icon-file"></i><span>文档</span></a>', 'url'=>array('/wiki/')),
					),
					'htmlOptions' => array('class'=> 'miannav'),
					'encodeLabel' => false,
				)); ?>
			</div>
		</div>
	</div>

	<div class="main" style="border-bottom:none;">
		<div class="main-inner">
			<div class="container">
				<div class="row">
					<?php if($this->menu) : ?>
						<div class="span3">
							<?php
							$this->beginWidget('bootstrap.widgets.TbWarp', array(
								'title'=>'操作',
								'icon' => 'icon-wrench',
							));
							$this->widget('bootstrap.widgets.TbMenu', array(
								'items'=>$this->menu,
								'htmlOptions'=>array('class'=>'operations'),
							));
							$this->endWidget();
							?>
						</div>
						<div class="span9">
							<?php echo $content; ?>
						</div>
					<?php else :?>
						<div class="span12">
							<?php echo $content; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>

<?php $this->endContent(); ?>