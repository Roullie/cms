<?php
	
	include dirname(__FILE__).'/../lib/common.php';
	
	$db = new DB();
	
	$savelinks = array();	
	
	$isProcessing = $App->get_process("cms/cron/index.php");
	// if cron is still running die..	
	if( $isProcessing && count($isProcessing) > 1 ){
		$pid = $isProcessing[0];	
		die();
	}
	// 1 means th user might changed something in the html of the pages
	//if($App->user['update_map']==1){
				
		$savelinks[] = array(
			'href' => $App->home,
			'text' => "Home",
			'links' => array()
		);
		
		$ctr = 0;

		$hm = parse_url($App->home);
		
		while( $ctr < count($savelinks) ){
			
			$thePageIwantTogetAllLinks = $savelinks[$ctr]['href'];
		
			$ret = get( $thePageIwantTogetAllLinks );
		
			$gatheredlinks = $App->getLinksFromHTML( $ret['html'] , $thePageIwantTogetAllLinks );
			
			foreach( $gatheredlinks as $link ){
				
				$link = array_map_recursive("trim",$link);
				
				$href = strtok($link['href'], "#");
				$href = strtok($href, "?");
				
				if( strrpos($link['href'],$hm['host'].$hm['path']) === false ){
					continue;
				}
				
				// remove duplicate links within the page...
				if( $href != $thePageIwantTogetAllLinks && trim($link['text']) ){					
					// also check if all items are in the array
					$isIn = array_filter($savelinks[$ctr]['links'],function($l) use ($link){					
						return $l['text'] == $link['text'] && $l['href'] == $link['href'] && $l['rel'] == $link['rel'];					
					});					
					if( !$isIn ){						
						$savelinks[$ctr]['links'][] = $link;
					}					
				}
				//pr($link);
				// remove duplicate links within the page... parent
				if( $href != $thePageIwantTogetAllLinks && trim($link['text']) && $link['rel'] != "nofollow" ){					
					// also check if all items are in the array
					$isIn = array_filter($savelinks,function($l) use ($link){
						return $l['text'] == $link['text'] && $l['href'] == $link['href'];					
					});					
					if( !$isIn ){												
						$savelinks[] = array(
							'links' => array(),
							'text' => $link['text'],
							'href' => $link['href']
						);
					}					
				}
				
			}
			echo $ctr ." => ". count($savelinks) . "\n";
			$ctr++;
			sleep(1);
			
		}
		// clear all items in the map
		$App->db->Truncate('map');
		foreach( $savelinks as $from_url ) {		
			
			foreach( $from_url['links'] as $to_url ){
				
				$map_id = $App->db->InsertMap(array(
					'from_url' => str_replace(array("http://","https://"),array("",""),$from_url['href']),
					'to_url' => str_replace(array("http://","https://"),array("",""),$to_url['href']),
					'text' => $to_url['text'],
					'rel' => $to_url['rel'],
				));
				
				if( $map_id ){
					echo "Inserted \n";
				}
				
			}
			
		}
		//pr($savelinks);
		
	//}
	
	
?>