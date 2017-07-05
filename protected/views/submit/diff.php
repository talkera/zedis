<style>
	pre{font-family:'Courier New' , Monospace; font-size:12px; width:99%; overflow:auto; margin:0 0 1em 0; background:#F7F7F7;box-sizing:border-box;white-space:pre;}
	pre ol, pre ol li, pre ol li span{margin:0 0; padding:0; border:none}
	pre a, pre a:hover{background:none; border:none; padding:0; margin:0}
	pre ol{ background:#F7F7F7; margin:0px 0px 1px 4em !important; padding:5px 0; color:#5C5C5C; border-left:3px solid #146B00;line-height: 5px;}
	pre ol li{list-style:decimal-leading-zero; list-style-position:outside !important; color:#5C5C5C; padding:0 3px 0 10px !important; margin:0 !important; line-height:15px;}
	pre ol li.alt{color:inherit}
	pre ol li span{color:black; background-color:inherit}
	pre .comment, pre .comments{color:#008200; background-color:inherit}
	pre .string{color:blue; background-color:inherit}
	pre .keyword{color:#069; font-weight:bold; background-color:inherit}
	pre .preprocessor{color:gray; background-color:inherit}
	pre .dp-xml .cdata{color:#ff1493}
	pre .dp-xml .tag, pre .dp-xml .tag-name{color:#069; font-weight:bold}
	pre .dp-xml .attribute{color:red}
	pre .dp-xml .attribute-value{color:blue}
	pre .dp-sql .func{color:#ff1493}
	pre .dp-sql .op{color:#808080}
	pre .dp-rb .symbol{color:#a70}
	pre .dp-rb .variable{color:#a70; font-weight:bold}
	pre .dp-py .builtins{color:#ff1493}
	pre .dp-py .magicmethods{color:#808080}
	pre .dp-py .exceptions{color:brown}
	pre .dp-py .types{color:brown; font-style:italic}
	pre .dp-py .commonlibs{color:#8A2BE2; font-style:italic}
	pre .dp-j .annotation{color:#646464}
	pre .dp-j .number{color:#C00000}
	pre .dp-delphi .number{color:blue}
	pre .dp-delphi .directive{color:#008284}
	pre .dp-delphi .vars{color:#000}
	pre .dp-css .value{color:black}
	pre .dp-css .important{color:red}
	pre .dp-c .vars{color:#d00}
	pre .dp-cpp .datatypes{color:#2E8B57; font-weight:bold}
	pre p.emptyLine{margin:0;height:15px;}

	.list {margin-left: 0px; width: 50%; float: left;}
	.delete {background: none repeat scroll 0 0 #FFC864; text-decoration: line-through;}
	.add {background: none repeat scroll 0 0 #FFFF00;}
</style>

<?php
if($error){
	echo "<div class='alert alert-danger'>对比失败，请查看日志:<br>{$error}</div>";
	return;
}

$trans = get_html_translation_table(HTML_SPECIALCHARS);
$type = Yii::app()->request->getParam('type', 'diff');
$orgStr = $newStr = '';
if($type == 'all'){
	$orgArray = $newArray = $fileArray;
	for($i=count($diff)-1; $i >= 0; $i--){
		$value = $diff[$i];
		$begin = $value['new']['begin'] - 1; //fileContent取自变更后的文件
		$count = $value['new']['count'];
		array_splice($orgArray, $begin, $count, $value['org']['code']);
		array_splice($newArray, $begin, $count, $value['new']['code']);
	}
	$inLi = 0;
	foreach($orgArray as $k => $item){
		if(null===$item){ $orgStr.= '<p class="emptyLine"></p>';continue; }

		$class = '';
		if(!empty($item[0])){
			if($item[0]=='-') $class = 'delete';
			elseif($item[0]=='+') $class = 'add';
			if($class) $item = substr($item, 1);
			$item = strtr($item, $trans);
		}
		if($inLi) $orgStr.= '</li>';
		$orgStr .= "<li class='{$class}'>{$item}";
		$inLi = 1;
	}
	$orgStr = "<ol>{$orgStr}</li></ol>\n";

	$inLi = 0;
	foreach($newArray as $k => $item){
		if(null===$item){ $newStr.= '<p class="emptyLine"></p>';continue; }

		$class = '';
		if(!empty($item[0])){
			if($item[0]=='-') $class = 'delete';
			elseif($item[0]=='+') $class = 'add';
			if($class) $item = substr($item, 1);
			$item = strtr($item, $trans);
		}
		if($inLi) $newStr.= '</li>';
		$newStr .= "<li class='{$class}'>{$item}";
		$inLi = 1;
	}
	$newStr = "<ol>{$newStr}</li></ol>\n";
}else{
	foreach($diff as $k => $block){
		$orgStr.= "<ol start='{$block['org']['begin']}'>";
		$inLi = 0;
		foreach($block['org']['code'] as $ln => $item){
			if(null===$item){ $orgStr.= '<p class="emptyLine"></p>';continue; }

			$class = '';
			if(!empty($item[0])){
				if($item[0]=='-') $class = 'delete';
				elseif($item[0]=='+') $class = 'add';
				if($class) $item = substr($item, 1);
				$item = strtr($item, $trans);
			}
			if($inLi) $orgStr.= '</li>';
			$orgStr .= "<li class='{$class}'>{$item}";
			$inLi = 1;
		}
		$orgStr.= "</li></ol>\n";
	}
	foreach($diff as $k => $block){
		$newStr .= "<ol start='{$block['new']['begin']}'>";
		$inLi = 0;
		foreach($block['new']['code'] as $ln => $item){
			if(!$item){ $newStr .= '<p class="emptyLine"></p>';continue; }

			$class = '';
			if(!empty($item[0])){
				if($item[0]=='-') $class = 'delete';
				elseif($item[0]=='+') $class = 'add';
				if($class) $item = substr($item, 1);
				$item = strtr($item, $trans);
			}
			if($inLi) $newStr .= '</li>';
			$newStr .= "<li class='{$class}'>{$item}";
			$inLi = 1;
		}
		$newStr .= "</li></ol>\n";
	}
}

$param = Tools::arrayExtract($_GET, array('pId','f','v','id'));
if($type=='all'){
	$param['type'] = 'diff';
	$label = '仅显示差异';
}else{
	$param['type'] = 'all';
	$label = '显示全部';
}
$param = http_build_query($param);

if($cmd) echo "<div class='alert'>{$cmd}</div>";
echo <<<EOF
<div style="padding-bottom:10px"><a class="btn btn-large btn-info" href="?{$param}">{$label}</a></div>
<div class="list"><pre id="left" onscroll="right.scrollLeft = this.scrollLeft;">{$orgStr}</pre></div>
<div class="list"><pre id="right" onscroll="left.scrollLeft = this.scrollLeft;">{$newStr}</pre></div>
EOF;
