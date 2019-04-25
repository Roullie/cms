<?php
	
	// Show no errors 
	//error_reporting(0);
	error_reporting(E_ALL);
	ini_set("display_errors", 1);
	
	ini_set('upload_max_size' , '64M');
	ini_set('post_max_size', '64M');
	ini_set('max_input_vars', '1000');
	ini_set('max_execution_time', '300');
	
	$APP_CONFIG = array(
		'DATABASE' 	=> array(
			'host'		=> 'localhost',
			
			//'username' 	=> 'root',
			//'password'	=> '',
			//'dbname'	=> 'libnik_cms',
			
			'link'		=> null
		)
	);
	
	define('ABBR','cms');
	define('UPLOADDIR',dirname(__FILE__).'/../uploads/');
	define('IMAGESDIR',dirname(__FILE__).'/../images/');
	define('FOLDER','/');
