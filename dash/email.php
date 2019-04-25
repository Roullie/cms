<?php include '../lib/common.php';?>
<?php 
	$App->redirecIfNotLogged();
	$App->Part->set();
	$info = $App->Part->get();
?>
<?php include 'parts/header.php';?>
	<style>
		[name="Part[email_top]"],
		[name="Part[email_bottom]"],
		[name="generatedHTML"]{
			font-size:12px;
			font-family:monospace;
		}
	</style>
		<div class="container-fluid">
		  <div class="row">
			<div class="col-sm-3 col-lg-2">
				<?php include 'parts/nav.php';?>				
			</div>
			<div class="col-sm-9 col-lg-10">
				
				<div class="">
					
						<h2 class="header-title">Email Template</h2>
						<div class="text-left" style="padding:20px;">
							<a class="btn btn-success" data-toggle="modal" data-target="#topPartModal" style="width:180px;">Show Top Code</a>
							<a class="btn btn-success" data-toggle="modal" data-target="#bottomPartModal" style="width:180px;">Show Bottom Code</a>
						</div>
						<input type="hidden" name="id" value="<?=$info['id']?>" />
						<textarea class="form-control" id="email_content" name="content"></textarea>
						<div class="text-center" style="padding:20px;">
							<button class="btn btn-primary btnGenerate" style="width:180px;">Generate</button>
						</div>
					
				</div>
				
			</div>
		  </div>
		</div>
		<div id="topPartModal" class="modal fade" role="dialog">
		  <div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">
			  <div class="modal-body">
				<form>
					<input type="hidden" name="Part[id]" value="<?=$info['id']?>" />
					<input type="hidden" name="action" value="savePart" />
					<textarea class="form-control" name="Part[email_top]" rows="30"><?=$info['email_top']?></textarea>
					<div class="text-center" style="padding-top:20px;">
						<button class="btn btn-primary saveEmailPart" style="width:180px;">Save</button>
					</div>
				</form>
			  </div>
			</div>

		  </div>
		</div>
		<div id="bottomPartModal" class="modal fade" role="dialog">
		  <div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">
			  <div class="modal-body">
				<form>
					<input type="hidden" name="Part[id]" value="<?=$info['id']?>" />
					<input type="hidden" name="action" value="savePart" />
					<textarea class="form-control" name="Part[email_bottom]" rows="30"><?=$info['email_bottom']?></textarea>
					<div class="text-center" style="padding-top:20px;">
						<button class="btn btn-primary saveEmailPart" style="width:180px;">Save</button>
					</div>
				</form>
			  </div>
			</div>

		  </div>
		</div>
		
		<div id="generatedModal" class="modal fade" role="dialog">
		  <div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">
			  <div class="modal-body">
					<textarea class="form-control" name="generatedHTML" rows="30"></textarea>
					<div class="text-center" style="padding-top:20px;">
						<button class="btn btn-primary btnCopyGenerated" style="width:180px;">Copy</button>
					</div>
			  </div>
			</div>

		  </div>
		</div>
		
<?php include 'parts/footer.php';?>
<link rel="stylesheet" href="../js/simplemde.min.css">
<script src="../js/simplemde.min.js"></script>
<script type="text/javascript">
	var simplemde = new SimpleMDE({ element: document.getElementById("email_content") });
	
	$('.btnGenerate').click(function(e){
		e.preventDefault();
		var render = simplemde.value();
		var renderedHTML = simplemde.options.previewRender(render);
		var html = '';
		
		html += $('[name="Part[email_top]"]').val();
		
		var ihtml = $('<div>'+renderedHTML+'</div>');
		$('*',ihtml).each(function(i,o){
			$(o).removeAttr('id');
			if( $(o).prop("tagName") == 'IMG' ){
				$(o).wrap(function() {
					return '<div style="text-align:center"></div>';
				});
			}
		});
		
		html += ihtml.html();
		html += $('[name="Part[email_bottom]"]').val();
		
		$('[name="generatedHTML"]').val(html);
		
		$('#generatedModal').modal('show');
		
	});
	
	$('.saveEmailPart').click(function(e){
		e.preventDefault();
		var ito = $(this);
		var form = ito.parent().parent();
		ito.prop('disabled',true);
		$.ajax({
			url: 'request/index.php',
			type: 'post',
			dataType: 'json',
			data: form.serialize(),
			success: function(ret){
				if( ret.status == 1 ){
					ito.prop('disabled',false);
					$('#bottomPartModal,#topPartModal').modal('hide');
				}else{
					alert("An error occured.  Please try again.");
				}
			}
		});
	});
	
	$('.btnCopyGenerated').click(function(){
		$('[name="generatedHTML"]').select();
		document.execCommand('copy');
		alert('Copied to Clpiboard');
	});
	
</script>