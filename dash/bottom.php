<?php include '../lib/common.php';?>
<?php 
	$App->redirecIfNotLogged();
	$App->Part->set();
	$info = $App->Part->get();
?>
<?php include 'parts/header.php';?>
		<div class="container-fluid">
		  <div class="row">
			<div class="col-sm-3 col-lg-2">
				<?php include 'parts/nav.php';?>
			</div>
			<div class="col-sm-9 col-lg-10">
				
				<div class="">
					<form class="formPrts">
						<h2 class="header-title">Bottom HTML Markup</h2>
						<input type="hidden" name="id" value="<?=$info['id']?>" />
						<textarea class="tinymce-area" name="bottom"><?=$info['bottom']?></textarea>
						<div class="text-center" style="padding:20px;">
							<button class="btn btn-primary btnSavePrt" style="width:180px;" id="save">Save</button>
						</div>
					</form>
				</div>
				
			</div>
		  </div>
		</div>
<?php include 'parts/footer.php';?>
<script type="text/javascript">
	$('.btnSavePrt').click(function(e){
		e.preventDefault();
		
		var ito = $(this);
		
		$.ajax({
			url: 'request/index.php',
			type: 'post',
			dataType: 'json',
			data: {
				action: 'savePart',
				Part: {
					bottom: tinymce.get('bottom').getContent(),
					id: $('input[name="id"]').val()
				}
			},
			success: function(ret){
				if( ret.status == 1 ){
					alert("Successfully Updated.");
				}
			},
			error: function(){
				
			}
		});
		
	});
</script>