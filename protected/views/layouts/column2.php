<?php /*不带导航模板*/ $this->beginContent('//layouts/main'); ?>
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				<a class="brand" href="javascript:;"><?php echo CHtml::encode(Yii::app()->name); ?></a>
			</div>
		</div>
	</div>

<?php echo $content; ?>

<?php $this->endContent(); ?>