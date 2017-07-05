<br/>
<?php
/*MsgOut 页面*/

$this->pageTitle=Yii::app()->name . ' - 提示';

$styles = [
	'notice'	=> "alert alert-block",
	'info'		=> "alert alert-info alert-block",
	'success'	=> "alert alert-success alert-block",
	'error'		=> "alert alert alert-error alert-block",
];

echo <<<EOF
<div class="{$styles[$type]}">{$msg}</div>
EOF;

$btnStyle = [
	'white'=>'btn-invert',
	'blue'=>'btn-primary',
	'lightBlue'=>'btn-info',
	'green'=>'btn-success',
	'yellow'=>'btn-warning',
	'red'=>'btn-danger',
];

if($targets) foreach($targets as $item){
	if(empty($item['url'])||empty($item['label'])) continue;
	$style = !isset($btnStyle[$item['type']]) ? '' : $btnStyle[$item['type']];
	echo "<a class='btn {$style}' href='{$item['url']}'>{$item['label']}</a> ";
}