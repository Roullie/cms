<?php
	
	include '../lib/common.php';
	
	$db = new DB();
	
	$thePageIwantTogetAllLinks = "http://libnik.com/cms/c/fishing";
	
	$ret = get( $thePageIwantTogetAllLinks );
	
	$gatheredlinks = $App->getLinksFromHTML( $ret['html'] , $thePageIwantTogetAllLinks );
	
	$savelinks = array();
	
	foreach( $gatheredlinks as $link ){
		
		$link = array_map_recursive("trim",$link);
		
		$href = strtok($link['href'], "#");
		$href = strtok($href, "?");
		
		if( $href != $thePageIwantTogetAllLinks && trim($link['text']) ){
			
			// also check if all items are in the array
			$isIn = array_filter($savelinks,function($l) use ($link){
				
				return $l['text'] == $link['text'] && $l['href'] == $link['href'] && $l['rel'] == $link['rel'];
				
			});
			
			if( !$isIn ){
				$savelinks[] = $link;
			}
			
		}
		
	}
	pr($savelinks);
	
?>