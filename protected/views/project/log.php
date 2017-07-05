<style>
	h2{margin-bottom:10px}
	pre{background-color:#000;color:#fff;height:500px;overflow:auto;}
	div.alert{margin-top:10px;}
</style>
<?php
$logName = array(
	'tmp' => '临时日志',
	'CL'=>'检查变更日志',
	'C2P'=>'提交变更日志',
	'BS'=>'部署到仿真机日志',
	'PS'=>'部署到产品机日志',
);

$logConf = Log::logConf('p');
$logType = Yii::app()->request->getParam('logType', '');

$this->menu=array();
foreach($logName as $k=>$title){
	if($k == $logType) continue;
	$this->menu[] = array('label'=>$title, 'url'=>array('log','logType'=>$k,'pId'=>$project['id']));
}

//如果没有指定日志，则显示白页
if(empty($logConf[$logType])){
	$this->pageTitle = '查看项目日志';
	return;
}
$this->pageTitle = '查看项目日志 - '.$logName[$logType];

echo '<h2>'.$this->pageTitle.'</h2>';

//获取读多少行
$l = Yii::app()->request->getParam('l', 1);
$lConf = array(1=>200, 2=>500, 3=>1000);
if(empty($lConf[$l])) $l = 1;
foreach($lConf as $k=>$v){
	echo " <a class='btn' href='?l={$k}'>{$v}行</a> ";
}

$logPath = Log::getPath($logType, $project['markPath']);//日志路径
$cmd = "tail -{$lConf[$l]} {$logPath}";
echo '<div class="alert">'.$cmd.'</div>';
exec($cmd, $results, $status);

$trans = get_html_translation_table(HTML_SPECIALCHARS);
echo '<pre>';
foreach($results as $item) echo strtr($item, $trans)."\n";
echo '</pre>';