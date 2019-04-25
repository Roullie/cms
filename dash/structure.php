<?php include '../lib/common.php';?>
<?php 
	$App->redirecIfNotLogged();
	$App->Part->set();
	$info = $App->Part->get();
	
	$cats = $App->db->columns(array('id','name'))->SelectCategories();
	
	$pagesCount = 0;
	$nodes = array(
		array(
			'text' => 'Home',
			'id' => "TopPage",
			'type' => 'top',
			'url' => $App->home
		)
	);
	
	$connections = $App->db->SelectMap();
	
	$tags = array();
	foreach( $cats as $key => $cat ){
		
		$nodes[] = array(
			'text' => $cat['name'],
			'id' => "_c".$cat['id'],
			'type' => 'category',
			'url' => $App->home . 'c/' . $cat['name']
		);		
		
		$cats[$key]['pages'] = $App->db->columns(array('id','title','url','tags'))->SelectPages(array(
			'category_id' => $cat['id']
		));
		
		foreach( $cats[$key]['pages'] as $pkey => $page ){
			
			$pagesCount++;
			
			$nodes[] = array(
				'text' => $page['title'],
				'id' => "_p".$page['id'],
				'url' => $App->home.$page['url'],
				'type' => 'page'
			);
			
			$_tags = array_map_recursive("trim",explode(",",$page['tags']));
			
			foreach( $_tags as $tag ){
				if( !in_array($tag,$tags) && trim($tag)){
					$tags[] = $tag;
				}
			}
			
		}
		
	}
	
	foreach( $tags as $key => $tag ){
		$nodes[] = array(
			'text' => $tag,
			'id' => "_t".$key,
			'url' => $App->home . 'tag/' . $tag,
			'type' => 'tag'
		);
	}
	$otherNodes = array();
	$blog = array_filter($connections,function($conn) use($App){
		return trim($conn['from_url'],"/") == $App->home . 'blog';
	});
	if($blog){
		$otherNodes[] = array(
			'text' => "Blog",
			'id' => "_blogPage",
			'url' => $App->home . 'blog/',
			'type' => 'blog'
		);
	}
	$offer = array_filter($connections,function($conn) use($App){
		return trim($conn['from_url'],"/") == $App->home . 'offer';
	});
	if($offer){
		$otherNodes[] = array(
			'text' => "Offer Page",
			'id' => "_offerPage",
			'url' => $App->home . 'offer/',
			'type' => 'offer'
		);
	}
	foreach( $otherNodes as $other ){
		$nodes[] = array(
			'text' => $other['text'],
			'id' => $other['id'],
			'url' => $other['url'],
			'type' => $other['type'],
		);
	}
	
	foreach( $nodes as $key => $node ){
		$nodes[$key]['url'] = str_replace(array("http://","https://"),array("",""),$nodes[$key]['url']);
	}
	
