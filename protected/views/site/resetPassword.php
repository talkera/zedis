<link href="<?php echo Yii::app()->request->baseUrl ?>/static/css/pages/signin.css" rel="stylesheet" type="text/css">

<div class="account-container register">
	<div class="content clearfix">
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'login-form',
			'enableClientValidation'=>true,
			'clientOptions'=>array(
				'validateOnSubmit'=>true,
			),
		)); ?>
			<h1>重置密码</h1>
			<div class="login-fields">
				<?php
				if( $error != '' )
					echo '<p class="alert alert-danger">'.$error.'</p>';
				?>

				<div class="field">
					<label for="username">原密码</label>
					<input type="password" id="password" name="User[password]" value="" placeholder="旧密码" class="login">
				</div>

				<div class="field">
					<label for="password">新密码</label>
					<input type="password" id="password" name="User[newPassword]" value="" placeholder="新密码" class="login">
				</div>

				<div class="field">
					<label for="password">密码确认</label>
					<input type="password" id="password" name="User[newPasswordRepeat]" value="" placeholder="新密码确认" class="login">
				</div>
			</div>

			<div class="login-actions">
				<button class="button btn btn-primary btn-large">重置</button>
			</div>
		<?php $this->endWidget(); ?>
	</div>
</div>