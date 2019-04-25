<?php 
	$view = $App->setPageView();
	$pages = $App->getPagesByTags();
	$pgntion 	= $App->db->pgntion;
	
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head><?php include 'page_header.php';?></head>
	<body class="tags">
		<div id="wrapper">
			<?php $view->echoPart('layout','navigation');?>
			<div id="header">
				<header><?php $view->echoPart('layout','header');?></header>
			</div>
			<!-- #header -->

			<div id="content">
				<main>
					<div class="container">
						<?php foreach( $pages as $key => $page  ){?>
							<div class="col-md-4 page-c <?=($key%7==0)?"mod-7":""?> <?=($key==7)?"pull-right":""?>">
								<h3 class=""><a href="<?=FOLDER?><?=$page['url']?>"><?=$page['title']?></a></h3>
								<p class=""><?=
									implode(" ",array_map(function($t){return '<a class="tag" rel="nofollow" href="'.FOLDER."tag/".trim($t).'">'.$t.'</a>';},array_filter(explode(",",$page['tags']),function($t){return trim($t);})) );
								?></p>
								<div class=""><a class="unstyled" href="<?=FOLDER?><?=$page['url']?>"><?=$page['tagline']?></a></div>
								<a class="continue-reading" rel="nofollow" href="<?=FOLDER?><?=$page['url']?>">Read More</a>
							</div>
						<?php }?>
						<div class="clearfix"></div>
						<?php $pageLinks = (generatePagination($pgntion['page'], $pgntion['totalPages']));?>
						<div class="text-center">
							<?php if($pageLinks){?>
							<ul class="pagination">
								<?php if( $pgntion['page'] > 1 ){?>
									<li class="" >
										<a class="pagination-link" href="<?=FOLDER?>c/<?=$App->category;?>/page/<?=($pgntion['page']-1)?>" data-page="">&laquo;</a>
									</li>
								<?php } ?>
								<?php foreach( $pageLinks as $plink ){?>
									<li class="<?=$plink['page']==$pgntion['page']?"active":""?>">
										<?php
											$link = "javascript:void(0);";
											if( $plink['page'] != '...' ){
												$link = FOLDER."c/".$App->category."/page/".$plink['page'];
											}
										?>
										<a class="pagination-link" href="<?=$link?>" data-page=""><?=$plink['page']?></a>
									</li>
								<?php } ?>
								<?php if( $pgntion['page'] < $pgntion['totalPages'] ){?>
									<li class="" >
										<a class="pagination-link" href="<?=FOLDER?>c/<?=$App->category;?>/page/<?=($pgntion['page']+1)?>" data-page="">&raquo;</a>
									</li>
								<?php } ?>
							</ul>
							<?php } ?>
						</div>
						
					</div>
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