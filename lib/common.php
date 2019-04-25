<?php
	session_start();
	session_save_path(dirname(__FILE__));
	ini_set('session.gc_probability', 1);
	date_default_timezone_set('Europe/Amsterdam');
	// include libraries for the Application
	include dirname(__FILE__).'/config.php';
	include dirname(__FILE__).'/DB.php';	
	include dirname(__FILE__).'/Encrypt.php';
	include dirname(__FILE__).'/Images.php';
	
	function pr($obj=null){
		echo "<pre>";	
		print_r($obj);
		echo "</pre>";	
	}
	
	function array_map_recursive($callback, $array){
		$func = function ($item) use (&$func, &$callback) {
			return is_array($item) ? array_map($func, $item) : call_user_func($callback, $item);
		};		
		return array_map($func, $array);
	}
	
	// check cookies and saved to $_SESSION['MPLOGGED'] if there is
	
	function getUserIp(){
		if( array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
			if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')>0) {
				$addr = explode(",",$_SERVER['HTTP_X_FORWARDED_FOR']);
				return trim($addr[0]);
			} else {
				return $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
		}
		else {
			return $_SERVER['REMOTE_ADDR'];
		}
	}
	
	function getUserAgent(){
		return $_SERVER['HTTP_USER_AGENT'];
	}
	
	function s_datediff( $str_interval, $dt_menor, $dt_maior, $relative=false){

       if( is_string( $dt_menor)) $dt_menor = date_create( $dt_menor);
       if( is_string( $dt_maior)) $dt_maior = date_create( $dt_maior);

       $diff = date_diff( $dt_menor, $dt_maior, ! $relative);
       
       switch( $str_interval){
           case "y": 
               $total = $diff->y + $diff->m / 12 + $diff->d / 365.25; break;
           case "m":
               $total= $diff->y * 12 + $diff->m + $diff->d/30 + $diff->h / 24;
               break;
           case "d":
               $total = $diff->y * 365.25 + $diff->m * 30 + $diff->d + $diff->h/24 + $diff->i / 60;
               break;
           case "h": 
               $total = ($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h + $diff->i/60;
               break;
           case "i": 
               $total = (($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h) * 60 + $diff->i + $diff->s/60;
               break;
           case "s": 
               $total = ((($diff->y * 365.25 + $diff->m * 30 + $diff->d) * 24 + $diff->h) * 60 + $diff->i)*60 + $diff->s;
               break;
          }
       if( $diff->invert)
               return -1 * $total;
       else    return $total;
   }
	
   //To remove all the hidden text not displayed on a webpage
	function strip_html_tags($str){
		$str = preg_replace('/\&\#8203\;/uis',' ',$str);
		$str = preg_replace('/\&\#\d+\;/ius',' ',$str);
		$str = preg_replace('/(<|>)\1{2}/uis', ' ', $str);
		$str = preg_replace('/\&nbsp\;/uis', ' ', $str);
		$str = preg_replace(
			array(// Remove invisible content
				'@<head[^>]*?>.*?</head>@siu',
				'@<style[^>]*?>.*?</style>@siu',
				'@<script[^>]*?.*?</script>@siu',
				'@<noscript[^>]*?.*?</noscript>@siu',
				'@<br[^>]@siu',
				),
			"", //replace above with nothing
			$str );
		$str = replaceWhitespace($str);
		$str = strip_tags($str);
		$str = preg_replace('/[\x00-\x1F\x7F-\xFF]/', ' ', $str);
		return trim(preg_replace('/\s\s+/', ' ', $str));
	} //function strip_html_tags ENDS

	//To replace all types of whitespace with a single space
	function replaceWhitespace($str) {
		$result = $str;
		foreach (array(
		"  ", " \t",  " \r",  " \n",
		"\t\t", "\t ", "\t\r", "\t\n",
		"\r\r", "\r ", "\r\t", "\r\n",
		"\n\n", "\n ", "\n\t", "\n\r",
		) as $replacement) {
		$result = str_replace($replacement, $replacement[0], $result);
		}
		return $str !== $result ? replaceWhitespace($result) : $result;
	}
	
	function minimizeString( $string , $max = 200 ){
		if( mb_strlen( $string ) <= $max ){
			return mb_substr( $string , 0 , $max );
		}
		return mb_substr( $string , 0 , $max ) . "...";
	}
	
	function get( $url = NULL ){
			
		// create headers
		//$this->createHeaders(1);
		
		$options = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER => false,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_ENCODING => "",
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_TIMEOUT => 10,
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_SSL_VERIFYPEER => false,
			CURL_HTTP_VERSION_1_1 => 1,
			CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4
		);
	
		// start curl
	
		$ch = curl_init ("");				
		curl_setopt($ch, CURLOPT_URL, $url);			
		curl_setopt_array( $ch , $options );
		
		$data = curl_exec($ch);			
		$status = curl_getinfo($ch);
		
		curl_close($ch);
		
		return array(
			'html' => $data,
			'status' => $status
		);
		
	}
		
	if( !empty($_COOKIE[ABBR.'LOGGED']) ){
		$enc=new Encryption();
		if( empty($_SESSION[ABBR.'LOGGED']) ){
			$db = new DB();
			$user_id = $enc->decode($_COOKIE[ABBR.'LOGGED']);
			$_SESSION[ABBR.'LOGGED'] = $db->SelectUsers(array('id'=>$user_id),true);
		}
	}
	
	if( isset($_GET['log']) && $_GET['log']=='out' ){
		
		setcookie(
			ABBR.'LOGGED',
			"",
			(time() - (365*24*60*60)),
			"/"
		);
		unset($_SESSION[ABBR.'LOGGED'],$_COOKIE[ABBR.'LOGGED']);
		header("Location: index.php");
		
	}
	
	function isLogged(){
		return !empty($_SESSION[ABBR.'LOGGED']) ? $_SESSION[ABBR.'LOGGED'] : array();
	}
	
	function redirect($url){
		header("Location: {$url}");
		die();
	}
	
	function random_strings( $length = 15 ){
		return substr( md5( rand(0,time()) ) , 0 , $length);
	}
	
	function generatePagination($currentPage, $totalPages, $add_query_string = "" ,$pageLinks = 5){
		if ($totalPages <= 1)
		{
			return NULL;
		}

		$links = array();

		$leeway = floor($pageLinks / 2);

		$firstPage = $currentPage - $leeway;
		$lastPage = $currentPage + $leeway;

		if ($firstPage < 1)
		{
			$lastPage += 1 - $firstPage;
			$firstPage = 1;
		}
		if ($lastPage > $totalPages)
		{
			$firstPage -= $lastPage - $totalPages;
			$lastPage = $totalPages;
		}
		if ($firstPage < 1)
		{
			$firstPage = 1;		}
		
		
		if ($firstPage != 1)
		{
			$links[] = array(
				'page' => 1,
				'link' => true
			);
			$links[] = array(
				'page' => "...",
				'link' => false
			);
		}

		for ($i = $firstPage; $i <= $lastPage; $i++)
		{
			if ($i == $currentPage)
			{
				$links[] = array(
					'page' => $i,
					'current' => true,
					'link' => false
				);
			}
			else
			{
				$links[] = array(
					'page' => $i,
					'current' => false,
					'link' => true
				);
			}
		}

		if ($lastPage != $totalPages)
		{
			$links[] = array(
				'page' => "...",
				'current' => false,
				'link' => false
			);
			$links[] = array(
				'page' => $totalPages,
				'current' => false,
				'link' => true
			);			
		}

		return $links;
	}
	
	function createOfferPopUp( $data ,$isOfferPage = true){
		$ul = "";
		return "<div class='popData'>" 
				."<div class='l-updated'>Last updated: " .date('m/Y',strtotime($data['updated'])) . "</div>"
				."<div class='l-title'>" .$data['title'] . "</div>"
				."<div class='l-topic_in'>" . ( !empty($data['topic_in']) ? $data['topic_in'] : "" ). "</div>"
				."<div class='l-time'>" . ( !empty($data['time']) ? "<i class='fa fa-clock-o'></i>&nbsp;".$data['time'] : "" ). "</div>"
				."<div class='l-hover_tagline'>" .$data['hover_tagline'] . "</div>"
				."<ul class='l-bullet'>" 
					. ( !empty($data['bullet1']) ? "<li class='l-bullet-item'>" .$data['bullet1'] . "</li>" : "" )
					. ( !empty($data['bullet2']) ? "<li class='l-bullet-item'>" .$data['bullet2'] . "</li>" : "" )
					. ( !empty($data['bullet3']) ? "<li class='l-bullet-item'>" .$data['bullet3'] . "</li>" : "" )
				. "</ul>"
				."<div class='l-button-play'>".(!$isOfferPage?"<a class='offer-play-button' data-url='".$data['url']."'><img class='' src='http://libnik.com/cms/uploads/7903293fb039f83a2adfe425ab7b83bd.jpg' /></a>":"<a class='pop-button-play btn-red-cts'>Play Video</a>")."</div>"
			.'</div>';
	}
	
	$enc=new Encryption();
	
	class Request{
		
		public $data = array(
			'get' => array(),
			'post' => array(),
		);
		
		public function __construct(){
			$this->data['get'] = array_map_recursive('trim',$_GET);
			$this->data['post'] = array_map_recursive('trim',$_POST);
		}
		
		public function postVar( $key = '' ){
			return isset( $this->data['post'][$key] ) ? $this->data['post'][$key] : '';
		}
		
		public function getVar( $key = '' ){
			return isset( $this->data['get'][$key] ) ? $this->data['get'][$key] : '';
		}
		
	}
	
	class Image{
		public function extensionFromFileType( $type = "" ){
			
			switch($type){
				case 'image/jpeg':
				case 'image/jpg':
					return ".jpg";
				break;
				case 'image/png':
					return ".png";
				break;
				default:
					return "";
				break;
			}
			
		}
	}
	
	class Page{
		
		private $info = array(
			'id' => '',
			'title' => '',
			'url' => '',
			'content' => '',
			'tags' => '',
			'tagline' => '',
			'category_id' => '',
			'custom_sidebar' => '',
			'custom_bottom' => '',
			'sidebar_links' => array(),
			'show_home' => 0,
			'meta_title' => '',
			'meta_keywords' => '',
			'meta_description' => '',
			'sidebar_links_data' => array(),
		);
		
		public function __construct( $App ){
			$this->App = $App;
		}
		
		public function setPageId( $id ){
			$this->id = $id;
			$this->setInfo(array('id'=>$id));
			return $this;
		}
		
		public function setPageUrl( $url ){
			$this->setInfo(array('url'=>$url));
			return $this;
		}
		
		private function setInfo( $params = array() ){
			if( !empty($this->App->user['id']) ){
				$paramsa['user_id'] = $this->App->user['id'];
				$info = $this->App->db->SelectPages($params,true);
				if( $info ){
					$this->id = $info['id'];
					$this->info = $info;
					$sidebar_links = $this->App->db
						->columns(array(
							'sidebar_links.linked_page_id',
							'pages.title',
							'pages.url',
						))
						->joins(array(
							'table' => 'pages',
							'on' => 'pages.id = sidebar_links.linked_page_id'
						))
						->SelectSidebar_links(array(
						'main_page_id' => $this->id
					));
					$this->info['sidebar_links'] = array();
					$this->info['sidebar_links_data'] = $sidebar_links;
					foreach($sidebar_links as $links){
						$this->info['sidebar_links'][] = $links['linked_page_id'];
					}
					
				}
			}
		}
		
		public function getInfo(){
			return $this->info;
		}
		
		public function other( $id = 0 ){
			
			if( $this->id && empty($id) ){
				$id = $this->id;
			}
			
			return $this->db->SelectPages(array(
				'id !=' => $id,
				'user_id' => $this->App->user['id']
			));
		}
		
	}
	
	class PageView{
		
		public function __construct( $Page , $Part ){
			$this->Page = $Page;
			$this->Part = $Part;
			$this->info['content'] = $Page->getInfo();
			$this->info['layout'] = $Part->getInfo();
			$this->info['content']['page_links'] = !empty( $this->info['content']['sidebar_links_data'] ) ? $this->generatePageLinks($this->info['content']['sidebar_links_data']) : "";
			$this->info['Category'] = array();
		}
		
		public function hasSideLinks(){
			return !empty( $this->info['content']['sidebar_links_data'] );
		}
		
		public function echoPart( $part , $key ){
			if( isset( $this->info[$part][$key] ) ){
				
				echo $this->iterateImagesToLazy($this->info[$part][$key]);
				
			}
		}
		
		public function hasContent(){
			return !empty( $this->info['content']['id'] );
		}
		
		public function hasBottom(){
			return !empty( $this->info['content']['custom_bottom'] );
		}
		
		public function show404(){
			return '<h1 style="text-align:center;">Page Not Found</h1>';
		}
		
		private function generatePageLinks($links){
			$ul = '<ul class="list-group right-menu-list"><li class="active list-group-item">Page Links</li>';
			foreach( $links as $link ){
				$ul .= '<li class="list-group-item"><a href="'.$link['url'].'"><i class="fa fa-file-text-o" aria-hidden="true"></i><span>'.$link['title'].'</span></a></li>';
			}
			$ul .= '</ul>';
			return $ul;
		}
		
		public function hasMetaTitle(){
			return !empty($this->info['content']['meta_title']);
		}
		
		public function metaDescriptions(){
			echo !empty($this->info['content']['meta_description']) ? '<meta name="description" content="'.$this->info['content']['meta_description'].'">' : ""; 
		}
		
		public function metaKeywords(){
			echo !empty($this->info['content']['meta_keywords']) ? '<meta name="keywords" content="'.$this->info['content']['meta_keywords'].'">' : "";			
		}
		
		public function pageTitle(){
			if($this->hasMetaTitle()){
				return $this->info['content']['meta_title'];
			}
			if( !empty($this->info['content']['title']) ){
				return $this->info['content']['title'];
			}
		}
		
		public function generateStyles( $type = 1 , $tagged = true ){
			$styles = $this->Page->db->columns(array(
				'target',
				'font',
				'fontColor',
				'fontSize',
				'backgroundColor',
				'backgroundImage',
				'useImage'
			))->SelectStyles(array(
				'type' => $type
			));
			$pagestyle = "";
			
			foreach( $styles as $style ){
				$pagestyle .= $style['target'] . " {";
				if( $style['font'] ){
					$pagestyle .= "font-family: '{$style['font']}' , sans-serif;";
				}
				if( $style['fontColor'] ){
					$pagestyle .= "color: {$style['fontColor']};";
				}
				if( $style['fontSize'] ){
					$pagestyle .= "font-size: {$style['fontSize']};";
				}
				if( $style['useImage'] ){
					$pagestyle .= "background-image: url(" . FOLDER . "uploads/" . $style['backgroundImage'] . ");";
				}
				if( !empty($style['backgroundColor']) ){
					$pagestyle .= "background-color: {$style['backgroundColor']};";
				}
				$pagestyle .= "}";
			}
			
			$pagestyle .= "";
			if( $tagged ){
				echo "<style>{$pagestyle}</style>";
			}else{
				echo $pagestyle;
			}
		}
		function DOMinnerHTML(DOMNode $element){ 
			$innerHTML = ""; 
			$children  = $element->childNodes;

			foreach ($children as $child) 
			{ 
				$innerHTML .= $element->ownerDocument->saveHTML($child);
			}

			return $innerHTML; 
		}
		public function iterateImagesToLazy( $content ){
	
			return preg_replace_callback("/<img[^>]+\>/i", function($img){
				if( $img ){	
					$dom = new DOMDocument();
					@$dom->loadHTML( "<p>{$img[0]}</p>" );
					$i = $dom->getElementsByTagName('img')->item(0);
					$classToAdd = 'img-lazy';
					$isLazy = false;
					if( $i->hasAttribute('class') ){
						$classToAdd .= ' '.$i->getAttribute('class');
						if( strpos($classToAdd,$i->getAttribute('class')) >= 0 ){
							$isLazy = true;
						}
					}
					if( !$isLazy ){
						$i->setAttribute('class' , $i->getAttribute('class') . ' img-lazy');
						$i->setAttribute('data-src' , $i->getAttribute('src') );
						$i->setAttribute('src' , '' );
					}
					return $i->C14N();
					return $dom->saveHTML();
					return $this->DOMinnerHTML($i);
				}
			}, $content);
		}
		
	}
	
	class Part{
		
		private $info = array(
			'id' => '',
			'header' => '',
			'footer' => '',
			'banner' => '',
			'offer_banner' => '',
			'homepage_qna' => '',
			'bottom' => '',
			'files' => array(
				'script' => array(),
				'style' => array(),
			)
		);
		
		public function __construct( $App ){
			$this->App = $App;
		}
		
		public function set(){
			if( !empty($this->App->user['id']) ){
				$this->info = $this->App->db->SelectParts(array(
					'user_id' => $this->App->user['id']
				),true);
				$files = $this->App->db->columns(array('script','style'))->SelectDeferfiles(array(
					'user_id' => $this->App->user['id']
				),true);
				$this->info['files']['script'] = array_filter(
					array_map('trim',explode("\n" ,$files['script'])),
					function($str){ 
						return $str?true:false;
					}
				);
				$this->info['files']['style'] = array_filter(array_map('trim',explode("\n" ,$files['style'])),function($str){ return $str?true:false;});
			}
		}
		
		public function get(){
			return $this->info;
		}
		
		public function getInfo(){
			return $this->info;
		}
		
	}
	
	class Category{
		public $info = array();
	}
	
	class App{
		
		private $url = "";
		public $category = "";
		
		public function __construct(){
			$this->db 		= new DB();
			$this->Data 	= new Request();
			$this->Image 	= new Image();
			
			$this->pageNum 	= $this->Data->getVar('page') && is_numeric($this->Data->getVar('page')) ? $this->Data->getVar('page') : 1;
			$this->sort 	= $this->Data->getVar('sort') ? $this->Data->getVar('sort') : "id"; 
			$this->sortby 	= $this->Data->getVar('sortby') ? $this->Data->getVar('sortby') : "asc";
			
			$this->Part 	= new Part( $this );
			$this->Page 	= new Page( $this );			
			
			$this->Page->db = $this->db;		
			
			$this->Category = new Category();
			
			$this->url = !empty( $this->Data->data['get']['url'] ) ? trim(str_replace('//','/',$this->Data->data['get']['url']),'/') : "";
			if(!empty($_SERVER['REQUEST_SCHEME'])){
				$this->home = $_SERVER['REQUEST_SCHEME']."://".$_SERVER['SERVER_NAME'].FOLDER;
			}else{
				$this->home = "http://libnik.com".FOLDER;
			}
		}
		
		public function getPages(){
			return $this->db
				->order("created desc")
				->limit(10,$this->pageNum)
				->SelectPages(array(
				'user_id' => $this->user['id']
			));
		}
		
		public function otherPages( $exludeID = 0 ){
			return $this->Page->other( $exludeID );		
		}
		
		public function getPage( $pageID = 0 ){
			$this->Page->setPageId($pageID);
			return $this->Page->getInfo();
		}
		
		public function getPageByUrl( $url ){
			$this->Page->setPageUrl($url);
			return $this->Page->getInfo();
		}
		
		public function setPageView(){
			$this->Part->set();
			$this->Page->setPageUrl($this->Data->getVar('url'));
			return new PageView($this->Page,$this->Part);
		}
		
		public function redirecIfNotLogged(){
			if( !$this->userLogged ){
				redirect(FOLDER . 'dash/login.php');
			}
		}
		
		public function getCategories($limit = 20){
			return $this->db
				->order("{$this->sort} {$this->sortby}")
				->limit($limit,$this->pageNum)
				->SelectCategories(array(
				//'user_id' => $this->user['id']
			));
		}
		
		public function getPagesByCategoryName( $limit = 12 ){
			$params = array(
				'user_id' => $this->user['id']
			);
			if( !empty($this->category) ){
				$cat = $this->db->SelectCategories(array(
					'name' => $this->category
				),true);
				$this->isNotCategory = false;
				if( !$cat ){
					$this->isNotCategory = true;
				}				
				$params['category_id'] = $cat['id'];
				$this->Category->info = $cat;
			}
			return $this->db
				->order("created desc")
				->limit($limit,$this->pageNum)
				->SelectPages($params);
		}
		
		public function getPagesByHome( ){
			$params = array(
				'user_id' => $this->user['id'],
				'show_home' => 1
			);
			if( count($this->splitUrl()) == 2 && $this->splitUrl()[0] == 'page' && is_numeric($this->splitUrl()[1]) ){
				$this->pageNum = $this->splitUrl()[1];
			}
			return $this->db
				->order("created desc")
				->limit(12,$this->pageNum)
				->SelectPages($params);
		}
		
		public function getPagesByTags( ){
			$params = array(
				'user_id' => $this->user['id'],
				'tags like "%'.$this->tag.'%"' => false
			);
			if( count($this->splitUrl()) == 2 && $this->splitUrl()[0] == 'page' && is_numeric($this->splitUrl()[1]) ){
				$this->pageNum = $this->splitUrl()[1];
			}
			return $this->db
				->order("created desc")
				->limit(12,$this->pageNum)
				->SelectPages($params);
		}
		
		public function getCategoriesAndPages( ){
			$blogs = $this->getCategories( 10 );
			$blogsPagination = $this->db->pgntion;
			foreach( $blogs as $key => $blog ){
				$this->category = $blog['name'];
				$blogs[$key]['pages'] = $this->getPagesByCategoryName(6);
				$blogs[$key]['pgntion'] = $this->db->pgntion;
				
			}
			return array(
				'blogs' => $blogs,
				'pagination' => $blogsPagination
			);
		}
		
		private function splitUrl( $str = null){
			if( $str != null){
				return explode("/",$this->url);
			}
			return explode("/",$this->url);
		}
		
		public function isPage(){
			if( empty(trim($this->splitUrl()[0])) ){
				return false;
			}
			return count($this->splitUrl()) == 1;
		}
		
		public function isCategoryPage(){
			$is = $this->splitUrl()[0] == 'c';
			if( isset( $this->splitUrl()[2] ) && isset( $this->splitUrl()[3] ) && $this->splitUrl()[2] == 'page' && is_numeric($this->splitUrl()[3]) ){
				$this->pageNum = $this->splitUrl()[3];
			}
			if( isset($this->splitUrl()[1]) && $this->splitUrl()[1] == 'page' && is_numeric($this->splitUrl()[2]) ){
				$this->pageNum = $this->splitUrl()[2];
			}
			if( $is ){
				if( isset($this->splitUrl()[1]) && $this->splitUrl()[1] != 'page'){
					$this->category = $this->splitUrl()[1];
				}
				return true;
			}
			return false;
		}
		
		public function isSitemap( ){
			$splt = $this->splitUrl();
			$urlsPerPage = 100;
			if( count($splt) == 1 ){
				preg_match("/sitemap\.xml/",$splt[0],$match);
				$this->stmps = array();
				if($match){
					$page = 0;
					$stmps = array_chunk($this->sitemaps() , $urlsPerPage);
					if( count($stmps) > 1 ){
						if(!empty($stmps[$page])){
							for($x=0;$x<count($stmps)-1;$x++){
								$h = get_headers($this->home."sitemap-".($x+1).".xml", TRUE);
								$this->stmps[] = array(
									"loc" => $this->home."sitemap-".($x+1).".xml",
									"mod" => date("Y-m-d",strtotime($h['Date']))
								);
							}
							$this->isSitemapIndex = true;
							return true;
						}
					}else if(count($stmps) == 1){ // means sitemap contains only 1 page
						if(!empty($stmps[$page])){
							foreach($stmps[$page] as $map){
								$isPage = $this->urlIsPage($map);
								if(!$isPage){
									$h = get_headers($map, TRUE);
									$date = $h['Date'];
								}else{
									$date = $isPage['updated'];
								}								
								$this->stmps[] = array(
									"loc" => $map,
									"mod" => date("Y-m-d",strtotime($date))
								);
							}
							$this->isSitemapIndex = false;
							return true;
						}
					}
				}
				preg_match("/sitemap\-(\d+)\.xml/",$splt[0],$match);
				if(!empty($match[1])){
					$page = $match[1] - 1;
					$stmps = array_chunk($this->sitemaps() , $urlsPerPage);
					if(!empty($stmps[$page])){
						foreach($stmps[$page] as $map){
							$isPage = $this->urlIsPage($map);
							if(!$isPage){
								$h = get_headers($map, TRUE);
								$date = $h['Date'];
							}else{
								$date = $isPage['updated'];
							}
							
							$this->stmps[] = array(
								"loc" => $map,
								"mod" => date("Y-m-d",strtotime($date))
							);
						}
						$this->isSitemapIndex = false;
						return true;
					}
				}
			}
			return false;
		}
		
		public function isTagPage(){
			$is = $this->splitUrl()[0] == 'tag';
			if( isset( $this->splitUrl()[2] ) && isset( $this->splitUrl()[3] ) && $this->splitUrl()[2] == 'page' && is_numeric($this->splitUrl()[3]) ){
				$this->pageNum = $this->splitUrl()[3];
			}
			if( isset($this->splitUrl()[1]) && $this->splitUrl()[1] == 'page' && is_numeric($this->splitUrl()[2]) ){
				$this->pageNum = $this->splitUrl()[2];
			}
			if( $is ){
				if( isset($this->splitUrl()[1]) && $this->splitUrl()[1] != 'page'){
					$this->tag = $this->splitUrl()[1];
				}
				return true;
			}
			return false;
		}
		
		public function isBlogPage(){
			$is = $this->splitUrl()[0] == 'blog';
			if( isset( $this->splitUrl()[2] ) && isset( $this->splitUrl()[3] ) && $this->splitUrl()[2] == 'page' && is_numeric($this->splitUrl()[3]) ){
				$this->pageNum = $this->splitUrl()[3];
			}
			if( isset($this->splitUrl()[1]) && $this->splitUrl()[1] == 'page' && is_numeric($this->splitUrl()[2]) ){
				$this->pageNum = $this->splitUrl()[2];
			}
			if( $is ){				
				return true;
			}
			return false;
		}
		
		public function isOfferPage(){
			$is = $this->splitUrl()[0] == 'offer';
			if( isset( $this->splitUrl()[2] ) && isset( $this->splitUrl()[3] ) && $this->splitUrl()[2] == 'page' && is_numeric($this->splitUrl()[3]) ){
				$this->pageNum = $this->splitUrl()[3];
			}
			if( isset($this->splitUrl()[1]) && $this->splitUrl()[1] == 'page' && is_numeric($this->splitUrl()[2]) ){
				$this->pageNum = $this->splitUrl()[2];
			}
			if( $is ){				
				return true;
			}
			return false;
		}
		
		public function getStructureFromPageName( $page_id ){
			$structure = array();
			
			$page = $this->db->SelectPages(array(
				'id' => $page_id
			),true);
			
			if( !$page ){
				return $structure;
			}
			
			$url = $this->home.$page['url'];
			
			$page_links = $this->db
				->columns(array(
					'sidebar_links.linked_page_id',
					'pages.title',
					'pages.url',
				))
				->joins(array(
					'table' => 'pages',
					'on' => 'pages.id = sidebar_links.linked_page_id'
				))
				->SelectSidebar_links(array(
				'main_page_id' => $page_id
			));
			
			foreach( $page_links as $link ) {
				$structure[] = array(
					'category_id' => $page['category_id'],
					'page_id' => $page['id'],
					'page_id_to' => $link['linked_page_id'],
					'text' => $link['title'],
					'url' => $url,
					'url_to' => $this->home.$link['url'],
					'nofollow' => 0
				);
			}
			
			//$doc = new DomDocument();
			
			//@$doc->loadHTML('<html><body>'.$page['content'].$page['custom_sidebar'].$page['custom_bottom'].'</body></html>');
			
			//$xpath = new DOMXPath($doc);
			//$anchors = $xpath->query("//a[@href]");	
			
			$anchors = $this->getLinksFromHTML($page['content'].$page['custom_sidebar'].$page['custom_bottom']);
			
			if( $anchors ){
				for( $x = 0 ; $x < count($anchors); $x++ ){
					$href = trim($this->fixSource($anchors[$x]['href'] , $url));						
					$urlToInfo = $this->urlIsPage($anchors[$x]['href']);				
					$urlToInfoCategory = $this->urlIsCategory($anchors[$x]['href']);				
					if( 
						$href && 
						$href != $url && 
						$href != "#" && 
						$href != $this->home &&
						( $urlToInfo || $urlToInfoCategory )
					){ 
						$nofollow = in_array("nofollow",explode(" ",$anchors[$x]['rel']));
						
						$page_id_to = $urlToInfo ? $urlToInfo['id'] : 0;
						$category_id = $urlToInfoCategory ? $urlToInfoCategory['id'] : $page['category_id'];
						
						$structure[] = array(
							'category_id' => $category_id,
							'page_id' => $page['id'],
							'page_id_to' => $page_id_to,
							'text' => $anchors[$x]['text'],
							'url' => $url,
							'url_to' => $href,
							'nofollow' => ( $nofollow ? 1 : 0 )
						);
						
					}
					
				}
				
			}
			
			return ($structure);
		}
		
		public function getTitleFromHTML( $html ){
			$doc = new DomDocument();			
			@$doc->loadHTML($html);
			
			$xpath = new DOMXPath($doc);
			$title = $xpath->query("//title");	
			
			$links = array();
			
			if( $title->length == 0 ){
				return "";
			}
			
			return trim($title->item(0)->nodeValue);
		}
		
		public function getLinksFromHTML( $html , $fixSource = "" ){
			$doc = new DomDocument();			
			@$doc->loadHTML('<html><body>'.$html.'</body></html>');
			
			$xpath = new DOMXPath($doc);
			$anchors = $xpath->query("//a[@href]");	
			
			$links = array();
			
			if( $anchors->length == 0 ){
				return $links;
			}
			
			for( $x = 0 ; $x < $anchors->length; $x++ ){
				$href = $anchors->item($x)->getAttribute('href');
				if( $href[0] != "/" && empty(parse_url($href)['host']) ){
					$href = $this->home . $href;
				}	
				if($fixSource){
					$href = $this->fixSource($href , $fixSource);
				}				
				$rel = trim($anchors->item($x)->getAttribute('rel'));
					
				
				$links[] = array(
					'text' => $anchors->item($x)->nodeValue,
					'href' => $href,
					'rel' => $rel,
				);
			}
			return $links;
		}
		
		public function urlIsPage( $url ){
			$parsedUrl = (parse_url($url));
			$name = str_replace( $this->home , "" , $url );		
			return $this->db->SelectPages(array(
				'url' => $name
			),true);			
		}
		
		public function urlIsCategory( $url ){
			$parsedUrl = (parse_url($url));
			$name = str_replace( $this->home . "c/" , "" , $url );		
			return $this->db->SelectCategories(array(
				'name' => $name
			),true);			
		}		
		
		public function fixSource( $url , $source ){
			
			if( !trim($url) ){
				return false;
			}
			
			// check is not a mailto: link
			if( preg_match("/^mailto\:/i",$url) ){
				return false;
			}
			
			// check is not a tel: link
			if( preg_match("/^tel\:/i",$url) ){
				return false;
			}
			
			// check is not a tel: link
			if( preg_match("/^\#/i",$url) ){
				return false;
			}
			
			$parsedUrl = (parse_url($url));
			$parsedSrc = (parse_url($source));
			
			//pr($url);
			//pr($parsedSrc);
			
			if( empty($parsedUrl['host']) ){
				$url = ( $url[0] == "/" ? $parsedSrc['host'] : $source ) . $url;
			}
			
			$parsedUrl = (parse_url($url));
			$scheme = !empty( $parsedUrl['scheme'] ) ? strtolower($parsedUrl['scheme']) : "http";
			$host = !empty( $parsedUrl['host'] ) ? strtolower($parsedUrl['host']) : "";
			$path = !empty( $parsedUrl['path'] ) ? $parsedUrl['path'] : "";
			$query = !empty( $parsedUrl['query'] ) ? "?" .$parsedUrl['query'] : "";
			
			$url = $scheme . "://" . $host . $path . $query;
			$url = explode("#",$url)[0];
			
			return $url;
		}
		
		public function get_process( $str ){
			exec('ps aux', $outputs);
			$row = array();
			foreach($outputs as $output){					
				preg_match("#". preg_quote($str) ."#i",$output,$m);				
				if($m){					
					$break = preg_replace("/\s+/"," ",$output);						
					$explode = explode(" ",$break);					
					$row[]  = $explode;					
				}						
			}
			return $row ;
		}
		
		public function sitemaps(){
			$froms 	= $this->db->group("from_url")->SelectMap();
			$tos 	= $this->db->group("to_url")->SelectMap();
			$this->ExcludedUrls	= explode("\n" , $this->user['exclude_sitemaps']);
			$this->cleanExcluded();
			$domain = parse_url($this->home);
			$links = array( $this->home );
			
			foreach( $froms as $url ){
				$prsd = parse_url($domain['scheme']."://".$url["from_url"]);
				if( !in_array($url['from_url'],$links) && $domain['host'] == $prsd['host'] ){
					if( strpos($domain['scheme']."://".$url["from_url"] , $this->home) === false ){
						
					}else{
						$links[] = $domain['scheme']."://".$url['from_url'];
					}
				}
			}
			
			foreach( $tos as $url ){
				$prsd = parse_url($domain['scheme']."://".$url["to_url"]);
				if( !in_array($url['to_url'],$links) && 
					$domain['host'] == $prsd['host']
				){
					if( strpos($domain['scheme']."://".$url["to_url"] , $this->home) === false ){
						
					}else{
						$links[] = $domain['scheme']."://".$url['to_url'];
					}
				}
			}
			$nlinks = array_unique($links);
			$links = array();
			// do not include category pages and tag pages.
			foreach( $nlinks as $nlink ){
				preg_match( "/\/c\//" , $nlink , $isCategory );
				preg_match( "/\/tag\//" , $nlink , $isTag );
				if( !$isCategory && !$isTag && !$this->sitemapIsExcluded($nlink) ){
					$links[] = $nlink;
				}				
			}
			
			return $links;
		}
		
		private function sitemapIsExcluded( $url ){
			return in_array($this->clearUrlString($url) , $this->ExcludedUrls);
		}
		
		private function clearUrlString( $url ){
			$parsed = parse_url($url);
			try{
				return trim(str_replace(array(
					"http://","https://"
				),array(
					"" , ""
				),
				$url));
			}catch(Exception $e){
				//return false;
			}
			return $url;
		}
		
		private function cleanExcluded(  ){
			foreach($this->ExcludedUrls as $key => $url){
				$this->ExcludedUrls[$key] = $this->clearUrlString($url);
			}
		}
		
	}
	$App = new App();
	$App->userLogged = isLogged();
	if( $App->userLogged )	{
		$App->user = $App->db->SelectUsers(array(),true);
	}else{
		$App->user = $App->db->SelectUsers(array(),true);
	}