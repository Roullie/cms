<?php
	
	include dirname(__FILE__) . '/../lib/common.php';
	
	$db = new DB();
	
	$proxyurl = "http://libnik.com/ipproxygatherer/api.php?type=quora";
	
	$lists = $db->SelectUrllists();
	
	foreach( $lists as $list ){
		
		$response = get($proxyurl);
		$proxies = json_decode($response['html'],true);
		$proxies = array_map(function($p){
			return $p['proxy'];
		},$proxies);
		
		$topic_urls = explode("\n",$list['topics']);
		
		if( $topic_urls ){
			$Quora = new Quora($topic_urls ,$proxies , false );
			$infos = $Quora->getTopicQuestions()->getInfo();
			pr($infos);
			foreach( $infos as $info ){
				foreach( $info['questions'] as $question ){
					$questionExist = $db->SelectQuestions(array(
						'question' => $question['question']
					),true);
					if( !$questionExist ){
						//$question['created'] = date("Y-m-d H:i:s");
						//$db->InsertQuestions($question);
						pr("Insert");
					}
				}
			}
			
		}
		sleep(1);
	}
	
	$questions = $db->SelectQuestions();
	
	
	
	
?>