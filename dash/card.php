<?php include '../lib/common.php';?>
<?php 
	$App->redirecIfNotLogged();
	$id = $_GET['iid'];
	
	$params = array('idea_id'=>$id,'topic'=>'','thought'=>'');
	
	if( isset($_GET['cid']) ){
		$hasBlank = $App->db->SelectCards(array('id'=>$_GET['cid']),true);
		$card = $hasBlank;
	}else{
		$hasBlank = $App->db->SelectCards($params,true);
	}
	
	if( $hasBlank ){
		$card_id = $hasBlank['id'];
	}else{
		$card_id = $App->db->InsertCards($params);
	}
	
?>
<?php include 'parts/header.php';?>
	<link rel="stylesheet" href="../css/style.css" />
	<style>
		body{
			background:#f4f4f4;
		}
		.card{
			margin:11em auto 0 auto;
			opacity:0;
		}
		.card input[name="Card[topic]"]{
			/*font-family: 'Google Sans', sans-serif;
			font-size: 30px;
			font-weight: bold;*/
		}
		.card textarea[name="Card[thought]"]{
			/*font-family: 'Google Sans', sans-serif;
			font-weight: normal;*/
		}
		div.mobile-plus-link{
			display:none;
		}
		div.w-count{
			width:500px;
			margin:5px auto;
			color: #a9a9a9;
			font-family : 'Open sans' , sans-serif;
		}
		@media (max-width: 480px){
			div.mobile-plus-link{
				text-align:center;
				display:none;
			}
			.card{
				margin:5em auto 0 auto;
			}
		}
	</style>
	<div class="container-fluid">
		<div class="top-links">
			<a href="<?=FOLDER?>dash/" data-toggle="tooltip" title="Dashboard" data-placement="bottom"><i class="fas fa-h-square"></i></a>
		</div>
		<div class="card">
			<form id="cardsForm">
				<input type="hidden" name="action" value="addCard" />
				<input type="hidden" name="Card[id]" value="<?=$card_id?>" />
				<input class="unbordered" type="text" name="Card[topic]" placeholder="Topic" value="<?=!empty($card['topic'])?$card['topic']:""?>" />
				<textarea class="unbordered" rows="7" name="Card[thought]" id="thought" placeholder="Thoughts"><?=!empty($card['thought'])?$card['thought']:""?></textarea>
				<input type="hidden" name="Card[idea_id]" value="<?=$id?>" />
			</form>			
		</div>
		<div class="text-right w-count">
			<span id="word-count"></span>
		</div>
		<div class="mobile-plus-link">
			<a style='color:#d2d2d2;font-size:50px;'>+</a>
		</div>
		<div id="stackLinks">
			<a href="stack.php?iid=<?=$id?>" data-toggle="tooltip" title="Stack"><i class="fa fa-bars" aria-hidden="true"></i></a>
		</div>
		<div id="ideaLinks">
			<a href="ideas.php" data-toggle="tooltip" title="Ideas"><i class="far fa-lightbulb"></i></a>
		</div>
	</div>
<?php include 'parts/footer.php';?>
<script type="text/javascript">
	if( $(window).width() <= 480 ){
		var mt = ($(window).height() / 2) - (($('.card').height()+70)/2);
		mt = mt < 0 ? 15 : mt;
		$('.card').css({
			'margin-top' : mt+'px'
		});
		$('div.mobile-plus-link').show();
	}
	$('.card').css({
		'opacity' : 1
	});
	var aTimeout,tTimeout;
	function saveCard(){
		var form = $('#cardsForm');
		aTimeout = $.ajax({
			url: 'request/index.php',
			type: 'post',
			dataType: 'json',
			data: form.serialize(),
			success: function(ret){
				
			},
			error: function(){
				
			}
		});
	}
	
	$('.unbordered').on('keyup',function(e){
		var ito = $(this);
		tTimeout = setTimeout(function(){saveCard();},100);
		
	}).on('keydown',function(){
		try{
			clearTimeout(tTimeout);
			aTimeout.abort();
		}catch(e){
			
		}
	});
	$(document).bind('keydown', '*', function(e) {
		var form = $('#cardsForm');
		if( e.altKey && e.which == 78 ){			
			$.ajax({
				url: 'request/index.php',
				type: 'post',
				dataType: 'json',
				data: form.serialize()+'&createNew=1',
				success: function(ret){
					$('[name="Card[id]"]').val(ret.id);
					$('.unbordered').val('');
					$('[name="Card[topic]"]').focus();
				},
				error: function(){
					
				}
			});
		}
    }).bind('keyup','#thought',function(){
		var ito= $(this);
		$('#word-count').text(function(){
			return $.trim($('#thought').val()) ? $('#thought').val().replace(/\n/g , " ").replace(/\s+/g , " ").split(" ").filter(function(w){ return $.trim(w); }).length + "": "0";
		});
	});
	$('#word-count').text(function(){
		return $.trim($('#thought').val()) ? $('#thought').val().replace(/\n/g , " ").replace(/\s+/g , " ").split(" ").filter(function(w){ return $.trim(w); }).length + "": "0";
	});
	$('div.mobile-plus-link a').click(function(){
		var form = $('#cardsForm');
		$.ajax({
			url: 'request/index.php',
			type: 'post',
			dataType: 'json',
			data: form.serialize()+'&createNew=1',
			success: function(ret){
				$('[name="Card[id]"]').val(ret.id);
				$('.unbordered').val('');
				$('[name="Card[topic]"]').focus();
			},
			error: function(){
				
			}
		});
	});
	
</script>
