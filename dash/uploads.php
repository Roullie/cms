<?php include '../lib/common.php';?>
<?php $App->redirecIfNotLogged(); ?>
<?php 
	$page = $App->Data->getVar('page') ? $App->Data->getVar('page') : 1;	
	$uploads = $App->db->limit(12,$page)->order("id desc")->SelectUploads();
	$pagination = $App->db->pgntion;
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
			opacity:0;
		}
		.btn-file span.input-file-text{
		}
		span.fname{
			display: inline-block;
			padding: 0 10px;
			font-family: monospace;
			font-size: 10px;
		}
		a.upload-item{
			height:200px;
			background:#e4e4e4;
			margin-bottom:15px;
			display:block;
		}
		a.upload-item img{
			width:100%;
		}
		div.img-cont{
			height:200px;
			background-size: contain;
			background-position: center center;
			background-repeat: no-repeat;
			position:relative;
		}
		div.fname{
			position:absolute;
			bottom:0;
			width:100%;
			padding:10px;
			background:rgba(0,0,0,0.3);
			color:#fff;
		}
		span.remove-image{
			position:absolute;
			top:0;
			right:0;
			padding: 4px 5px 1px 5px;
			color:#fff;
			background:rgba(0,0,0,0.3);
		}
		form#uploadsForm{
			float: none;
			margin: 0 auto;
			border: 1px solid #e4e4e4;
			padding: 15px;
			margin-bottom: 20px;
		}
		
		div#preview-image canvas{
			max-height:135px;
			margin-bottom:5px;
		}
		div.progress-line{
			background:#286090;
			height:3px;
			width:0%;
			margin-bottom:5px;
		}
	</style>
		<div class="container-fluid">
		  <div class="row">
			<div class="col-sm-3 col-lg-2">
				<?php include 'parts/nav.php';?>				
			</div>
			<div class="col-sm-9 col-lg-10">
				<div class="">
					<h2 class="header-title">Upload Images</h2>
				</div>
				
				<div class="">
				
					<form id="uploadsForm" method="post" enctype="multipart/form-data" style="max-width:50%;" onsubmit="return false;" class="col-md-8 col-center">
						<input type="hidden" name="action" value="addUploads" />
						<div class="form-group">
							<label>File Name</label>
							<input type="text" class="form-control" name="filename" value="" />
							<input type="hidden" name="blob" value="" />
						</div>
						<div class="form-group">
							<label>Max Width (in pixels)</label>
							<input type="number" class="form-control" min="10" name="maxwidth" step="1" id="image-width" value="585" />
						</div>
						<div class="form-group">
							<label>Quality</label>
							<input type="number" class="form-control" min="0" max="1" step=".1" id="image-quality" value="1" />
						</div>
						<div class="form-group">
							<label>File</label>
							<div id="preview-image"></div>
							<label class="btn btn-default btn-file btn-sm">
								<span class="input-file-hidden"><input type="file" name="image" id="file" accept="image/*"/></span>
								<span class="input-file-text">Browse</span>
							</label><span class="fname"></span>							
						</div>
						<div class="progress-line"></div>
						<div class="form-group text-center">
							<button class="btn btn-sm btn-primary" id="btn-upload">Upload</button>
						</div>
						<div class="clearfix"></div>
					</form>
					<div class="clearfix"></div>
					<hr />
					<?php foreach($uploads as $upload){?>
						<?php if( file_exists( UPLOADDIR . $upload['name'] ) || file_exists( IMAGESDIR . $upload['name'] ) ){?>
							<?php 
								$path = file_exists( UPLOADDIR . $upload['name'] ) ? UPLOADDIR . $upload['name'] : IMAGESDIR . $upload['name'] ;
								$url = file_exists( UPLOADDIR . $upload['name'] ) ? $App->home . 'uploads/' . $upload['name'] : $App->home . 'images/' . $upload['name'];
								$dimension = getimagesize($path);
								$size = filesize($path);
							?>
							<div class="col-md-3 ">
								<a class="upload-item" href="<?=str_replace(array(" "),array("%20"),$url);?>" target="_blank">
									<div class="img-cont" style="background-image: url(<?=str_replace(array(" "),array("%20"),$url);?>)">
										<span class="remove-image" data-id="<?=$upload['id']?>"><i class="fa fa-window-close" aria-hidden="true"></i></span>
										<div class="fname">
											<span><?=$upload['name']?></span><br />
											[<?=$dimension[0]?>x<?=$dimension[1]?>] [<?=number_format($size / 1024, 2) . ' KB';?>]
										</div>
									</div>
								</a>
							</div>
						<?php }?>
					<?php }?>
					<div class="clearfix"></div>
					<?php if($pagination['totalPages'] > 1){?>
						<div class="text-center">
							<ul class="pagination">
								<?php for($x = 1; $x < $pagination['totalPages']; $x++){?>
									<li class="<?=$x==$page?"active":""?>"><a href="?page=<?=$x?>"><?=$x?></a>
								<?php } ?>
							</ul>
						</div>
					<?php }?>
				</div>
				<br />
				<br />
				<br />
			</div>
		  </div>
		</div>
