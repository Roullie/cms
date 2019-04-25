<?php
	header ("Content-Type:text/xml");
?>
<?php echo '<?xml version="1.0" encoding="utf-8"?>'; ?>
<?php if($App->isSitemapIndex){?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<?php foreach( $App->stmps as $map ){?>
		<sitemap>
			<loc><?=$map["loc"]?></loc>
			<lastmod><?=$map["mod"]?></lastmod>
		</sitemap>
	<?php } ?>   
</sitemapindex>
<?php }else{ ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<?php foreach( $App->stmps as $map ){?>
		<url>
			<loc><?=$map["loc"]?></loc>
			<lastmod><?=$map["mod"]?></lastmod>
		</url>
	<?php } ?>
</urlset>
<?php } ?>
