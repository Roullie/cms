<?php include '../lib/common.php';?>
<?php 
	$App->redirecIfNotLogged();
	$ideas = $App->db->order('arrangement asc')->columns(array(
		'id',
		'idea',
		'status',
		'(select count(cards.id) from cards where cards.idea_id = ideas.id and cards.topic != "" and cards.thought != "") as card_count',
		'"" as words' 
	))->SelectIdeas();
	foreach( $ideas as $key => $idea ){
		$stacks = $App->db->order('arrangement asc')->SelectCards(array('idea_id'=>$idea['id']));
		foreach( $stacks as $stack ){
			if( $stack['topic'] && $stack['thought'] ){
				$ideas[$key]['words'] .= " " . $stack['thought'];
			}
		}
	}
	$viewAbles = array_values(array_filter($ideas,function($idea){
		return $idea['status'];
	}));
	
	$archives = array_values(array_filter($ideas,function($idea){
		return $idea['status'] == 0;
	}));
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
		.archives{
			opacity:0;
		}
		.unbordered{
			font-family: 'Open Sans', sans-serif;
		}
		#blist{
			width: 80%;
		}
		#blist ul{
			line-height: 3em;
			padding-left:10px;
		}
		#blist ul>li{
			list-style-type: none;
		}
		h2.idea-titile{
			font-size:40px;
		}
		@media (max-width: 480px){
			.archives{
				position: initial !important;
				text-align: right;
			}
		}
	</style>
	<div class="container-fluid">
		<div class="top-links">
			<a href="<?=FOLDER?>dash/" data-toggle="tooltip" title="Dashboard" data-placement="bottom"><i class="fas fa-h-square"></i></a>
		</div>
		<div class="paper">
			<h2 class="idea-titile">Ideas...</h2>
			<textarea class="unbordered"></textarea>

			<div id="blist" class="text-left"></div>
			
		</div>
		<div class="archives">
			<a href="javascript:void(0);" id="showArchives">show/hide</a>
		</div>
	</div>
	
	<div id="archivesModal" class="modal fade" role="dialog">
	  <div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">All Ideas</h4>
		  </div>
		  <div class="modal-body">
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		</div>

	  </div>
	</div>
	
