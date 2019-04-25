<?php 
	include 'lib/common.php';
	$App->pageIs = "404";
	$view = $App->setPageView();
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
		<?php include 'pages/page_header.php';?>
	</head>
	<body class="individual <?=$view->hasBottom()?"hasCustomBottom":"hasBottom"?>">
		<div id="wrapper">
			<?php $view->echoPart('layout','navigation');?>
			<div id="header">
				<header><?php $view->echoPart('layout','header');?></header>
			</div>
			<!-- #header -->
			<div id="content">
				<main>
					<div class="container-fluid">
						<?=$view->show404();?>
					</div>
				</main>
			</div>
			<!-- #content -->
			<div id="footer">
				<footer><?php $view->echoPart('layout','footer');?></footer>
			</div>
			<!-- #footer -->
		</div>		
		<?php include 'pages/page_scripts.php';?>
	</body>
</html>