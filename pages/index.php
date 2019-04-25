<?php 
	$view = $App->setPageView();
	$pages = $App->getPagesByHome();
	$pgntion 	= $App->db->pgntion;
	$offers = $App->db->limit(5)->SelectOffers();
	$sections = $App->db->SelectOffer_sections();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
		<?php include 'page_header.php';?>
	</head>
	<body class="homepage <?=$App->user['show_homepage_bottom']?"showBottom":""?>">
		<div id="wrapper">
			<?php $view->echoPart('layout','navigation');?>
			<div id="header">
				<header>
					<?php $view->echoPart('layout','header');?>
					<?php if($App->user['show_homepage_banner']){?>
						<?php $view->echoPart('layout','banner');?>
					<?php } ?>
				</header>
			</div>
			<!-- #header -->

			<div id="content">
				<main>
					<?php if($App->user['show_5_offer_hompage']){?>
					<div class=" offer-boxes">
						<div class="container-fluid">
						<?php foreach($offers as $key => $offer){?>
							<div class="<?=(( $key + 1 ) % 6 == 0 || $key == 0) ? "col-md-offset-1" : ""?> col-md-2 col-xs-6 oi-containers">
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
							<?php if( ( $key + 1 ) % 5 == 0){?>								
								<div class="clearfix"></div>
							<?php } ?>		
						<?php } ?>		
						
						<div class="clearfix"></div>
						</div>
					</div>
					<?php } ?>					
					<div class="container site-pages">
						<?php foreach( $pages as $key => $page  ){?>
							<div class="col-md-4 page-c <?=($key%7==0)?"mod-7":""?> <?=($key==7)?"pull-right":""?>">
								<h3 class=""><a href="<?=FOLDER?><?=$page['url']?>"><?=$page['title']?></a></h3>
								<p class=""><?=
									implode(" ",array_map(function($t){return '<a class="tag" rel="nofollow" href="'.FOLDER."tag/".trim($t).'">'.$t.'</a>';},array_filter(explode(",",$page['tags']),function($t){return trim($t);})) );
								?></p>
								<div class=""><a class="unstyled" href="<?=FOLDER?><?=$page['url']?>"><?=nl2br($page['tagline'])?></a></div>
								<a class="continue-reading" href="<?=FOLDER?><?=$page['url']?>" rel="nofollow">Read More</a>
							</div>
						<?php }?>
						<div class="clearfix"></div>
						<?php $pageLinks = (generatePagination($pgntion['page'], $pgntion['totalPages']));?>
						<div class="text-center">
							<?php if($pageLinks){?>
							<ul class="pagination">
								<?php if( $pgntion['page'] > 1 ){?>
									<li class="" >
										<a class="pagination-link" href="<?= FOLDER .( (($pgntion['page']-1) <= 1) ? "" : "page/".($pgntion['page']-1) )?>" data-page="">&laquo;</a>
									</li>
								<?php } ?>
								<?php foreach( $pageLinks as $plink ){?>
									<li class="<?=$plink['page']==$pgntion['page']?"active":""?>">
										<?php
											$link = "javascript:void(0);";
											if( $plink['page'] != '...' ){
												if($plink['page'] <= 1){
													$link = FOLDER;
												}else{
													$link = FOLDER."page/".$plink['page'];
												}
											}
										?>
										<a class="pagination-link" href="<?=$link?>" data-page=""><?=$plink['page']?></a>
									</li>
								<?php } ?>
								<?php if( $pgntion['page'] < $pgntion['totalPages'] ){?>
									<li class="" >
										<a class="pagination-link" href="<?=FOLDER?>page/<?=($pgntion['page']+1)?>" data-page="">&raquo;</a>
									</li>
								<?php } ?>
							</ul>
							<?php } ?>
						</div>
						
					</div>
					<?php if($App->user['show_5_qna_hompage']){?>
						<?php $view->echoPart('layout','homepage_qna');?>
					<?php } ?>
					<?php if($App->user['show_homepage_bottom']){?>
						<?php $view->echoPart('layout','homepage_bottom');?>
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