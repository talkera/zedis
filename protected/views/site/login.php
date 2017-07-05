<link href="<?php echo Yii::app()->request->baseUrl ?>/static/css/pages/signin.css" rel="stylesheet" type="text/css">
<style>
	.field i{    color: #ccc;
		display: block !important;
		position: absolute !important;
		z-index: 1;
		margin:2px;
		text-align: center;}
</style>

<div class="account-container">
	<div class="content clearfix">
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'login-form',
			//'enableClientValidation'=>true,
			'clientOptions'=>array(
				//'validateOnSubmit'=>true,
			),
		)); ?>
		<h1>用户登录</h1>
		<div class="login-fields">
			<?php
			if($model->getError('password'))
				echo '<p class="alert alert-danger">'.$model->getError('password').'</p>';
			else
				echo '<p>欢迎回来！</p>';
			?>

			<div class="field">
				<label for="username">用户名</label>
				<i><img src="/static/img/signin/user.png"/></i>
				<?php echo $form->textField($model,'username',['class'=>'login username-field','placeholder'=>'用户名']); ?>
			</div>

			<div class="field">
				<label for="password">密码:</label>
				<i><img src="/static/img/signin/password.png"/></i>
				<?php echo $form->passwordField($model,'password',['class'=>'login password-field','placeholder'=>'密码']); ?>
			</div>

			<div class="field">
				<label for="verifyCode">验证码:</label>
				<?php echo $form->textField($model,'verifyCode', array('class'=>'password-field', 'placeholder'=>'验证码', 'autoComplete'=>'off', 'style'=>'width:150px;float:left;')); ?>

				<img id="captcha" onclick="jQuery.ajax({url: '/site/captcha/refresh/1',dataType: 'json',cache: false,success: function(data) {jQuery('#captcha').attr('src', data['url']);}});return false;"
					 title="看不清楚？换一张" src="/site/captcha" alt="" style="line-height:54px;float:right;border:1px solid #ccc;" class="right verifyImg"/>
				<div style="clear:both;" class="clear"></div>
				<?php echo $form->error($model,'verifyCode', array('class'=>'alert alert-error','style'=>'margin-top:10px;')); ?>
			</div>

		</div>

		<div class="login-actions">
			<button type="submit" class="button btn btn-success btn-large">登录</button>
		</div>
		<?php $this->endWidget(); ?>
	</div>
</div>