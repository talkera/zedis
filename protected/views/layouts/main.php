<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<link rel="" href="/favicon.ico" type="image/x-icon" />
	<link href="<?php echo Yii::app()->request->baseUrl ?>/static/css/bootstrap.min.css" rel="stylesheet">
	<link href="<?php echo Yii::app()->request->baseUrl ?>/static/css/bootstrap-responsive.min.css" rel="stylesheet">
	<link href="<?php echo Yii::app()->request->baseUrl ?>/static/css/font-awesome.css" rel="stylesheet">
	<link href="<?php echo Yii::app()->request->baseUrl ?>/static/css/style.css" rel="stylesheet">

	<?php Yii::app()->clientScript->registerCoreScript('bootstrap'); /*自动加载 jquery*/	?>

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>
<?php echo $content; ?>
</body>
</html>
