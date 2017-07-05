<?php
/**
 * The following variables are available in this template:
 * - $this: the BootCrudCode object
 */

echo "<?php
\$form=\$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'action'=>Yii::app()->createUrl(\$this->route),
	'method'=>'get',
));\n";

foreach($this->tableSchema->columns as $column){
	$field=$this->generateInputField($this->modelClass,$column);
	if(strpos($field,'password')!==false)
		continue;

	echo "\techo ".$this->generateActiveRow($this->modelClass,$column).";\n\n";
}

echo <<<EOF
	echo '<div class="form-actions">';

		\$this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>'Search',
		));

	echo '</div>';

\$this->endWidget();
EOF;
