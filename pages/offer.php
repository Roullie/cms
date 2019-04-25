<?php 
	$view = $App->setPageView();
	$pages = $App->getPagesByHome();
	$pgntion 	= $App->db->pgntion;
	$offers = $App->db->SelectOffers();
	$sections = $App->db->SelectOffer_sections(array('show_section'=>1));
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
		<?php include 'page_header.php';?>
		<style></style>
	</head>
	<body class="offer">
	
		<div id="wrapper">
			<?php $view->echoPart('layout','navigation');?>
			<div id="header">
				<header>
					<?php $view->echoPart('layout','header');?>
					<?php if($App->user['show_offer_banner']){?>
						<?php $view->echoPart('layout','offer_banner');?>
					<?php } ?>
					
				</header>	
			</div>
			<!-- #header -->

			<div id="content">
				<main>
					<div class="offer-boxes">
						<div class="container-fluid">				
							<?php foreach($offers as $offer){?>
								<div class="col-md-2 col-xs-6 oi-containers">
									<a class="oi-link" data-container="body" data-toggle="popover" data-placement="auto right" data-html="true" 
										data-content="<?=createOfferPopUp($offer,false);?>" data-url="<?=$offer['url']?>">
										<div class="offer-item">
											<div class="oi-image" style="background-image:url(<?=FOLDER?>uploads/<?=$offer['image']?>);"></div>
											<div class="offer-item-info">
												<div class="oi-title"><h3><?=$offer['title']?></h3></div>
												<div class="oi-tagline"><?=nl2br($offer['tagline'])?></div>
												<div class="oi-prices">
													<span class="oi-bp"><?=$offer['before_price']?></span>
													<span class="oi-ap"><?=$offer['after_price']?></span>
												</div>
											</div>
										</div>
										<div class="oi-info-hover"></div>
									</a>
								</div>
							<?php } ?>				
						</div>
					</div>
					<!-- sections -->
					<?php foreach($sections as $key => $section){?>
						<?=$section['html'];?>
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