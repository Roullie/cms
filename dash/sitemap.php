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
					<form id="exclude_form">
						<input type="hidden" name="action" value="saveExcludedUrls" />
						<h2 class="header-title">Exclude these pages in Sitemap</h2>
						<textarea class="form-control" name="exclude_sitemaps" rows="20"><?=$App->user['exclude_sitemaps']?></textarea>
						<div class="text-center" style="padding:20px;">
							<button class="btn btn-primary btnSaveExcludeUrls" style="width:180px;">Save</button>
						</div>
					</form>
				</div>
				
			</div>
		  </div>
		</div>
<?php include 'parts/footer.php';?>
<script type="text/javascript">
	$('.btnSaveExcludeUrls').click(function(e){
		e.preventDefault();
		
		var ito = $(this);
		var form = $('#exclude_form');
		
		$.ajax({
			url: 'request/index.php',
			type: 'post',
			dataType: 'json',
			data: form.serialize(),
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