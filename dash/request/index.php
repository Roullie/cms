<?php
	
	function logToTxt($str){
		$file = dirname(__FILE__) . "/debug.txt";
		if (!file_exists($file)) { 
			$fp = fopen($file, 'a');
			fclose($fp);  
		}
		$fp = fopen($file, 'a');
		fwrite($fp, $str . "\n");  
		fclose($fp);  
	}
	
	include '../../lib/common.php';
	
	if( empty($_POST) || empty($_POST['action']) ){
		die();
	}
	$json = array('status'=>0);
	
	switch( $App->Data->postVar('action') ){
		case 'getPages':
			
			
			
			$json = array(
				"draw" => intval($draw),
				"iTotalRecords" => $totalRecordwithFilter,
				"iTotalDisplayRecords" => $totalRecords,
				"aaData" => $data,
				'status' => 1
			);
			
		break;
		case 'savePage':
			$page = $App->Data->postVar('Page');
			$slink = array();
			if( isset($page['sidebar_links']) ){
				$slink = $page['sidebar_links'];
				unset($page['sidebar_links']);
			}
			if( !empty($page['id']) ){
				$App->db->UpdatePages($page);
			}else{
				$page['user_id'] = $App->user['id'];
				$page['created'] = date('Y-m-d H:i:s');
				$page['id'] = $App->db->InsertPages($page);
			}
			
			$App->db->DeleteSidebar_links(array(
				'main_page_id' => $page['id']
			));
			
			if( !empty( $slink ) ){
				foreach($slink as $link){
					$App->db->InsertSidebar_links(array(
						'main_page_id' => $page['id'],
						'linked_page_id' => $link
					));
				}
			}
			$json['status'] = 1;
			$json['Page']['id'] = $page['id'];
			
		break;
		
		case 'removePage':
			
			if( $App->Data->postVar('id') ){
				$App->db->DeletePages(array(
					'id' => $App->Data->postVar('id')
				));
				$App->db->DeleteSidebar_links(array(
					'main_page_id' => $App->Data->postVar('id')
				));
				$App->db->DeleteSidebar_links(array(
					'linked_page_id' => $App->Data->postVar('id')
				));
				/*
				$App->db->DeleteStructures(array(
					'page_id' => $App->Data->postVar('id')
				));
				$App->db->DeleteStructures(array(
					'page_id_to' => $App->Data->postVar('id')
				));*/
				// tell the cron to update the site map
				$App->db->UpdateUsers(array(
					'id' => $App->user['id'],
					'update_map' => 1
				));
				$json['status'] = 1;
			}
			
		break;
		
		case 'savePart':
			$part = $App->Data->postVar('Part');
			if( $part ){
				$App->db->UpdateParts($part);
				// tell the cron to update the site map
				$App->db->UpdateUsers(array(
					'id' => $App->user['id'],
					'update_map' => 1
				));
				$json['status'] = 1;
				$show_offer_banner = $App->Data->postVar('show_offer_banner');
				if( isset($show_offer_banner) ){
					$App->db->UpdateUsers(array(
						'id' => $App->user['id'],
						'show_offer_banner' => $App->Data->postVar('show_offer_banner')
					));
				}
				
			}
		break;
		
		case 'removeCompilation':
			
			$App->db->DeleteCompilations(array(
				'id' => $App->Data->postVar('id')
			));
			$json['status'] = 1;
			
		break;
		
		case 'saveElementStyle':
			$style = $App->Data->data['post'];
			unset($style['action']);
			if( isset($style['useImage']) ){
				$style['useImage'] = 1;
			}else{
				$style['useImage'] = 0;
			}
			
			if( !empty($_FILES['backgroundImage']) ){
				$img = $_FILES['backgroundImage'];
				$name = md5(rand(111111,999999).time()) . $App->Image->extensionFromFileType($img['type']);
				$moved = move_uploaded_file(
					$img['tmp_name'],
					UPLOADDIR . $name
				);
				if($moved){
					$style['backgroundImage'] = $name;
				}
			}
			
			$App->db->InsertStyles($style);
			
			$json['status'] = 1;
			
		break;
		
		case 'updateElementStyle':
			$style = $App->Data->data['post'];
			unset($style['action']);
			if( isset($style['useImage']) ){
				$style['useImage'] = 1;
			}else{
				$style['useImage'] = 0;
			}
			
			if( !empty($_FILES['backgroundImage']) ){
				$img = $_FILES['backgroundImage'];
				$name = md5(rand(111111,999999).time()) . $App->Image->extensionFromFileType($img['type']);
				$moved = move_uploaded_file(
					$img['tmp_name'],
					UPLOADDIR . $name
				);
				if($moved){
					$style['backgroundImage'] = $name;
				}
			}else{
				unset($style['backgroundImage']);
			}
			
			$App->db->UpdateStyles($style);
			
			$json['status'] = 1;
			
		break;
		
		case 'saveDefers':
			
			$files = $App->Data->data['post'];
			
			unset( $files['action'] );
			
			$App->db->UpdateDeferfiles( $files );
			
			$json['status'] = 1;
			
		break;
		
		case 'changeLogoImage':
			
			if( !empty($_FILES['webLogo']) ){
				$img = $_FILES['webLogo'];
				$name = "e768830dfc995988b008fab598a04689.png";
				
				if( file_exists(UPLOADDIR . $name) ){
					unlink(UPLOADDIR . $name);
				}
				
				$moved = move_uploaded_file(
					$img['tmp_name'],
					UPLOADDIR . $name
				);
				if($moved){
					$App->db->UpdateParts(array(
						'id' => 1,
						'logo' => $name
					));
					$json['status'] = 1;
				}
			}else{
				
			}
			
		break;
		
		case 'removeStyle':
			$p = $App->Data->data['post'];
			if( !empty($p['id']) ){
				$App->db->DeleteStyles(array(
					'id' => $p['id']
				));
			}
			$json['status'] = 1;
		break;
		
		case 'addIdea':
			$p = $App->Data->data['post'];
			if( $p['idea'] ){
				
				if( isset($p['idea']['id']) ){
					$maxArr = $App->db->order('arrangement desc')->SelectIdeas(array(
						'id' => $p['idea']['id']
					),true);
				}
				$arrangement = 0;
				if( !empty($maxArr) ){
					$arrangement = $maxArr + 1;
				}
				
				$idea_id = $App->db->InsertIdeas(array(
					'idea' => $p['idea'],
					'arrangement' => $arrangement
				));
				$json['status'] = 1;
				$json['idea'] =  $App->db->order('arrangement asc')->SelectIdeas(array(
					'id' => $idea_id
				),true);
			}
			
		break;
		
		case 'addCard':
			$p = $App->Data->data['post'];
			$card = $App->Data->postVar('Card');
			if(!empty($p['createNew'])){
				$json['id'] = $App->db->InsertCards(array('idea_id'=>$card['idea_id'],'topic'=>'','thought'=>''));
			}else if( !empty($card['id']) ){
				$App->db->UpdateCards($card);
			} 
			$json['status'] = 1;
			
		break;
		
		case 'saveArr':
			$arrs = $App->Data->postVar('ids');
			
			foreach( $arrs as $key => $id ){
				$App->db->UpdateCards(array(
					'id' => $id,
					'arrangement' => ($key + 1)
				));
			}
			
			$json['status'] = 1;
		break;
		
		case 'removeCard':
			
			$cid = $App->Data->postVar('cid');
			
			$App->db->DeleteCards(array('id' => $cid));
			
			$json['status'] = 1;
			
		break;
		
		case 'removeIdea':
			
			$id = $App->Data->postVar('id');
			
			$App->db->UpdateIdeas(array('id' => $id,'status'=>0));
			
			$json['status'] = 1;
			
		break;
		
		case 'revertIdea':
			
			$id = $App->Data->postVar('id');
			
			$App->db->UpdateIdeas(array('id' => $id,'status'=>1));
			
			$json['status'] = 1;
			
		break;
		
		case 'addCompilations': // this is actually update compilations
		
			$comp = $App->Data->postVar('Compilation');
			$comp['updated'] = date('Y-m-d H:i:s');
			$App->db->UpdateCompilations($comp);
			$json['status'] = 1;
		
			
		break;
		
		case 'saveArrIdeas': 
		
			$arrs = $App->Data->postVar('ids');
			
			foreach( $arrs as $key => $id ){
				$App->db->UpdateIdeas(array(
					'id' => $id,
					'arrangement' => ($key + 1)
				));
			}
			
			$json['status'] = 1;
		
			
		break;
		
		case 'saveArrCompilations': 
		
			$arrs = $App->Data->postVar('ids');
			
			foreach( $arrs as $key => $id ){
				$App->db->UpdateCompilations(array(
					'id' => $id,
					'arrangement' => ($key + 1)
				));
			}
			
			$json['status'] = 1;
		
			
		break;
		
		case 'toggleArchived': 
		
			$id = $App->Data->postVar('id');
			
			if( $id ){
				$App->db->UpdateIdeas(array(
					'id' => $id,
					'status' => $App->Data->postVar('status')
				));
			}
			$json['status'] = 1;
		
			
		break;
		
		case 'addCategory': 
		
			$cats = $App->Data->postVar('Category');
			$App->db->InsertCategories($cats);
			$json['status'] = 1;
		
			
		break;
		
		case 'editCategory': 
		
			$cat = $App->Data->postVar('Category');
			$App->db->UpdateCategories($cat);
			$json['status'] = 1;
			
		break;
		
		case 'getCategoryInfo': 
		
			$id = $App->Data->postVar('id');
			
			if( $id ){
				$json['Category'] = $App->db->SelectCategories(array(
					'id' => $id
				),true);
				$json['status'] = 1;
			}
		
			
		break;
		
		case 'removeCategory': 
		
			$cid = $App->Data->postVar('id');
			
			$App->db->DeleteCategories(array('id' => $cid));
			
			$json['status'] = 1;
		
			
		break;
		
		case 'addOffer':
			
			$errors = array();
			
			$offer = $App->Data->postVar('Offer');
			
			if( empty($_FILES) ){
				$errors[] = "Upload an image file.";
			}
			
			if( empty($offer['title']) || empty($offer['tagline']) || empty($offer['before_price']) || empty($offer['after_price']) ){
				$errors[] = "Please fill all inputs.";
			}
			
			$img = $_FILES['image'];
			$name = md5(rand(111111,999999).time()) . $App->Image->extensionFromFileType($img['type']);
			$moved = move_uploaded_file(
				$img['tmp_name'],
				UPLOADDIR . $name
			);
			if($moved){
				$offer['image'] = $name;
			}
			if( !$errors ){
				$offer['created'] = date('Y-m-d H:i:s');
				$App->db->InsertOffers($offer);
				
				$json['status'] = 1;
				
			}
			
			
		break;
		
		case 'getOfferInfo':
			
			$json['Offer'] = $App->db->SelectOffers(array(
				'id' => $App->Data->postVar('id')
			),true);
			$json['status'] = 1;
			
		break;
		
		case 'editOffer':
			
			$errors = array();
			
			$offer = $App->Data->postVar('Offer');
			
			if( !empty($_FILES) ){
				$img = $_FILES['image'];
				$name = md5(rand(111111,999999).time()) . $App->Image->extensionFromFileType($img['type']);
				$moved = move_uploaded_file(
					$img['tmp_name'],
					UPLOADDIR . $name
				);
				if($moved){
					$offer['image'] = $name;
				}
			}
			
			if( empty($offer['title']) || empty($offer['tagline']) || empty($offer['before_price']) || empty($offer['after_price']) ){
				$errors[] = "Please fill all inputs.";
			}
			
			
			if( !$errors ){
				
				$App->db->UpdateOffers($offer);
				
				$json['status'] = 1;
				
			}
			
		break;
		
		case 'removeOffer':
			
			$App->db->DeleteOffers(array(
				'id' => $App->Data->postVar('id')
			));
			$json['status'] = 1;
		break;
		
		case 'addUploads':
			
			if( !empty($_FILES) ){
				$img = $_FILES['image'];				
				$imgs = new Images();
				
				$filename = $App->Data->postVar('filename');
				$maxwidth = $App->Data->postVar('maxwidth');
				$maxwidth = $maxwidth && is_numeric($maxwidth) ? $maxwidth : 585;
				$filetype = $imgs->extensionFromFileType($img['type']);
				$json['img'] = $img;
				$json['filename'] = $filename;
				$json['filetype'] = $filetype;
				if( $filename && $filetype ){
					$file = $filename . $filetype;
					if( !file_exists(IMAGESDIR . $file) ){
						$moved = move_uploaded_file(
							$img['tmp_name'],
							IMAGESDIR . $file
						);						
						if($moved){
							// check width of image
							$size = getimagesize(IMAGESDIR.$file);
							if($size[0] > $maxwidth){ // width is greater than 585
								$imgs->set_img(IMAGESDIR.$file);
								$imgs->set_size($maxwidth);
								$imgs->save_img(IMAGESDIR.$file);
							}
							//unlink(IMAGESDIR . $file);
							$offer['image'] = $file;							
							
							$App->db->InsertUploads(array(
								'name' => $file
							));
							$json['status'] = 1;
						}else{
							$json['msg'] = "Unable to upload file.";
						}
					}else{
						$json['msg'] = "The Filename already exists.";
					}
				}else{
					if( !$filename ){
						$json['msg'] = "Filename is needed.";
					}else{
						$json['msg'] = "Please upload an image file..";
					}
				}
				
				
			}else{
				$json['msg'] = "Please upload an image file.";
			}
			
			
		break;
		
		case 'removeUploads':
			
			$id = $App->Data->postVar('id');
			
			$file = $App->db->SelectUploads(array(
				'id' => $id
			),true);
			
			if( $file ){
				if( file_exists( UPLOADDIR . $file['name'] ) ){				
					unlink( UPLOADDIR . $file['name']);
				}
				if( file_exists( IMAGESDIR . $file['name'] ) ){
					unlink( IMAGESDIR . $file['name']);
				}
				$App->db->DeleteUploads(array('id'=>$id));
				$json['status'] = 1;
			}
			
		break;
		
		case 'saveSection':
			
			$section = $App->Data->postVar('Section');
			if( !empty($section['id']) ){
				$App->db->UpdateOffer_sections($section);
				// tell the cron to update the site map
				$App->db->UpdateUsers(array(
					'id' => $App->user['id'],
					'update_map' => 1
				));
				$json['status'] = 1;
			}
			
		break;
		
		case 'getLinksOnPage':
			
			$url = $App->Data->postVar('url');
			
			$hm = parse_url($App->home);
			$savelinks = array();
			$thePageIwantTogetAllLinks = $url['href'];
			$ret = get( $thePageIwantTogetAllLinks );
			
			if( strrpos($url['href'],$hm['host'].$hm['path']) === false ){
				
			}else{
				
				$gatheredlinks = $App->getLinksFromHTML( $ret['html'] , $thePageIwantTogetAllLinks );			
				
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
				
				if( $App->Data->postVar('index') == 0 ){
					$App->db->Truncate('map');
				}
				
				foreach( $savelinks as $to_url ) {		
						
					$App->db->InsertMap(array(
						'from_url' => str_replace(array("http://","https://"),array("",""),$thePageIwantTogetAllLinks),
						'to_url' => str_replace(array("http://","https://"),array("",""),$to_url['href']),
						'text' => $to_url['text'],
						'rel' => $to_url['rel'],
					));
					
				}
				
			}
			
			$json['links'] = $savelinks;
			$json['title'] = $App->getTitleFromHTML($ret['html']);
			$json['isSameDomain'] = strrpos($url['href'],$hm['host'].$hm['path']);
			$json['h'] = parse_url($App->home);
			$json['hr'] = parse_url($url['href']);
			$json['status'] = 1;
			
		break;
		
		case 'show_5_offer_hompage':
			
			$App->db->UpdateUsers(array(
				'id' => $App->user['id'],
				'show_5_offer_hompage' => $App->Data->postVar('show_5_offer_hompage')
			));
			
		break;
		
		case 'show_5_qna_hompage':
			
			$App->db->UpdateUsers(array(
				'id' => $App->user['id'],
				'show_5_qna_hompage' => $App->Data->postVar('show_5_qna_hompage')
			));
			
		break;
		
		case 'show_homepage_bottom':
			
			$App->db->UpdateUsers(array(
				'id' => $App->user['id'],
				'show_homepage_bottom' => $App->Data->postVar('show_homepage_bottom')
			));
			
		break;
			
		case 'show_homepage_banner':
			
			$App->db->UpdateUsers(array(
				'id' => $App->user['id'],
				'show_homepage_banner' => $App->Data->postVar('show_homepage_banner')
			));
			
		break;
		
		case 'totallyDeleteIdea':
			
			$id = $App->Data->postVar('id');
			
			if( !empty($id) ){
				$App->db->DeleteIdeas(array(
					'id' => $id
				));
				$json['status'] = 1;
			}
			
		break;
		
		case 'saveExcludedUrls':
			
			$App->db->UpdateUsers(array(
				'id' => $App->user['id'],
				'exclude_sitemaps' => $App->Data->postVar('exclude_sitemaps')
			));
			$json['status'] = 1;
		break;
	}
	
	die(json_encode($json));
	
	