<?php include 'parts/footer.php';?>
<script type="text/javascript">
	
	_blist = {
		list : [],
		archives : [],
		defaultText: 'Type your idea here...',
		create:function( list ){
			var that = this;
			list = that.list.filter(function(l){
				return l.status == 1;
			});	
			var ul = '<ul>';
			for( x = 0 ; x < list.length; x++ ){
				ul += '<li class="ideas" data-iid="'+list[x].id+'">';
					ul += '<a href="card.php?iid='+list[x].id+'">'+list[x].idea+'</a>';
					if( list[x].card_count > 0 ){
						ul += '&nbsp;<a href="arrange.php?iid='+list[x].id+'" data-toggle="tooltip" title="Arrange"><i class="fa fa-list-alt" aria-hidden="true"></i></a>';
						ul += '&nbsp;<a href="stack.php?iid='+list[x].id+'" data-toggle="tooltip" title="Stack"><i class="fas fa-bars"></i></a>';
					}
					if( $.trim(list[x].words) != "" ){
						var wordCount = $.trim(list[x].words).replace(/\n/g , " ").replace(/\s+/g,' ').split(' ').length;
						ul += "&nbsp;<span style='color:#d2d2d2;font-size:10px;'>"+wordCount+"</span>";
					}
					
				ul += '</li>';
			}
			ul += '</ul>';
			return ul;
		},
		createFromArchives: function(){
			var that = this;
			var list =  that.list;			
			var chk = "";
			var unchk = "";
			var ul = '<ul class="list-group">';
			for( x = 0 ; x < list.length; x++ ){
				if( list[x].status==1 ){
					chk += '<li class="list-group-item">';
						chk += '<label>';						
							chk += '<input type="checkbox" class="isViewable" data-iid="'+list[x].id+'" '+(list[x].status==1?"checked":"")+' />&nbsp;';
							chk += '<a href="card.php?iid='+list[x].id+'">'+list[x].idea+'</a>';
							chk += ' <a class="totallyDeleteIdea" data-id='+list[x].id+'" data-toggle="tooltip" title="Delete" data-placement="top"><i class="fas fa-trash-alt"></i></a>';
						chk += '<label>';
					chk += '</li>';
				}else{
					unchk += '<li class="list-group-item">';
						unchk += '<label>';						
							unchk += '<input type="checkbox" class="isViewable" data-iid="'+list[x].id+'" '+(list[x].status==1?"checked":"")+' />&nbsp;';							
							unchk += '<a href="card.php?iid='+list[x].id+'">'+list[x].idea+'</a>';
							unchk += ' <a class="totallyDeleteIdea" data-id='+list[x].id+'" data-toggle="tooltip" title="Delete" data-placement="top"><i class="fas fa-trash-alt"></i></a>';
						unchk += '<label>';
					unchk += '</li>';

				}
			}
			ul += chk + unchk;
			ul += '</ul>';
			return ul;
		}
	};
	$('.unbordered').click(function(){
		var ito = $(this);
		if( $.trim(ito.val()) == '' ){
			$('.unbordered').attr('placeholder',"");
		}
	}).on('keypress',function(e){
		var ito = $(this);
		if( e.which == 13 && ito.val() && !ito.hasClass('sending') ){
			e.preventDefault();
			var idea = ito.val();
			ito.addClass('sending');
			ito.prop('disabled',true);
			$.ajax({
				url: 'request/index.php',
				type: 'post',
				dataType: 'json',
				data: {
					action: 'addIdea',
					idea: idea
				},
				success: function(ret){
					if( ret.status == 1 ){
						_blist.list.push(ret.idea);
						$('#blist').html(_blist.create());
						ito.val('');
						listSortable();
					}
					ito.prop('disabled',false);
					ito.removeClass('sending');
				},
				error: function(){
					
				}
			});
			
		}
		
	}).on('blur',function(){
		$(this).val('');
		if( $.trim($(this).val()) == '' ){
			$('.unbordered').attr('placeholder',_blist.defaultText);
		}
	});
	$('.unbordered').attr('placeholder',_blist.defaultText);
	
	_blist.list = <?=json_encode($ideas,true)?>;
	_blist.archives = <?=json_encode($archives)?>;
	
	$('#blist').html(_blist.create());
	if( $(window).width() <= 480 ){
		var mt = ($(window).height() / 2) - (($('.paper').height()+30)/2);
		mt = mt < 0 ? 15 : mt;
		$('.paper').css({
			'margin-top' : mt+'px'
		});
	}
	$('.paper,.archives').css({
		'opacity' : 1
	});
	listSortable();
	$('body').on('click','a.removeIdea',function(){
		var ito = $(this);
		if( confirm("Are you sure to remove this idea?") ){
			ito.prop('disabled',true);
			$.ajax({
				url: 'request/index.php',
				type: 'post',
				dataType: 'json',
				data: {
					action: 'removeIdea',
					id : ito.data('iid')
				},
				success: function(ret){
					if(ret.status == 1){
						ito.parent().remove();
						var idea = _blist.list.filter(function(i){
							return i.id == ito.data('iid');
						});						
						if( idea.length ){
							_blist.archives.push(idea[0]);
						}						
						_blist.list = _blist.list.filter(function(i){
							return i.id != ito.data('iid');
						});
					}
				},
				error: function(){
					
				}
			});
		}
	}).on('click','a.revertIdea',function(){
		var ito = $(this);
		if( confirm("This will revert back this Idea to the list.\nWould you like to continue?") ){
			ito.prop('disabled',true);
			$.ajax({
				url: 'request/index.php',
				type: 'post',
				dataType: 'json',
				data: {
					action: 'revertIdea',
					id : ito.data('iid')
				},
				success: function(ret){
					if(ret.status == 1){
						ito.parent().remove();
						var idea = _blist.archives.filter(function(i){
							return i.id == ito.data('iid');
						});						
						if( idea.length ){
							_blist.list.push(idea[0]);
						}						
						_blist.archives = _blist.archives.filter(function(i){
							return i.id != ito.data('iid');
						});
						$('#blist').html(_blist.create());
					}
				},
				error: function(){
					
				}
			});
		}
	}).on('change','.isViewable',function(){
		var ito = $(this);
		_blist.list.forEach(function(l){
			if( l.id == ito.data('iid') ){
				l.status = (ito.is(':checked') ? 1 : 0);
			}
		});
		$.ajax({
			url: 'request/index.php',
			type: 'post',
			dataType: 'json',
			data: {
				action: 'toggleArchived',
				id : ito.data('iid'),
				status : (ito.is(':checked') ? 1 : 0)
			},
			success: function(ret){
				$('#blist').html(_blist.create());
			},
			error: function(){
				
			}
		});
	}).on('click','a.totallyDeleteIdea',function(e){
		e.preventDefault();
		var ito = $(this);
		if( confirm("This Delete process cannot be revered.") ){
			$.ajax({
			url: 'request/index.php',
			type: 'post',
			dataType: 'json',
			data: {
				action: 'totallyDeleteIdea',
				id : ito.data('id')
			},
			success: function(ret){
				if(ret.status==1){
					window.location.reload();
				}else{
					alert("An error occured.");
				}
			},
			error: function(){
				alert("An error occured.");
			}
		});
		}
	});
	
	$('#showArchives').click(function(){
		var ito =  $(this);
		var modal = $('#archivesModal');
		
		modal.find('.modal-body').html(_blist.createFromArchives());
		modal.modal('show');
		
	});
	
	function listSortable(){
		try{
			$('#blist ul').sortable( "destroy" );
		}catch(e){
			
		}
		
		$('#blist ul').sortable({update: function(){
			var arr = [];
			$.each($('.ideas'),function(i,o){
				arr.push($(o).data('iid'));
			});
			$.ajax({
				url: 'request/index.php',
				type: 'post',
				dataType: 'json',
				data: {
					action: 'saveArrIdeas',
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
