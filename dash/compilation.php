<?php include '../lib/common.php';?>
<?php 
	$App->redirecIfNotLogged();
	$compilations = $App->db
	->order('compilations.arrangement asc')
	->columns(array(
		'ideas.id as idea_id',
		'ideas.idea',
		'compilations.id as compile_id',
		'compilations.compiled',
		'compilations.updated',
		'compilations.created',
	))
	->joins(array(
		'table' => 'ideas',
		'on' => 'ideas.id = compilations.idea_id'
	))
	->SelectCompilations(array(
		'ideas.status' => 1,
		'compilations.compiled !=' => ""
	));
?>
<?php include 'parts/header.php';?>
	<link rel="stylesheet" href="../css/style.css" />
	<style>
		body{
			background:#f5f5f5;
		}
		.paper{
			margin-top:150px;
		}
		.unbordered{
			font-family: 'Open Sans', sans-serif;
		}
		#clist ul{
			line-height: 3em;
			padding-left:10px;
		}
		#clist ul>li{
			list-style-type: none;
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
			<h2 class="idea-titile">Compilations</h2>
			<div id="clist" class="text-left"></div>			
		</div>
		<div id="ideaLinks">
			<a href="ideas.php" data-toggle="tooltip" title="Ideas"><i class="far fa-lightbulb"></i></a>&nbsp;&nbsp;
		</div>
	</div>
	
<?php include 'parts/footer.php';?>
<script type="text/javascript">
	var compilations = <?=json_encode($compilations);?>;
	function toYMD(date){
		
		
		var exploded = date.split(" ");
		exploded[0] = exploded[0].split("-");
		exploded[1] = exploded[1].split(":");
		
		var date = new Date(
			exploded[0][0],
			(exploded[0][1] - 1),
			exploded[0][2],
			exploded[1][0],
			exploded[1][1],
			exploded[1][2]
		);
		
		var y = date.getFullYear();
		var m = date.getMonth() + 1;
		var d = date.getDate();
		var min = date.getMinutes();
		var hour = date.getHours();
		
		m = ( m < 10 ? "0" : "" ) + m;
		d = ( d < 10 ? "0" : "" ) + d;
		min = ( min < 10 ? "0" : "" ) + min;
		hour = ( hour < 10 ? "0" : "" ) + hour;
		
		return  m + '/' + d  + ' ' + hour + ':' + min;
		
	}
	function createList(){
		var ul = '<ul>';
		for( var i=0; i< compilations.length; i++ ){
			var c = compilations[i];
			var text = $('<div>'+c.compiled+'</div>').text();
			ul += '<li class="comps" data-cid="'+c.compile_id+'">';
			ul += '<a href="compilation-simplemde.php?cid='+c.compile_id+'">'+text.substr(0,25)+'</a>';
			ul+= '&nbsp;<span style="color:#d2d2d2;font-size:10px;">'+toYMD(c.updated)+'</span>';
			ul+= '&nbsp;<a style="color:#d2d2d2;font-size:10px;" class="removeCompilation" data-id="'+c.compile_id+'" data-toggle="tooltip" title="Remove compilation" data-placement="top"><i class="fa fa-times" aria-hidden="true"></i></a>';
			ul += '</li>';
		}
		ul += '</ul>';
		return ul;
	}
	$('#clist').html(createList());
	listSortable();
	function listSortable(){
		try{
			$('#clist ul').sortable( "destroy" );
		}catch(e){
			
		}
		
		$('#clist ul').sortable({update: function(){
			var arr = [];
			$.each($('.comps'),function(i,o){
				arr.push($(o).data('cid'));
			});
			$.ajax({
				url: 'request/index.php',
				type: 'post',
				dataType: 'json',
				data: {
					action: 'saveArrCompilations',
					ids : arr
				},
				success: function(ret){
					
				},
				error: function(){
					
				}
			});
		}});
	}
	$('.removeCompilation').click(function(){
		var ito = $(this);
		if(confirm("Are you sure to remove this Compilation?")){
			$.ajax({
				url: 'request/index.php',
				type: 'post',
				dataType: 'json',
				data: {
					action: 'removeCompilation',
					id : ito.data('id')
				},
				success: function(ret){
					if( ret.status == 1 ){
						ito.parent().remove();
					}
				},
				error: function(){
					
				}
			});
		}
	});
</script>
