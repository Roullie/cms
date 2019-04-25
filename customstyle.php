<?php 
	include 'lib/common.php';
	$view = $App->setPageView();
	header("Content-type: text/css");
	$view->generateStyles(1,false);
?>