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
					<form>
						<h2 class="header-title">Global Codes</h2>
						<input type="hidden" name="id" value="<?=$info['id']?>" />
						<div class="form-group">
							<label>Head</label>
							<textarea class="form-control" rows="15" name="global_head"><?=$info['global_head']?></textarea>
						</div>
						<div class="form-group">
							<label>Body</label>
							<textarea class="form-control" rows="15" name="global_body"><?=$info['global_body']?></textarea>
						</div>
						<div class="text-center" style="padding:20px;">
							<button class="btn btn-primary btnSavePrt" style="width:180px;">Save</button>
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
					global_head: $('[name="global_head"]').val(),
					global_body: $('[name="global_body"]').val(),
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