<?php include 'parts/footer.php';?>
<script type="text/javascript">
	$('#btn-upload').click(function(e){
		e.preventDefault();
		
		var ito = $(this);
		
		$('#uploadsForm').ajaxForm({
			beforeSend: function(){
				$('div.progress-line').width('0%');
				$('div.progress-line').show();
			},
			uploadProgress: function(event, position, total, percentComplete) {
				var percentVal = percentComplete + '%';
				$('div.progress-line').width(percentVal);
			},
			type: 'post',
			dataType: 'json',
			url : 'request/index.php',
			success: function(ret) {
				if( ret.status == 1 ){
					window.location.href = 'uploads.php';
				}else{
					alert(ret.msg);
				}
				ito.prop('disabled',false);
				
			},
			complete: function(xhr) {
				ito.prop('disabled',false);
				$('div.progress-line').hide();
			}
		}).submit();
		
	});
	$('body').on('change','input[type=file]',function(event){
		var ito = $(this);
		var name = ito.val().split('/').pop().split('\\').pop();
		ito.parent().parent().next().text(name);
		compress(event);
	});
	$('span.remove-image').click(function(e){
		e.preventDefault();
		var ito = $(this);
		if( confirm("Are you sure to remove this image file?") ){
			$.ajax({
				url: 'request/index.php',
				type: 'post',
				dataType: 'json',
				data: {
					action: 'removeUploads',
					id: ito.data('id')
				},
				success: function(ret){
					if( ret.status == 1 ){
						window.location.href = 'uploads.php';
					}
				},
				error: function(req, textStatus, errorThrown) {
				}
			});
			
		}
	});
	if (!HTMLCanvasElement.prototype.toBlob) {
	  Object.defineProperty(HTMLCanvasElement.prototype, 'toBlob', {
		value: function (callback, type, quality) {
		  var dataURL = this.toDataURL(type, quality).split(',')[1];
		  setTimeout(function() {
			var binStr = atob( dataURL ),
				len = binStr.length,
				arr = new Uint8Array(len);
			for (var i = 0; i < len; i++ ) {
			  arr[i] = binStr.charCodeAt(i);
			}
			callback( new Blob( [arr], {type: type || 'image/png'} ) );
		  });
		}
	  });
	}
	function compress(e) {
		const width = $('#image-width').val();
		const fileName = e.target.files[0].name;
		const reader = new FileReader();
		var mimeType = e.target.files[0].type;
		reader.readAsDataURL(e.target.files[0]);
		reader.onload = event => {
			const img = new Image();
			img.src = event.target.result;
			img.onload = () => {
					const scaleFactor = img.width < width ? 1 : (width / img.width);
					const elem = document.createElement('canvas');
					elem.width = img.width < width ? img.width : width;
					elem.height = img.height * scaleFactor;
					const ctx = elem.getContext('2d');
					ctx.drawImage(img, 0, 0, img.width < width ? img.width : width, img.height * scaleFactor);
					console.log(mimeType);
					if( mimeType != 'image/gif' ){
						ctx.canvas.toBlob((blob) => {
							const file = new File([blob], fileName, {
								type: mimeType,
								lastModified: Date.now()
							});
							var objUrl = URL.createObjectURL(file);						
							var input = document.createElement('input');
							input.type = 'file';
							input.files = createFileList(file);
							input.name = 'image';
							input.hidden = true;
							input.id = 'hidden_file_blob';
							$('span.input-file-hidden').html(input);
						}, mimeType, parseFloat($('#image-quality').val()));
					}					
					//console.log(elem);
					$('#preview-image').html(elem);					
					
				},
				reader.onerror = error => console.log(error);
		};
	}
	function createFileList(a) {
		a = [].slice.call(Array.isArray(a) ? a : arguments)
		for (var c, b = c = a.length, d = !0; b-- && d;) d = a[b] instanceof File
		if (!d) throw new TypeError('expected argument to FileList is File or array of File objects')
		for (b = (new ClipboardEvent('')).clipboardData || new DataTransfer; c--;) b.items.add(a[c])
		return b.files;
	}
</script>