?>
<?php include 'parts/header.php';?>
	<style>
		div.gridContainerTop{
			overflow: auto;
		}
		div.gridContainer{
			position:relative;
			width:100%;
			height:600px;
			    overflow: auto;
		}
		div.gridContainer svg{
			z-index:1040 !important;
		}
		div.gridContainer svg.makeFront{
			z-index:1070 !important;
		}
		div.nodes{
			position:absolute;
			padding: 10px;
			border: 1px solid #ccc;
			background: #fff;
			z-index:1060;
			width:140px;
			text-align:center;
			font-size:14px;
			opacity:0;
			font-family: 'Open Sans';
			font-weight:700;
		}
		div.connectorLabel {
			font-size:14px;
			z-index:1050;
			display:none !important;
			font-family: 'Open Sans';
			font-weight:700;
			font-style:italic;
		}
		div.connectorLabel.showLabel{
			display:block !important;
		}
		div.map-options{
			padding:5px;
		}
		div.map-options label{
			font-weight:normal;
		}
		div.map-options label input[type=checkbox]{
			position:relative;
			top:1px;
		}
		div.progress{
			width: calc(100% - 160px);
			margin: 0 20px 0 0;
			display: inline-block;
			height: 30px;
			position:relative;
			display:none;
		}
		span#loadProgress{
			position:absolute;
			top:0;
			left:0;
			width:100%;
			padding: 5px;
			text-align:center;
		}
	</style>
		<div class="container-fluid">
		  <div class="row">
			<div class="col-sm-3 col-lg-2">
				<?php include 'parts/nav.php';?>				
			</div>
			<div class="col-sm-9 col-lg-10">
				<div class="map-options">
					<label class="col-md-3">
						<input type="checkbox" class="hideNoFollow"> Hide nofollow
					</label>
					<div class="col-md-9 text-right">
						<div class="row">
							
							<div class="progress">
								<div class="progress-bar progress-bar-primary" style="width:10%"></div>
								<span id="loadProgress"></span>
							</div>
							
							<button class="btn btn-primary btn-sm pull-right" id="updateLinkTable">Update Link Table</button>
							
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="gridContainerTop">					
					<div class="gridContainer">
						<div id="TopPage" class="nodes">
							<div class="nodeText"><a href="<?=$App->home?>" target="_blank">Home</a></div>
						</div>
						<?php 
							$boxHeight = 40;
							$boxWidth = 140;
							$top = $boxHeight * 2;
							$left = 0;
							$highest = $top;
						?>
						<?php foreach($cats as $key => $cat){?>
							<div id="<?="_c".$cat['id']?>" class="nodes" style="top:<?=$top?>px;left:<?=$left?>px;">
								<div class="nodeText"><a href="<?=$App->home.'c/'.$cat['name']?>" target="_blank">CAT:<?=$cat['name']?></a></div>
							</div>							
							<?php foreach($cat['pages'] as $page){?>
								<?php 
									$top += $boxHeight * 2;
									$left += 30;
								?>
								<div id="<?="_p".$page['id']?>" class="nodes" style="top:<?=$top?>px;left:<?=$left?>px;">
									<div class="nodeText"><a href="<?=$App->home.$page['url']?>" target="_blank"><?=$page['title']?></a></div>
								</div>
							<?php } ?>
							<?php 
								if( $highest < $top ){
									$highest = $top;
								}
								$top = $boxHeight * 2;
								$left = ( $boxWidth * ($key + 1) ) * 2;
							?>
						<?php } ?>
						<?php 
							$top = $highest + $boxHeight * 2;
							$left = 0;
						?>
						<?php foreach( $tags as $key => $tag ){ ?>
							<div id="<?="_t". $key?>" class="nodes" style="top:<?=$top?>px;left:<?=$left?>px;">
								<div class="nodeText"><a href="<?=$App->home.'tag/'.$tag?>" target="_blank">TAG:<?=$tag?></a></div>
							</div>	
							<?php 
								$left = ( $boxWidth * ($key + 1) ) * 2;
							?>
						<?php }	?>
						<?php if($otherNodes){?>
							<?php 
								$top = $top + $boxHeight * 2;
								$left = 0;
							?>
							<?php foreach( $otherNodes as $other ){ ?>
								<div id="<?=$other['id']?>" class="nodes" style="top:<?=$top?>px;left:<?=$left?>px;">
									<div class="nodeText"><a href="<?=$other['url']?>" target="_blank"><?=$other['text']?></a></div>
								</div>	
								<?php 
									$left = ( $boxWidth * ($key + 1) ) * 2;
								?>
							<?php }	?>	
						<?php } ?>
					</div>
				</div>
				
			</div>
		  </div>
		</div>
