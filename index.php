<?php include 'lib/common.php';?>
<?php 
	if( $App->isCategoryPage() ){
		$view = $App->setPageView();
		$pages = $App->getPagesByCategoryName();
		if( $App->isNotCategory ){ // category pattern but doesnt exist
			redirect(FOLDER.'404.php');
		}
		$App->pageIs = 'category';
		include 'pages/category.php';
	}else if( $App->isTagPage() ){
		$App->pageIs = 'tag';
		include 'pages/tag.php';
	}else if( $App->isBlogPage() ){
		$App->pageIs = 'blog';
		include 'pages/blog.php';
	}else if( $App->isOfferPage() ){
		$App->pageIs = 'offer';
		include 'pages/offer.php';
	}else if( $App->isSitemap() ){
		$App->pageIs = 'sitemap';
		include 'pages/sitemap.php';
	}else if( $App->isPage() ){
		$App->pageIs = 'individual';
		$view = $App->setPageView();
		if( !$view->hasContent() ){
			redirect(FOLDER.'404.php');
		}
		include 'pages/page.php';
	}else{
		$App->pageIs = 'home';
		include 'pages/index.php';
	}
?>