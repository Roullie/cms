<?php include '../lib/common.php';?>
<?php $App->redirecIfNotLogged(); $styles = $App->db->SelectStyles(array('type'=>1)); ?>
<?php $previewstyles = $App->db->SelectStyles(array('type'=>2)); ?>
<?php $files = $App->db->SelectDeferfiles(array(),true); ?>
<?php
	$App->Part->set();
	$info = $App->Part->get();
?>
<?php include 'parts/header.php';?>
	<style>
		.btn-file{
			overflow:hidden;
			position:relative;
		}
		.btn-file input{
			position:absolute;
			top:0;
			right:0;
			font-size:40px;
		}
		.btn-file span{
			
		}
		.styleElement{
			margin-bottom:10px;
			border-top:1px solid #ccc;
			padding-top:10px;
		}
		.style .form-group label{
			padding-top:5px;
			font-weight:normal;
		}
		#cloneThis,.styleProgress,#cloneThisPreview{
			display:none;
		}
		input[name="name"]{
			font-weight:900;
		}
		h3.header-title{
			font-size:22px;
		}
		span.fname{
			padding-left: 10px;
			font-family: monospace;
			font-size: 11px;
			color: #555;
		}
	</style>
		<div class="container-fluid">
		  <div class="row">
			<div class="col-sm-3 col-lg-2">
				<?php include 'parts/nav.php';?>				
			</div>
			<div class="col-sm-9 col-lg-10">
				
				<div class="style">
					<h2 class="header-title">
						Styles
					</h2>
					<h3 class="header-title">
						Website Logo
					</h2>
					<form id="webLogoForm">
						<input type="hidden" name="action" value="changeLogoImage" />
						<img src="../uploads/<?=$info['logo']?>" alt="" />
						<label class="btn btn-default btn-file btn-sm"><input type="file" name="webLogo" accept="image/*"/><span>Browse</span></label><span class="fname"></span> <br /><br />
						<div class="progress styleProgress">
							<div class="progress-bar"></div>
						</div>
						<button class="btn btn-primary changeLogoButton">Change</button>
					</form>
					
					<h3 class="header-title">
						Defer Files
					</h2>
					<form id="deferForm">
						<input type="hidden" name="id" value="<?=$files['id']?>" />
						<input type="hidden" name="action" value="saveDefers" />
						<div class="col-md-6">
							<div class="form-group">
								<label>Scripts</label>
								<textarea class="form-control" name="script" style="height:100px;resize:none;"><?=$files['script']?></textarea>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Styles</label>
								<textarea class="form-control" name="style" style="height:100px;resize:none;"><?=$files['style']?></textarea>
							</div>
						</div>
						<div class="clearfix"></div>
						<div class="text-center">
							<div class="form-group">
								<button class="btn btn-primary saveDeferFiles">Save</button>
							</div>
						</div>
					</form>
					<h3 class="header-title">
						Compilation Preview Rules
						<a class="btn btn-success pull-right btn-sm addNewRule" data-target="#elementsPreview" data-clone="#cloneThisPreview">Add new Rule</a>
					</h2>
					<div id="elementsPreview">
						<div class="styleElement" id="cloneThisPreview">
							<form method="post" enctype="multipart/form-data" >
								<input type="hidden" name="action" value="saveElementStyle" />
								<input type="hidden" name="type" value="2" />
								<div class="col-md-6">
									<div class="form-group">
										<label>Name</label>
										<input type="text" class="form-control" name="name" placeholder="Name" />
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Target Element</label>
										<input type="text" class="form-control" name="target" placeholder="Tagname or ID or Class of element" />
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="col-md-4">Background Color</label>
										<div class="col-md-8">
											<input type="text" class="_colorpicker" name="backgroundColor" />
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="form-group">
										<label class="col-md-4">Font</label>
										<div class="col-md-8">
											<input type="text" class="form-control" name="font" placeholder="Open Sans" />
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="form-group">
										<label class="col-md-4">Font Color</label>
										<div class="col-md-8">
											<input type="text" class="_colorpicker" name="fontColor" />
										</div>
										<div class="clearfix"></div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="col-md-4">Background Image</label>
										<div class="col-md-8">
											<label class="btn btn-default btn-file btn-sm"><input type="file" name="backgroundImage" accept="image/*"/><span>Browse</span></label><span class="fname"></span>
											<label class="pull-right"><input type="checkbox" name="useImage"> Use Image</label>
										</div>
										<div class="clearfix"></div>
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="text-center">
									<div class="progress styleProgress">
										<div class="progress-bar"></div>
									</div>
									<div class="form-group">
										<button class="btn btn-primary saveStyle">Save</button>
										<button class="btn btn-danger removeStyle">Remove</button>
									</div>
								</div>
							</form>
						</div>
						
						<?php foreach($previewstyles as $style){?>
							<div class="styleElement">
								<form method="post" enctype="multipart/form-data" >
									<input type="hidden" name="action" value="updateElementStyle" />
									<input type="hidden" name="id" value="<?=$style['id']?>" />
									<div class="col-md-6">
										<div class="form-group">
											<label>Name</label>
											<input type="text" class="form-control" name="name" placeholder="Name" value="<?=$style['name']?>" />
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Target Element</label>
											<input type="text" class="form-control" name="target" placeholder="Tagname or ID or Class of element" value="<?=$style['target']?>"/>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label class="col-md-4">Background Color</label>
											<div class="col-md-8">
												<input type="text" class="colorpicker" name="backgroundColor"  value="<?=$style['backgroundColor']?>" />
											</div>
											<div class="clearfix"></div>
										</div>
										<div class="form-group">
											<label class="col-md-4">Font</label>
											<div class="col-md-8">
												<input type="text" class="form-control" name="font" placeholder="Open Sans"  value="<?=$style['font']?>" />
											</div>
											<div class="clearfix"></div>
										</div>
										<div class="form-group">
											<label class="col-md-4">Font Color</label>
											<div class="col-md-8">
												<input type="text" class="colorpicker" name="fontColor" value="<?=$style['fontColor']?>" />
											</div>
											<div class="clearfix"></div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label class="col-md-4">Background Image</label>
											<div class="col-md-8">
												<label class="btn btn-default btn-file btn-sm"><input type="file" name="backgroundImage" accept="image/*"/><span>Browse</span></label><span class="fname"></span>
												<label class="pull-right"><input type="checkbox" name="useImage" <?=$style['useImage']?"checked":""?>> Use Image</label>
											</div>
											<div class="clearfix"></div>
										</div>
										<?php if($style['backgroundImage']){?>
											<img src="<?=FOLDER?>uploads/<?=$style['backgroundImage']?>" alt="image" style="max-width:200px;max-height:75px;" />
										<?php } ?>
									</div>
									<div class="clearfix"></div>
									<div class="text-center">
										<div class="progress styleProgress">
											<div class="progress-bar"></div>
										</div>
										<div class="form-group">
											<button class="btn btn-primary saveStyle">Save</button>
											<button class="btn btn-danger removeStyle" data-id="<?=$style['id']?>">Remove</button>
										</div>
									</div>
								</form>
							</div>
						<?php } ?>
						
					</div>
					<h3 class="header-title">
						Page Rules
						<a class="btn btn-success pull-right btn-sm addNewRule" data-target="#elements" data-clone="#cloneThis">Add new Rule</a>
					</h2>	
					<div id="elements">
						<div class="styleElement" id="cloneThis">
							<form method="post" enctype="multipart/form-data" >
								<input type="hidden" name="action" value="saveElementStyle" />
								<input type="hidden" name="type" value="1" />
								<div class="col-md-6">
									<div class="form-group">
										<label>Name</label>
										<input type="text" class="form-control" name="name" placeholder="Name" />
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Target Element</label>
										<input type="text" class="form-control" name="target" placeholder="Tagname or ID or Class of element" />
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="col-md-4">Background Color</label>
										<div class="col-md-8">
											<input type="text" class="_colorpicker" name="backgroundColor" />
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="form-group">
										<label class="col-md-4">Font</label>
										<div class="col-md-8">
											<input type="text" class="form-control" name="font" placeholder="Open Sans" />
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="form-group">
										<label class="col-md-4">Font Color</label>
										<div class="col-md-8">
											<input type="text" class="_colorpicker" name="fontColor" />
										</div>
										<div class="clearfix"></div>
									</div>
									<div class="form-group">
										<label class="col-md-4">Font Size</label>
										<div class="col-md-8">
											<input type="text" class="form-control" name="fontSize" />
										</div>
										<div class="clearfix"></div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="col-md-4">Background Image</label>
										<div class="col-md-8">
											<label class="btn btn-default btn-file btn-sm"><input type="file" name="backgroundImage" accept="image/*"/><span>Browse</span></label><span class="fname"></span>
											<label class="pull-right"><input type="checkbox" name="useImage"> Use Image</label>
										</div>
										<div class="clearfix"></div>
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="text-center">
									<div class="progress styleProgress">
										<div class="progress-bar"></div>
									</div>
									<div class="form-group">
										<button class="btn btn-primary saveStyle">Save</button>
										<button class="btn btn-danger removeStyle">Remove</button>
									</div>
								</div>
							</form>
						</div>
						
						<?php foreach($styles as $style){?>
							<div class="styleElement">
								<form method="post" enctype="multipart/form-data" >
									<input type="hidden" name="action" value="updateElementStyle" />
									<input type="hidden" name="id" value="<?=$style['id']?>" />
									<div class="col-md-6">
										<div class="form-group">
											<label>Name</label>
											<input type="text" class="form-control" name="name" placeholder="Name" value="<?=$style['name']?>" />
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Target Element</label>
											<input type="text" class="form-control" name="target" placeholder="Tagname or ID or Class of element" value="<?=$style['target']?>"/>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label class="col-md-4">Background Color</label>
											<div class="col-md-8">
												<input type="text" class="colorpicker" name="backgroundColor"  value="<?=$style['backgroundColor']?>" />
											</div>
											<div class="clearfix"></div>
										</div>
										<div class="form-group">
											<label class="col-md-4">Font</label>
											<div class="col-md-8">
												<input type="text" class="form-control" name="font" placeholder="Open Sans"  value="<?=$style['font']?>" />
											</div>
											<div class="clearfix"></div>
										</div>
										<div class="form-group">
											<label class="col-md-4">Font Color</label>
											<div class="col-md-8">
												<input type="text" class="colorpicker" name="fontColor" value="<?=$style['fontColor']?>" />
											</div>
											<div class="clearfix"></div>
										</div>
										<div class="form-group">
											<label class="col-md-4">Font Size</label>
											<div class="col-md-8">
												<input type="text" class="form-control" name="fontSize" value="<?=$style['fontSize']?>" />
											</div>
											<div class="clearfix"></div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label class="col-md-4">Background Image</label>
											<div class="col-md-8">
												<label class="btn btn-default btn-file btn-sm"><input type="file" name="backgroundImage" accept="image/*"/><span>Browse</span></label><span class="fname"></span>
												<label class="pull-right"><input type="checkbox" name="useImage" <?=$style['useImage']?"checked":""?>> Use Image</label>
											</div>
											<div class="clearfix"></div>
										</div>
										<?php if($style['backgroundImage']){?>
											<img src="<?=FOLDER?>uploads/<?=$style['backgroundImage']?>" alt="image" style="max-width:200px;max-height:75px;" />
										<?php } ?>
									</div>
									<div class="clearfix"></div>
									<div class="text-center">
										<div class="progress styleProgress">
											<div class="progress-bar"></div>
										</div>
										<div class="form-group">
											<button class="btn btn-primary saveStyle">Save</button>
											<button class="btn btn-danger removeStyle" data-id="<?=$style['id']?>">Remove</button>
										</div>
									</div>
								</form>
							</div>
						<?php } ?>
						
					</div>
					
					
				</div>
				
			</div>
		  </div>
		</div>