<?php include 'parts/footer.php';?>
<script type="text/javascript">
	var home = '<?=$App->home?>';
	var urls = [
		{
			href: home,
			//text : 'Home',
			connections : []
		}
	];
	
	$('#updateLinkTable').click(function(){
		var ito = $(this);
		ito.prop('disabled',true);
		$('div.progress div.progress-bar').width(0);
		$('div.progress').css('display','inline-block');
		$('span#loadProgress').text("");
		getLinks(0);
	});
	function getLinks( index ){
		
		index = index || 0;
		
		if( index < urls.length ){
			
			$('div.progress div.progress-bar').width( ((index / urls.length) * 100 ) + "%");
			$('span#loadProgress').text("Processing " + urls[index].href);
			$.ajax({
				url : 'request/index.php',
				type: 'post',
				dataType: 'json',
				data : {
					action: 'getLinksOnPage',
					url : urls[index],
					index : index
				},
				success: function(ret){
					
					if( ret.status == 1 ){
						
						for(var i=0; i<ret.links.length; i++){
							urls[index].connections.push(ret.links[i]);
							var l = urls.filter(function(u){
								return u.href == ret.links[i].href;
							});
							if( ret.links[i].rel != "nofollow" && l.length == 0 ){
								urls.push({
									href : ret.links[i].href,
									connections : [],
									title : "",
								});
							}						
						}
						urls[index].title = ret.title;
						setTimeout(function(){
							getLinks( index + 1 );
						},1000);
					}
					
				},
				error: function(){
					
				}
			});
		}else{
			$('#updateLinkTable').prop('disabled',false);
			$('div.progress div.progress-bar').width(0);
			$('div.progress').hide();
			alert("Refresh the page to show the updated version.");
			/*
			urls.forEach(function(u){
				var sourcenode = nodes.filter(function(_sn){
					return _sn.url.toLowerCase() == u.href.toLowerCase();
				});
				if( sourcenode.length ){
					
					u.connections.forEach(function(un){
						
						var targetnode = nodes.filter(function(_tn){
							return _tn.url.toLowerCase() == un.href.toLowerCase();
						});
						
						if( targetnode.length ){
							
							var c = un.rel == 'nofollow' ? "red" : "blue";
			
							var createdConn = plumbInstance.connect({
							  source: $("#" + sourcenode[0].id ),
							  target: $("#" + targetnode[0].id ),
							  anchors:[ "Bottom","Top" ]
							},
							{
								endpoint: 'Blank',
								connector: ["Straight",{alwaysRespectStubs:true,cornerRadius:1}],
								paintStyle: { stroke:c ,strokeWidth:2 },
								overlays: [
									[ 'Label', { label: un.text, cssClass: 'connectorLabel s'+sourcenode[0].id+'t'+targetnode[0].id } ]
								],
							});
							
							if( un.rel == 'nofollow' ){
								createdConn.addClass("nofollow-line");
								console.log(createdConn);
							}
							
							createdConn.bind("mouseover", function(conn) {
								console.log(conn);
								$('.connectorLabel.s'+sourcenode[0].id+'t'+targetnode[0].id).addClass('showLabel');
							}); 

							createdConn.bind("mouseout", function(conn) {
								$('.connectorLabel.s'+sourcenode[0].id+'t'+targetnode[0].id).removeClass('showLabel');
							});
							
							
							
						}
						
					});
					
				}
			});
			*/
		}
		
	}
	
	
	var nodes = <?=json_encode($nodes)?>;
	var connection = <?=json_encode($connections)?>;
	var pagesCount = <?=$pagesCount?>;
	var catCount = <?=count($cats)?>;
	
	$('#TopPage').css('left',function(){
		return ($('.gridContainer').width() / 2) - ($(this).width() / 2);
	});
	$('div.nodes').css('opacity',1);
	var plumbInstance = jsPlumb.getInstance();
	plumbInstance.ready(function() {
		plumbInstance.importDefaults({
			//Anchors: [ "TopCenter" ],
			Overlays: [
				[ "Arrow", { 
					location:1,
					id:"arrow",
					length:10,
					width:15,
					foldback:0.8
				} ]
			]
		});
		for( var i = 0; i < connection.length; i++ ){
			var n = nodes.slice(0);
			var sourcenode = n.filter(function(_sn){
				return _sn.url.toLowerCase() == connection[i].from_url.toLowerCase();
			});
			var targetnode = n.filter(function(_sn){
				return _sn.url.toLowerCase() == connection[i].to_url.toLowerCase();
			});
			var hasConnection = false;
			try{
				hasConnection = plumbInstance.getConnections({source:sourcenode[0].id, target:targetnode[0].id});
				hasConnection = hasConnection.length ? true: false;
			}catch(e){
				console.log(e);
			}
			if( sourcenode.length && targetnode.length && !hasConnection ){
				
				var c = connection[i].rel == 'nofollow' ? "red" : "blue";
		
				var createdConn = plumbInstance.connect({
				  source: $("#" + sourcenode[0].id ),
				  target: $("#" + targetnode[0].id ),
				  anchors:[ "Bottom","Top" ]
				},
				{
					endpoint: 'Blank',
					connector: ["Straight",{alwaysRespectStubs:true,cornerRadius:1}],
					paintStyle: { stroke:c ,strokeWidth:2 },
					overlays: [
						[ 'Label', { label: connection[i].text, cssClass: 'connectorLabel s'+sourcenode[0].id+'t'+targetnode[0].id } ]
					],
				});
				
				if( connection[i].rel == 'nofollow' ){
					createdConn.addClass("nofollow-line");
					
				}
				
				createdConn.bind("mouseover", function(conn) {				
					conn.setPaintStyle({stroke:"#39ff14", strokeWidth:2,pastStroke:conn.getPaintStyle().stroke});
					$('.connectorLabel.s'+conn.sourceId+'t'+conn.targetId).addClass('showLabel');
					conn.addClass('makeFront');
				}); 

				createdConn.bind("mouseout", function(conn) {
					var c = conn.hasClass('nofollow-line') ? "red" : 'blue';
					conn.setPaintStyle({stroke:c, strokeWidth:2});
					$('.connectorLabel.s'+conn.sourceId+'t'+conn.targetId).removeClass('showLabel');
					conn.removeClass('makeFront');
				});
				
				
				
			}
		}
	}); 
	var app = {
		top_largest : 0,
		top : 215,
		left: 0
	};
	var nodePages = nodes;
	for( var i=0; i < nodePages.length; i++ ){
		createBox(nodePages[i]);
	}
	app.left = 0;
	
	
	//$('.gridContainer').append(element);
	/*
	for(var x=0;x< connections.length; x++){
		
		var c = connections[x].type == 'pageToPage' ? "red" : "#333";
		
		var createdConn = plumbInstance.connect({
		  source: $("#" + connections[x].source ),
		  target: $("#" + connections[x].target ),
		  //uuids:[connections[x].source + "_BottomEndpoint", connections[x].target + "_TopEndpoint"],
		  //paintStyle:{ fill:"white", outlineStroke:"grey", strokeWidth:2,radius:1 },
		  anchors:[ "Bottom","Top" ]
		},
		{
			endpoint: 'Blank',
			connector: ["Straight",{alwaysRespectStubs:true,cornerRadius:1}],
			paintStyle: { stroke:c ,strokeWidth:2 },
			overlays: [
				[ 'Label', { label: connections[x].text, cssClass: 'connectorLabel s'+connections[x].source+'t'+connections[x].target } ]
			],
		});
		
		createdConn.bind("mouseover", function(conn) {
			console.log(conn);
			$('.connectorLabel.s'+conn.sourceId+'t'+conn.targetId).addClass('showLabel');
		}); 

		createdConn.bind("mouseout", function(conn) {
			$('.connectorLabel.s'+conn.sourceId+'t'+conn.targetId).removeClass('showLabel');
		});
		
	}
	*/
	
	function createBox( node ){
		
		//return false;
		
		var id = node.id;
		var top = app.top;
		var text = node.text;
		left = 50;
		
		if( top > app.top_largest ){
			app.top_largest = top;
		}
		
		//if( node.type == 'page' ){
			text = '<a href="'+node.url+'" target="_blank">'+text+'</a>';
		//}
		
		var element = $(
			'<div id="'+id+'" class="nodes" style="top:'+(top)+'px;left:'+(app.left)+'px">'+
				'<div class="nodeText">'+text+'</div>'+
			'</div>'
		);
		
		//$('.gridContainer').append(element);
		
		//app.top += ( $('#' + id).height() + 50);
		
		plumbInstance.draggable(id, {
			//containment:"parent",
			grid:[10,10],
			stop: function(e){
				
				
			}
		});	
		/*
		plumbInstance.addEndpoint(id, { 
			anchors:["Left"],
			cssClass : 'LeftEndpoint',
			uuid: id + "_LeftEndpoint"				
		}, {
			paintStyle:{ fill:"white", outlineStroke:"grey", strokeWidth:1,radius:1 },
			isSource:false, // draggable from point (this is the from)
			isTarget:false, // draggable end point (this is the to)
			endpoint: "Dot",
			maxConnections: -1,
			dropOptions:{ 
				drop:function(e, ui) { 
				} 
			}
		}); 
		
		plumbInstance.addEndpoint(id, { 
			anchors:["Top"],
			cssClass : 'TopEndpoint',
			uuid: id + "_TopEndpoint"
		}, {
			paintStyle:{ fill:"white", outlineStroke:"grey", strokeWidth:1,radius:1 },
			isSource:false, // draggable from point (this is the from)
			isTarget:false, // draggable end point (this is the to)
			endpoint: "Dot",
			maxConnections: -1,
			dropOptions:{ 
				drop:function(e, ui) { 
				} 
			}
		}); 
		
		plumbInstance.addEndpoint(id, { 
			anchors:["Right"],
			cssClass : 'RightEndpoint',
			uuid: id + "_RightEndpoint"
		}, {
			//paintStyle:{ fill:"red", stroke:"red", strokeWidth:1,radius:4 },
			paintStyle:{ fill:"white", outlineStroke:"grey", strokeWidth:1,radius:1 },
			isSource:false, // draggable from point (this is the from)
			isTarget:false, // draggable end point (this is the to)
			connector: ["Flowchart",{
				alwaysRespectStubs:true,
				cornerRadius:5
			}],
			connectorStyle: { stroke:"#333", strokeWidth:2 },
			endpoint: "Dot",					
			maxConnections: -1
		}); 
		
		plumbInstance.addEndpoint(id, { 
			anchors:["Bottom"],
			cssClass : 'BottomEndpoint',
			uuid: id + "_BottomEndpoint"
		}, {
			//paintStyle:{ fill:"green", stroke:"green", strokeWidth:1,radius:4 },
			paintStyle:{ fill:"white", outlineStroke:"grey", strokeWidth:1,radius:1 },
			isSource:false, // draggable from point (this is the from)
			isTarget:false, // draggable end point (this is the to)
			connector: ["Flowchart",{
				alwaysRespectStubs:true,
				cornerRadius:5
			}],
			connectorStyle: { stroke:"#333", strokeWidth:1 },
			endpoint: "Dot",
			dropOptions:{ 
				drop:function(e, ui) { 
					//alert('drop!'); 
				} 
			},
			maxConnections: -1
		});
		
			*/	
		
		
	}
	$('.gridContainer').height( ( $(window).height() - 40 ) + "px" );
	
	$('.hideNoFollow').on('change',function(){
		var ito = $(this);
		var noFollows = nodes.filter(function(n){
			return n.nofollow;
		});
		plumbInstance.getAllConnections().forEach(function(conn){
			if( conn.hasClass('nofollow-line') && ito.is(':checked') ){
				conn.setVisible(false);
			}else{
				conn.setVisible(true);
			}
		});
		
		
	});
	
	$('.nodes').on('mouseover',function(){
		var ito = $(this);
		//$('.hideNoFollow').prop('checked',false);
		
		// hide all conns
		var allConns = plumbInstance.getAllConnections();
		//$('.nodes').not(ito).hide();
		//for(var c=0;c<allConns.length;c++){
		   //allConns[c].setVisible(false);
		//}
		var conns = plumbInstance.getConnections({target:ito.attr('id')});		
		
		//traceTillHome(ito.attr('id'));
		
	}).on('mouseout',function(){
		
		var allConns = plumbInstance.getAllConnections();
		for(var c=0;c<allConns.length;c++){
		   //allConns[c].setVisible(true);
		}
		$('.nodes').show();
	});
	
	function traceTillHome( id ){
		if( id == 'TopPage' ){
			return false;
		}
		var conns = plumbInstance.getConnections({target:id});
		for(var c=0;c<conns.length;c++){
			conns[c].setVisible(true);
			$('#'+conns[c].sourceId).show();
			traceTillHome( conns[c].sourceId );
		}
	}
	
	var _traces = [];
	//allTraces( 'TopPage' , ['TopPage'] );
	
	function allTraces( id , traces ){
		var __traces = traces.slice(0);
		
		var conns = plumbInstance.getConnections({source:id});
		if( conns.length > 0 ){
			var accepted = [];
			for(var c=0;c<conns.length;c++){
				if( !in_array( conns[c].targetId , __traces ) ){
					accepted.push(conns[c].targetId);
				}
			}
			if( accepted.length > 0 ){
				for(var i=0; i< accepted.length; i++){
					var tmp = __traces.slice(0);
					tmp.push(accepted[i]);
					allTraces( accepted[i] , tmp );
				}
			}else{
				
				_traces.push(traces);
				//return traces;
			}
		}else{
			_traces.push(traces);
			//return traces;
		}
		
	}
	
	function in_array(needle, haystack) {
		var length = haystack.length;
		$break = false;
		for(var i = 0; i < length; i++) {
			if(haystack[i] == needle) {$break = true;break;}
		}
		return $break;
	}
	//getLinks(0);
</script>