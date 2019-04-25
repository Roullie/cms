<?php include '../lib/common.php';?>
<?php 
	$id = $_GET['iid'];
	
	$params = array('idea_id'=>$id,'compiled'=>'');
	
	$hasBlank = $App->db->SelectCompilations($params,true);
	
	if( $hasBlank ){
		$comp_id = $hasBlank['id'];
	}else{
		$params['created'] = date('Y-m-d H:i:s');
		$comp_id = $App->db->InsertCompilations($params,true);
	}
	
	$cards = $App->db->order('arrangement asc')->SelectCards(array('idea_id'=>$id));
	
	
	$cards = array_values(array_filter($cards,function($card){
		return $card['topic'] && $card['thought'];
	}));

	$text = implode("",array_map(function($card){
		return "<p><span style='font-size:14px;font-family:Poppins, sans-serif;'>{$card['topic']}</span></p>".
			"<p><span style='font-size:12px;font-family:Muli, sans-serif;'>{$card['thought']}</span></p>";
		
	},$cards));
	
?>
<?php include 'parts/header.php';?>
	<link rel="stylesheet" href="../js/trumbow/ui/trumbowyg.css">
    <link rel="stylesheet" href="../js/trumbow/plugins/colors/ui/trumbowyg.colors.css">
	<link rel="stylesheet" href="../css/style.css" />
	<style>
		body{
			background:#f4f4f4;
		}
		.trumbow-container{
			width: 800px;
			margin: 70px auto 0 auto;
		}
		.trumbowyg-editor{
			background:#fff;
		}
		.trumbowyg-editor p,
		#compilationCompiled p{
			margin:0;
		}
		.card{
			margin:0 auto;
			width:700px;
		}
		.card.noPadding{
			padding:0;
		}
		.trumbowyg-box{
			margin-bottom:5px;
		}
	</style>
	<div class="container-fluid">
		<div class="trumbow-container">
			<div class="">
				<form id="compilationsForm">
					<input type="hidden" name="action" value="addCompilations" />
					<input type="hidden" name="Compilation[id]" value="<?=$comp_id?>" />
					<input type="hidden" name="Compilation[idea_id]" value="<?=$id?>" />
					<textarea class="unbordered" rows="15" name="Compilation[compiled]" style="display:none;"><?=$text?></textarea>
					<div class="text-right">
						<span><b>Words</b>: <span id="wCount"></span></span>&nbsp;&nbsp;&nbsp;
						<span><b>Lines</b>: <span id="lCount"></span></span>
					</div>
				</form>
			</div>
			
		</div>
		<div id="stackLinks">
			<a class="toPage" data-compid="<?=$comp_id?>"><i class="fa fa-location-arrow" aria-hidden="true"></i></a>&nbsp;&nbsp;
			<a href="stack.php?iid=<?=$id?>"><i class="fa fa-bars" aria-hidden="true"></i></a>
		</div>
		<div id="ideaLinks">
			<a href="ideas.php"><i class="fa fa-lightbulb-o" aria-hidden="true"></i></a>
		</div>
	</div>
<?php include 'parts/footer.php';?>
<script src="../js/trumbow/trumbowyg.js"></script>
<script src="../js/trumbow/plugins/base64/trumbowyg.base64.js"></script>
<script src="../js/trumbow/plugins/colors/trumbowyg.colors.js"></script>
<script src="../js/trumbow/plugins/noembed/trumbowyg.noembed.js"></script>
<script src="../js/trumbow/plugins/pasteimage/trumbowyg.pasteimage.js"></script>
<script src="../js/trumbow/plugins/upload/trumbowyg.upload.js"></script>
<script src="../js/trumbow/plugins/fontfamily/trumbowyg.fontfamily.js"></script>
<script src="../js/trumbow/plugins/fontsize/trumbowyg.fontsize.js"></script>
<script type="text/javascript">
	var aTimeout,tTimeout;
	function saveCompilation( cb ){
		var form = $('#compilationsForm');
		aTimeout = $.ajax({
			url: 'request/index.php',
			type: 'post',
			dataType: 'json',
			data: form.serialize(),
			success: function(ret){
				
				if( typeof(cb) == 'function' ){
					cb();
				}
				
			},
			error: function(){
				
			}
		});
	}
	$('a.toPage').click(function(){
		var ito = $(this);
		saveCompilation(function(){
			window.location.href = "page.php?compid=" + ito.data('compid');
		});
	});
	$('[name="Compilation[compiled]"]').trumbowyg({
		btns: [
			['viewHTML'],
			['p', 'blockquote', 'h1', 'h2', 'h3', 'h4'],
			['strong', 'em', 'underline', 'del'],
			['superscript', 'subscript'],
			['createLink', 'unlink'],
			['insertImage'],
			['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
			['unorderedList', 'orderedList'],
			['removeformat'],
			['fontsize'],
			['fontfamily'],

			['upload', 'base64', 'noembed'],
			['foreColor', 'backColor'],
			['preformatted'],
			['fullscreen']
		]
	}).on('tbwchange', function(){ 
		try{
			clearTimeout(tTimeout);
			aTimeout.abort();
			
		}catch(e){
			
		}
		
		$('#wCount').text(function(){
			return  $.trim($('<textarea>'+$("<div>"+$('[name="Compilation[compiled]"]').trumbowyg('html').replace(/\<\//g,' </')+"</div>").text()+"</textarea>").val()).replace(/\s+ /g,' ').split(' ').length;
		});
		
		$('#lCount').text(function(){
			return  $("<div>"+$('[name="Compilation[compiled]"]').trumbowyg('html').replace(/\<\//g,' </')+"</div>").find('p').length;
		});
		
		tTimeout = setTimeout(function(){saveCompilation();},100);
	});
	$('#wCount').text(function(){
		return $.trim($('<textarea>'+$("<div>"+$('[name="Compilation[compiled]"]').trumbowyg('html').replace(/\<\//g,' </')+"</div>").text()+"</textarea>").val()).replace(/\s+ /g,' ').split(' ').length;
	});		
	$('#lCount').text(function(){
			return  $("<div>"+$('[name="Compilation[compiled]"]').trumbowyg('html').replace(/\<\//g,' </')+"</div>").find('p').length;
		});
</script>
