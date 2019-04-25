<?php include '../lib/common.php';?>
<?php 
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
					<form>
						<h2 class="header-title">Navigation HTML Markup</h2>
						<input type="hidden" name="id" value="<?=$info['id']?>" />
						<textarea class="tinymce-area" name="navigation"><?=$info['navigation']?></textarea>
						<div class="text-center" style="padding:20px;">
							<button class="btn btn-primary btnSavePrt" style="width:180px;">Save</button>
						</div>
					</form>
				</div>
				
			</div>
		  </div>
		</div><script type="text/javascript">
	var content_css = ['<?=FOLDER?>css/dash-common.css'];
</script>
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
					navigation: tinymce.get('navigation').getContent(),
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