<?php
$form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'project-form',
	'enableAjaxValidation'=>false,
));

echo '<p class="help-block">带 <span class="required">*</span> 为必填项.</p>';

echo $form->errorSummary($model);

echo $form->textFieldRow($model,'name',array('class'=>'span5','maxlength'=>30));
echo $form->textFieldRow($model,'markPath',array('class'=>'span5','maxlength'=>100));

echo $form->textFieldRow($model,'pRepo',array('class'=>'span5','maxlength'=>100));
echo $form->dropDownListRow($model, 'pType', Project::$vcConf);
echo $form->textFieldRow($model,'pUser',array('class'=>'span5','maxlength'=>30));
echo $form->textFieldRow($model,'pPwd',array('class'=>'span5','maxlength'=>30));

echo $form->textFieldRow($model,'dRepo',array('class'=>'span5','maxlength'=>100));
echo $form->dropDownListRow($model, 'dType', Project::$vcConf);
echo $form->textFieldRow($model,'dUser',array('class'=>'span5','maxlength'=>30));
echo $form->textFieldRow($model,'dPwd',array('class'=>'span5','maxlength'=>30));

echo $form->textAreaRow($model,'betaServers',array('rows'=>6, 'cols'=>50, 'class'=>'span5'));
echo $form->textAreaRow($model,'prodServers',array('rows'=>6, 'cols'=>50, 'class'=>'span5'));

echo '<div class="form-actions">';

	$this->widget('bootstrap.widgets.TbButton', array(
		'buttonType'=>'submit',
		'type'=>'primary',
		'label'=>$model->isNewRecord ? '创建' : '更新',
	));

echo '</div>';

$this->endWidget();