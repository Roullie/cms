<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
		<?php include 'page_header.php';?>
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
						<?php 
							if( !$view->hasContent() ){ 
								echo $view->show404();
							}else{ ?>
								<div class="row">
									<div class="col-md-8">
										<div class="content">
											<?php $view->echoPart('content','content');?>
										</div>
									</div>
									<div class=" col-md-4">
										<?php $view->echoPart('content','custom_sidebar');?>
										<?php if($view->hasSideLinks()){?>
										<div class="sidebar">
											<div class="right-menu"><?php $view->echoPart('content','page_links');?></div>										
										</div>
										<?php } ?>
									</div>	
									<div class="clearfix"></div>
								</div>
						<?php } ?>
					</div>
					<?php if( $view->hasBottom() ){ ?>
						<?php $view->echoPart('content','custom_bottom');?>
					<?php }else{ ?>
						<?php $view->echoPart('layout','bottom');?>
					<?php } ?>
				</main>
			</div>
			<!-- #content -->

			<div id="footer">
				<footer><?php $view->echoPart('layout','footer');?></footer>
			</div>
			<!-- #footer -->

		</div>
		
		<?php include 'page_scripts.php';?>
	</body>
</html>