<?php include 'parts/footer.php';?>
<script type="text/javascript">
	$('.addNewRule').click(function(){
		
		var ito = $(this);
		
		var clone = $(ito.data('clone')).clone();
		var id = '_ele'+(new Date().getTime());
		$(ito.data('target')).append('<div class="styleElement" id="'+id+'">'+clone.html()+'</div>');
		$('._colorpicker',$('#'+id)).each(function(i,o){
			$(o).spectrum({
				color: $(o).val(),
				preferredFormat: "hex",
				showInput: true,
				change: function(color) {
					$(o).val(color.toHexString());
				}
			});
		});
	});
	$('body').on('click','.saveStyle',function(e){
		e.preventDefault();
		var ito = $(this);
		var form = ito.parent().parent().parent();
		
		ito.prop('disabled',true);
		
		form.ajaxForm({
			type: 'post',
			dataType: 'json',
			url : 'request/index.php',
			beforeSend: function() {
				var percentVal = '0%';
				$('.progress-bar',form).width(percentVal);
				$('.progress',form).show();
				ito.prop('disabled',true);
			},
			uploadProgress: function(event, position, total, percentComplete) {
				var percentVal = percentComplete + '%';
				$('.progress-bar',form).width(percentVal);
			},
			success: function(ret) {
				if( ret.status == 1 ){
					alert("Style Saved.");	
					window.location.reload();					
				}
				ito.prop('disabled',false);
			},
			complete: function(xhr) {
				ito.prop('disabled',false);
			}
		}).submit();
		
	}).on('click','.removeStyle',function(e){
		e.preventDefault();
		var ito = $(this);
		
		if( confirm("Are you sure to remove this style?") ){
			if( typeof(ito.attr('data-id')) != 'undefined' ){
				ito.prop('disabled',true);
				$.ajax({
					url : 'request/index.php',
					type : 'post',
					dataType: 'json',
					data: {
						action: 'removeStyle',
						id : ito.attr('data-id')
					},
					success: function(){
						ito.parent().parent().parent().parent().remove();
					}
				});
			}else{
				ito.parent().parent().parent().parent().remove();
			}
		}
		
	}).on('change','input[type=file]',function(){
		var ito = $(this);
		var name = ito.val().split('/').pop().split('\\').pop();
		ito.parent().next().text(name);
	});			
	$('.saveDeferFiles').click(function(e){
		e.preventDefault();
		var ito = $(this);
		var form = $('#deferForm');
		ito.prop('disabled',true);
		$.ajax({
			url : 'request/index.php',
			type : 'post',
			dataType: 'json',
			data: form.serialize(),
			success: function(){
				alert("Successfully Saved.");
			}
		});
	});
	$('.changeLogoButton').click(function(e){
		e.preventDefault();
		var ito = $(this);
		var form = $('#webLogoForm');
		
		ito.prop('disabled',true);
		
		form.ajaxForm({
			type: 'post',
			dataType: 'json',
			url : 'request/index.php',
			beforeSend: function() {
				var percentVal = '0%';
				$('.progress-bar',form).width(percentVal);
				$('.progress',form).show();
				ito.prop('disabled',true);
			},
			uploadProgress: function(event, position, total, percentComplete) {
				var percentVal = percentComplete + '%';
				$('.progress-bar',form).width(percentVal);
			},
			success: function(ret) {
				if( ret.status == 1 ){
					alert("Logo Saved.");	
					window.location.reload();					
				}
				ito.prop('disabled',false);
			},
			complete: function(xhr) {
				ito.prop('disabled',false);
			}
		}).submit();
		
	});
	
</script>