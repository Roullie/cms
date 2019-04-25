<?php
	
	include '../../lib/common.php';
	
	if( empty($_POST) || empty($_POST['action']) ){
		die();
	}
	$json = array('status'=>0);
	
	pr($App->Data->postVar('Page'));
	pr($_POST);
	
	die();
	
	