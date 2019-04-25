<?php include '../lib/common.php';?>
<?php 
	$App->redirecIfNotLogged();
	$id 	= !empty($_GET['iid']) && is_numeric( $_GET['iid'] ) ? $_GET['iid'] : ( !empty($_GET['cid']) && is_numeric( $_GET['cid'] ) ? $_GET['cid'] : 0 );
	$from	= !empty($_GET['iid']) && is_numeric( $_GET['iid'] ) ? "ideas" : ( !empty($_GET['cid']) && is_numeric( $_GET['cid'] ) ? "compilations" : "" );
	
	$t = array('txt'=>'');

	if( $from == 'ideas' ){
		
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
			return "## {$card['topic']}\n".
				"{$card['thought']}\n";
			
		},$cards));
		
		$t['txt'] = $text;
		
	}else{
		
		$params = array('id'=>$id);
		
		$comp = $App->db->SelectCompilations($params,true);
		
		$text = $comp['compiled'];
		$id = $comp['idea_id'];
		$comp_id = $id;
		
		$t['txt'] = $comp['compiled'];
		
	}
	$t['txt'] = str_replace("<br>","",$t['txt']);
?>
<?php $view = $App->setPageView();?>
<?php include 'parts/header.php';?>
	<link rel="stylesheet" href="../js/simplemde.min.css">
	<script src="../js/simplemde.min.js"></script>
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
		div.editor-preview.editor-preview-active h2{
			font-weight:bold;
			font-size:30px;
			padding:10px;
			margin:0;
		}
		div.editor-preview.editor-preview-active p{
			font-size: 18px;
			padding:10px;
			margin:0;
		}
	</style>
	<?php $view->generateStyles(2);?>
	<div class="container-fluid">
		<div class="top-links">
			<a href="<?=FOLDER?>dash/" data-toggle="tooltip" title="Dashboard" data-placement="bottom"><i class="fas fa-h-square"></i></a>
		</div>
		<br />
		<div class="col-md-8 col-center" style="max-width:900px">
			<textarea id="simple1"></textarea>
			<label style="display:none"><input type="radio" name="saveThis" checked /> Save this</label>
		</div>
		<div class="col-md-8"  style="display:none">
			<textarea id="simple2"></textarea>
			<label ><input type="radio" name="saveThis"  /> Save this</label>
		</div>
		<div class="clearfix"></div>
		
		
		<div id="stackLinks">
			<a class="toPage" data-compid="<?=$comp_id?>" data-toggle="tooltip" title="Create Page"><i class="fa fa-location-arrow" aria-hidden="true"></i></a>&nbsp;&nbsp;
			<a href="stack.php?iid=<?=$id?>" data-toggle="tooltip" title="Stack"><i class="fa fa-bars" aria-hidden="true"></i></a>
		</div>
		<div id="ideaLinks">
			<a href="ideas.php" data-toggle="tooltip" title="Ideas" data-placement="top"><i class="far fa-lightbulb"></i></a>
		</div>
	</div>
<?php include 'parts/footer.php';?>
<script type="text/javascript" src="../js/timeme.js"></script>
<script type="text/javascript">
	var sTxt = <?=json_encode($t)?>;
	function setValue(t,obj){
		var html = $('<div>'+t+'</div>');
		var txt = "";
		var isOrdered = false;
		var liCount = 1;
		$.each($('*',html),function(i,e){
			var tag = $(e).prop("tagName");
			
			if( tag[0] == "H" ){
				for( var ctr=0; ctr < parseInt(tag[1]); ctr++ ){
					txt += "#";
				}
				txt += " " + $(e).text() + "\n";
			}else if( tag == "P" ){
				txt += $(e).text() + "\n";
			}else if( tag == "IMG" ){
				txt += "![](" + $(e).attr('src') + ")\n";
			}else if( tag == "A" ){
				txt += "[](" + $(e).attr('src') + ")\n";
			}else if( tag == "UL" ){
				isOrdered = false;
				liCount = 1;
			}else if( tag == "OL" ){
				isOrdered = true;
				liCount = 1;
			}else if( tag == "LI" ){
				txt += (isOrdered ? liCount + ". " : "* ") + $(e).text() + "\n";
				if( isOrdered ){
					liCount = 1;
				}
			}

		});
		if( typeof(obj) != 'undefined' ){
			obj.value(txt);
		}else{
			return txt;
		}
	}
	
	var aTimeout,tTimeout;
	function saveCompilation( cb ){
		var form = $('#compilationsForm');
		var compiled = '';
		var whatIs = $('[name="saveThis"]:checked').parent().parent().find('textarea').attr('id');
		
		if( whatIs == 'simple1' ){
			var render = simplemde.value();		
			compiled = simplemde.options.previewRender(render.replace(/\n/g,'\n<br />'));
		}else{
			var render = simplemde2.value();
			compiled =  simplemde2.options.previewRender(render.replace(/\n/g,'\n<br />'));
		}
		
		var html = $('<div>'+compiled+'</div>');
		$('*',html).each(function(i,o){
			$(o).removeAttr('id');
			if( $(o).prop("tagName") == 'IMG' ){
				$(o).wrap(function() {
					return '<div style="text-align:center"></div>';
				});
			}
		});
		console.log(html);
		
		aTimeout = $.ajax({
			url: 'request/index.php',
			type: 'post',
			dataType: 'json',
			data: {
				action: "addCompilations",
				Compilation: {
					id : <?=$comp_id?>,
					idea_id : <?=$id?>,
					compiled: html.html()
				}
			},
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
	
	
	//simplemde.togglePreview(simplemde);
	// setValue( sTxt.txt , simplemde );
	$('#simple1').val( setValue( sTxt.txt) ) ;
	var simplemde = new SimpleMDE({ 
		element: document.getElementById("simple1") ,
		renderingConfig : {
			singleLineBreaks: false
		}
	});
	// detect event change save...
	simplemde.codemirror.on("change", function(){
		var render = simplemde.value();
		var renderedHTML = simplemde.options.previewRender(render.replace(/\n/g,'<br />'));
		saveCompilation();
	});
	simplemde.value(sTxt.txt);
	
	var simplemde2 = new SimpleMDE({ 
		element: document.getElementById("simple2") ,
		toolbar: false
	});
	
	// detect event change save...
	simplemde2.codemirror.on("change", function(){
		var render = simplemde2.value();
		var renderedHTML = simplemde2.options.previewRender(render);
		console.log(renderedHTML);
	});
	//simplemde2.togglePreview(simplemde2);
	
	TimeMe.initialize({
		currentPageName: "compilations", // current page (page title will be registered)
		idleTimeoutInSeconds: ( 10  ) 
	});	
	TimeMe.callWhenUserReturns(function(){
		var whatIs = $('[name="saveThis"]:checked').parent().parent().find('textarea').attr('id');
		
		if( whatIs == 'simple1' ){
			//simplemde.togglePreview(simplemde);
		}else{
			//simplemde2.togglePreview(simplemde2);
		}
	});
	TimeMe.callWhenUserLeaves(function(){
		var whatIs = $('[name="saveThis"]:checked').parent().parent().find('textarea').attr('id');
		
		if( whatIs == 'simple1' ){
			//simplemde.togglePreview(simplemde);
		}else{
			//simplemde2.togglePreview(simplemde2);
		}
	});
</script>
