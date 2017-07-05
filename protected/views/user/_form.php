<?php
$form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'user-form',
	'enableAjaxValidation'=>false,
));

echo '<p class="help-block">带 <span class="required">*</span> 为必填项.</p>';

echo $form->errorSummary($model);
echo $form->dropDownListRow($model,'type', User::$typeConf);
echo $form->textFieldRow($model,'name');
echo $form->textFieldRow($model,'svnName');
echo $form->dropDownListRow($model,'status', User::$statusConf);
echo $form->textFieldRow($model,'pwd');
echo '<div class="control-group" style="color:#999;margin-top:-18px;">
	<label class="control-label required" for="User_pwd"></label>
	<div class="controls">更新时不填密码，表示不更新用户密码</div>
</div>';

echo '<div class="form-actions">';

	$this->widget('bootstrap.widgets.TbButton', array(
		'buttonType'=>'submit',
		'type'=>'primary',
		'label'=>$model->isNewRecord ? '创建' : '更新',
	));

echo '</div>';

$this->endWidget();