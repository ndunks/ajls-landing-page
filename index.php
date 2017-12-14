<?php

include 'boot.php';

$page	= @$_REQUEST['page'] ?: 'index';
$file	= 'pages/' . $page . '.json';
if(!is_file($file)){
	include 'notfound.php';
	exit;
}
$str	= file_get_contents($file);

if(empty($str)) die('No Content');

extract(json_decode($str, true));

?><!DOCTYPE html>
<html>
<head>
	<title><?= $title ?></title>
	<meta name="description" content="<?= $description ?>">
	<meta name="keywords" content="<?= $keywords ?>">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.2/css/materialize.min.css">
 	<link href="css/style.css" rel="stylesheet">
 	<script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
 	<style type="text/css">
 		.fixed {
 			position: fixed;
		    height: 100%;
		    width: 100%;
		    top: 0px;
		    left: 0px;
 		}
	 	#outter{
	 		background-color: #<?= @$page_background_color ?>;
 			background-image: <?= @$page_background_image ? "url($page_background_image)" : '' ?>;
 			background-size: <?= @$page_background_fit ? '100% 100%' : 'auto' ?>;
 			background-repeat: no-repeat;
 			padding: 30px 0px;
	 	}
 		#content{
 			background-color: #<?= @$content_background_color ?>;
 			background-image: <?= @$content_background_image ? "url($content_background_image)" : '' ?>;
 			background-size: <?= @$content_background_fit ? '100% 100%' : 'auto' ?>;
 			background-repeat: no-repeat;
 			color: #<?= $content_color ?>;
 		}
 	</style>
</head>
<body>
<div id="outter" class="<?= @$content_central ? 'fixed' : '' ?>">
	<div class="row <?= @$content_central ? 'center-middle' : '' ?>">
		<div id="content" class="col <?= $content_size ?>">
			<?= $content ?>
		</div>
	</div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.2/js/materialize.min.js"></script>
</body>
</html>