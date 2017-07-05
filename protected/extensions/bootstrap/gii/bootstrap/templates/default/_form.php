<?php
/**
 * The following variables are available in this template:
 * - $this: the BootCrudCode object
 */

echo <<<EOF
<?php
\$form=\$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'{$this->class2id($this->modelClass)}-form',
	'enableAjaxValidation'=>false,
));

	echo '<p class="help-block">带 <span class="required">*</span> 为必填项.</p>';

	echo \$form->errorSummary(\$model);\n\n
EOF;




foreach($this->tableSchema->columns as $column)
{
	if($column->autoIncrement)
		continue;

	echo "\techo ".$this->generateActiveRow($this->modelClass,$column).";\n\n";
}

echo <<<EOF
	echo '<div class="form-actions">';

		\$this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>\$model->isNewRecord ? '创建' : '更新',
		));

	echo '</div>';

\$this->endWidget();
EOF;
