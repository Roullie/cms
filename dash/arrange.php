<?php include '../lib/common.php';?>
<?php 
	$App->redirecIfNotLogged();
	$id = $_GET['iid'];
	$params = array('idea_id'=>$id);
	$cards = $App->db
		->columns(array(
			'id',
			'idea_id',
			'topic',
			'(CONCAT(topic, " " , thought)) as words'
		))
		->order('arrangement asc')
		->SelectCards($params);
?>
<?php include 'parts/header.php';?>
	<link rel="stylesheet" href="../css/style.css" />
	<style>
		body{
			background:#f5f5f5;
		}
		.paper{
			margin-top:150px;
			opacity:0;
		}
		.unbordered{
			font-family: 'Open Sans', sans-serif;
		}
		#clist{
			width: 80%;
		}
		#clist ul{
			line-height: 2em;
			padding-left:10px;
		}
		#clist ul>li{
			list-style-type: none;
			cursor: grab;
		}
		h2.idea-titile{
			font-size:40px;
		}
		@media (max-width: 480px){
			h2.idea-titile{
				font-size:36px;
			}
		}
	</style>
	<div class="container-fluid">
		<div class="top-links">
			<a href="<?=FOLDER?>dash/" data-toggle="tooltip" title="Dashboard" data-placement="bottom"><i class="fas fa-h-square"></i></a>
		</div>
		<div class="paper">
			<h2 class="idea-titile">Logic Chain</h2>
			<div id="clist" class="text-left"></div>			
		</div>
		<div id="ideaLinks">
			<a href="ideas.php" data-toggle="tooltip" title="Ideas"><i class="far fa-lightbulb"></i></a>&nbsp;&nbsp;
			<a href="compilation-simplemde.php?iid=<?=$id?>" data-toggle="tooltip" title="Compilations"><i class="fa fa-tasks" aria-hidden="true"></i></a>
		</div>
		<div id="stackLinks">
			<a href="stack.php?iid=<?=$id?>" data-toggle="tooltip" title="Stack"><i class="fa fa-bars" aria-hidden="true"></i></a>
		</div>
	</div>
	
<?php include 'parts/footer.php';?>
<script type="text/javascript">
	var cards = <?=json_encode($cards);?>;
	function createCardList(){
		var ul = '<ul>';
		for( var i=0; i< cards.length; i++ ){
			var c = cards[i];
			ul += '<li class="_card" data-id="'+c.id+'">';
			ul += '<a href="card.php?iid='+c.idea_id+'&cid='+c.id+'">'+c.topic+'</a>';
			if( $.trim(c.words) != "" ){
				var wordCount = $.trim(c.words).replace(/[^a-zA-Z0-9]/g,' ').replace(/\s+ /g,' ').split(' ').length;
				ul += "&nbsp;<span style='color:#d2d2d2;font-size:10px;'>"+wordCount+"</span>";
			}			
			ul += '</li>';
		}
		ul += '</ul>';
		return ul;
	}
	$('#clist').html(createCardList());
	listSortable();
	if( $(window).width() <= 480 ){
		var mt = ($(window).height() / 2) - (($('.paper').height()+70)/2);
		mt = mt < 0 ? 15 : mt;
		$('.paper').css({
			'margin-top' : mt+'px'
		});
	}
	$('.paper').css({
		'opacity' : 1
	});
	function listSortable(){
		try{
			$('#clist ul').sortable( "destroy" );
		}catch(e){
			
		}
		
		$('#clist ul').sortable({update: function(){
			var arr = [];
			$.each($('._card'),function(i,o){
				arr.push($(o).data('id'));
			});
			$.ajax({
				url: 'request/index.php',
				type: 'post',
				dataType: 'json',
				data: {
					action: 'saveArr',
					ids : arr
				},
				success: function(ret){
					
				},
				error: function(){
					
				}
			});
		}});
	}
</script>
