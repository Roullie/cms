<?php include '../lib/common.php';?>
<?php 
	$App->redirecIfNotLogged();
	$id = $_GET['iid'];
	
	$params = array('idea_id'=>$id);
	
	$cards = $App->db->order('arrangement asc')->SelectCards($params);
	
	
?>
<?php include 'parts/header.php';?>
	<link rel="stylesheet" href="../css/style.css" />
	<style>
		body{
			background:#f4f4f4;
		}
		#arrangeLink{
			position: fixed;
			top: 0;
			width: 100%;
			left: 0;
			padding: 5px;
		}
		div.card:hover div.card-option{
			display:block;
			width:100%;
			left:0;
			right:0;
			padding: 5px 10px;
		}
		div.card:hover div.card-option a{
			color: #a7a7a7;
		}
	</style>
	<div class="container-fluid">
		<div id="scaleButtons">
			<div class="row">
				<button class="btn btn-default scale-down">-</button>
				<input type="text" class="form-control" value="100" style="width:100px;display:inline-block;vertical-align: middle;"/>
				<button class="btn btn-default scale-up">+</button>
			</div>
		</div>
		<div id="wrapper">
			<div id="arrangeCards">
				<?php foreach($cards as $card){?>
					<?php if( $card['topic'] && $card['thought'] ){?>
						<div class="card" data-id="<?=$card['id']?>">
							<div class="_topic"><?=$card['topic']?></div>
							<div class="_thought"><?=nl2br($card['thought'])?></div>
							<div class="card-option">
								
								<a href="javascript:void(0)" class="removeCard pull-left" data-cid="<?=$card['id']?>" data-toggle="tooltip" title="Delete" data-placement="top"><i class="fas fa-trash-alt"></i></a>
								<a href="card.php?iid=<?=$card['idea_id']?>&cid=<?=$card['id']?>" class="pull-right" data-toggle="tooltip" title="Edit" data-placement="top">&nbsp;<i class="fas fa-pencil-alt"></i></a>
								<div class="clearfix"></div>
								
							</div>
						</div>
					<?php } ?>
				<?php } ?>
			</div>
		</div>
		<div id="ideaLinks">
			<a href="ideas.php" data-toggle="tooltip" title="Ideas"><i class="far fa-lightbulb"></i></a>&nbsp;&nbsp;
			<a href="compilation-simplemde.php?iid=<?=$card['idea_id']?>" data-toggle="tooltip" title="Compilation Edit"><i class="fas fa-tasks"></i></a>
		</div>
		<div id="arrangeLink">
			<a href="<?=FOLDER?>dash/" class="pull-left" data-toggle="tooltip" title="Dashboard" data-placement="bottom"><i class="fas fa-h-square"></i></a>
			<a href="arrange.php?iid=<?=$id?>"  class="pull-right" data-toggle="tooltip" title="Arrange" data-placement="bottom"><i class="fas fa-list-alt"></i></a>
			<div class="clearfix"></div>
		</div>
	</div>
<?php include 'parts/footer.php';?>
<script type="text/javascript">
	var scaled = 100;
	$('#arrangeCards').sortable({
		update: function(){
			var arr = [];
			$.each($('#arrangeCards .card'),function(i,o){
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
		}
	});
	$('#scaleButtons button').click(function(e){
		var ito = $(this);
		if( ito.hasClass('scale-down') ){
			scaled -= 25;
		}else{
			scaled += 25;
		}
		$('#arrangeCards').css('transform','translate(0%, -'+ Math.round( (scaled/100) /2 ) +'%)  scale('+(scaled/100)+')');
		$('#scaleButtons input[type=text]').val(scaled);
	});
	$('.removeCard').click(function(){
		var ito = $(this);
		if( confirm("Are you sure to remove this card?") ){
			ito.prop('disabled',true);
			$.ajax({
				url: 'request/index.php',
				type: 'post',
				dataType: 'json',
				data: {
					action: 'removeCard',
					cid : ito.data('cid')
				},
				success: function(ret){
					if(ret.status == 1){
						ito.parent().parent().remove();
					}
				},
				error: function(){
					
				}
			});
		}
	});
</script>
