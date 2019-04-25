<?php include '../lib/common.php';?>
<?php 
	$App->redirecIfNotLogged();
	$App->Part->set();
	$info = $App->Part->get();
	$offers = $App->db->SelectOffers();
	$sections = $App->db->SelectOffer_sections();
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
		span.fname{
			display: inline-block;
			padding: 0 10px;
			font-family: monospace;
			font-size: 10px;
		}
		#offer-item-image{
			height: 125px;
			background-position: center center;
			background-size: cover;
			background-repeat: no-repeat;
			margin-bottom:10px;
		}
		#editOfferModal div.modal-dialog{
			width:950px;
		}
	</style>
		<div class="container-fluid">
		  <div class="row">
			<div class="col-sm-3 col-lg-2">
				<?php include 'parts/nav.php';?>				
			</div>
			<div class="col-sm-9 col-lg-10">
				
				<div class="banner-form">
					<form>
						<h2 class="header-title">Offer Banner HTML Markup</h2>
						<label>
							<input type="checkbox" id="show_offer_banner" <?=$App->user['show_offer_banner']?"checked":""?>/> Show Banner
						</label>
						<input type="hidden" name="id" value="<?=$info['id']?>" />
						<textarea class="tinymce-area" id="offer_banner" name="offer_banner"><?=$info['offer_banner']?></textarea>
						<div class="text-center" style="padding:20px;">
							<button class="btn btn-primary btnSavePrt" style="width:180px;">Save</button>
						</div>
					</form>
				</div>
				
				<div class="">
					<h2 class="header-title">
						Offers
						<a class="btn btn-success pull-right btn-sm" data-toggle="modal" data-target="#addOfferModal">Add Offer Item</a>
					</h2>
				</div>
				
				<div class="">
					<label>
						<input type="checkbox" id="show_5_offer_hompage" <?=$App->user['show_5_offer_hompage']?"checked":""?>/> Show 5 offers on homepage
					</label>
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>Title</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($offers as $offer){?>
								<tr>
									<td><?=$offer['title']?></td>									
									<td class="text-center">
										<a class="editOffer" data-id="<?=$offer['id']?>">edit</a> | 
										<a class="removeOffer" data-id="<?=$offer['id']?>">remove</a> 
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
				
				<div class="">
					<h2 class="header-title">
						Sections
					</h2>
				</div>
				<ul class="ul-sections">
					<?php foreach($sections as $key => $section){?>
						<li>
							<form id="section-<?=$section['id']?>">
								<h4 class="header-title">Section <?=$key + 1?></h4>
								<label>
									<input type="checkbox" id="show_section<?=$section['id']?>" <?=$section['show_section']?"checked":""?> /> Show option
								</label>
								<input type="hidden" name="id" value="<?=$section['id']?>" />
								<textarea class="tinymce-area" id="tiny_<?=$section['id']?>"><?=$section['html']?></textarea>
								<div class="text-center" style="padding:20px;">
									<button class="btn btn-primary btnSaveSection" data-id="<?=$section['id']?>" style="width:180px;">Save</button>
								</div>
							</form>
						</li>
					<?php } ?>
				</ul>
			</div>
		  </div>
		</div>
		<div id="addOfferModal" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header modal-header-primary">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="">Add Offer Item</h4>
					</div>
					<div class="modal-body">
						<form id="addOffer" method="post" enctype="multipart/form-data">
							<input type="hidden" name="action" value="addOffer" />
							<div class="form-group">
								<label class="btn btn-default btn-file btn-sm"><input type="file" name="image" accept="image/*"/><span>Browse</span></label><span class="fname"></span>
							</div>
							<div class="form-group">
								<label>Title</label>
								<input class="form-control" name="Offer[title]" type="text" placeholder="Offer Title" />
							</div>
							<div class="form-group">
								<label>Tag Line</label>
								<textarea class="form-control" name="Offer[tagline]" rows="3"></textarea>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label>Before Price</label>
										<input class="form-control" name="Offer[before_price]" type="text" placeholder="Before Price" />
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>After Price</label>
										<input class="form-control" name="Offer[after_price]" type="text" placeholder="After Price" />
									</div>
								</div>
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-primary" id="btnAddOffer">Add</button>
					</div>
				</div>
			</div>
		</div>
		<div id="editOfferModal" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header modal-header-primary">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="">Edit Offer Item</h4>
					</div>
					<div class="modal-body">
						<form id="editOffer" method="post" enctype="multipart/form-data">
							<div class="row">
								<div class="col-md-6">
									<input type="hidden" name="action" value="editOffer" />
									<input type="hidden" name="Offer[id]" value="" />
									<div class="form-group">
										<div id="offer-item-image"></div>
										<label class="btn btn-default btn-file btn-sm"><input type="file" name="image" accept="image/*"/><span>Change</span></label><span class="fname"></span>
									</div>
									<div class="form-group">
										<label>URL</label>
										<input class="form-control" name="Offer[url]" type="text" placeholder="Offer URL" />
									</div>
									<div class="form-group">
										<label>Title</label>
										<input class="form-control" name="Offer[title]" type="text" placeholder="Offer Title" />
									</div>
									<div class="form-group">
										<label>Tag Line</label>
										<textarea class="form-control" name="Offer[tagline]" rows="3"></textarea>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label>Before Price</label>
												<input class="form-control" name="Offer[before_price]" type="text" placeholder="Before Price" />
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label>After Price</label>
												<input class="form-control" name="Offer[after_price]" type="text" placeholder="After Price" />
											</div>
										</div>
									</div>
									
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>In</label>
										<input class="form-control" name="Offer[topic_in]" type="text" placeholder="CSS | Development" />
									</div>
									<div class="form-group">
										<label>Time</label>
										<input class="form-control" name="Offer[time]" type="text" placeholder="25 hours" />
									</div>	
									<div class="form-group">
										<label>Hover Tag Line</label>
										<textarea class="form-control" name="Offer[hover_tagline]" rows="3"></textarea>
									</div>	
									<div class="form-group">
										<label>Bullet 1</label>
										<textarea class="form-control" name="Offer[bullet1]" rows="3"></textarea>
									</div>	
									<div class="form-group">
										<label>Bullet 2</label>
										<textarea class="form-control" name="Offer[bullet2]" rows="3"></textarea>
									</div>
									<div class="form-group">
										<label>Bullet 3</label>
										<textarea class="form-control" name="Offer[bullet3]" rows="3"></textarea>
									</div>
									
								</div>
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-primary" id="btnEditOffer">Edit</button>
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
					offer_banner: tinymce.get('offer_banner').getContent(),
					id: $('input[name="id"]').val()
				},
				show_offer_banner : (ito.is(':checked') ? 1 : 0)
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
	
	$('.btnSaveSection').click(function(e){
		e.preventDefault();
		
		var ito = $(this);
		
		$.ajax({
			url: 'request/index.php',
			type: 'post',
			dataType: 'json',
			data: {
				action: 'saveSection',
				Section: {
					html: tinymce.get('tiny_' + ito.data('id') ).getContent(),
					id: ito.data('id'),
					show_section : ($('#show_section'+ito.data('id')).is(':checked') ? 1 : 0)
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
	
	$('body').on('change','input[type=file]',function(){
		var ito = $(this);
		var name = ito.val().split('/').pop().split('\\').pop();
		ito.parent().next().text(name);
	});
	
	$('#btnAddOffer').click(function(){
		
		var ito = $(this);
		var modal = $('#addOfferModal');
		var form = $('#addOffer');
		
		ito.prop('disabled',true);
		form.ajaxForm({
			type: 'post',
			dataType: 'json',
			url : 'request/index.php',
			success: function(ret) {
				if( ret.status == 1 ){
					window.location.reload();
				}
				ito.prop('disabled',false);
			},
			complete: function(xhr) {
				ito.prop('disabled',false);
			}
		}).submit();
		
	});
	
	$('.editOffer').click(function(){
		var ito = $(this);
		var modal = $('#editOfferModal');
		$.ajax({
			url: 'request/index.php',
			type: 'post',
			dataType: 'json',
			data: {
				action: 'getOfferInfo',
				id : ito.data('id'),
				
			},
			success: function(ret){
				if( ret.status == 1 ){
					for(i in ret.Offer){
						if( $('[name="Offer['+i+']"]',modal).length ){
							$('[name="Offer['+i+']"]',modal).val(ret.Offer[i]);
						}
					}
					$('#offer-item-image').css({
						'background-image' : 'url(/cms/uploads/'+ret.Offer.image+')'
					});
					modal.modal('show');
				}
			},
			error: function(){
				
			}
		});
		
	});
	
	$('#btnEditOffer').click(function(){
		var ito = $(this);
		var modal = $('#editOfferModal');
		var form = $('#editOffer',modal);
		ito.prop('disabled',true);
		
		form.ajaxForm({
			type: 'post',
			dataType: 'json',
			url : 'request/index.php',
			success: function(ret) {
				if( ret.status == 1 ){
					window.location.reload();
				}
				ito.prop('disabled',false);
			},
			complete: function(xhr) {
				ito.prop('disabled',false);
			}
		}).submit();
		
	});
	
	$('.removeOffer').click(function(){
		var ito = $(this);
		if( confirm("Are you sure to remove this Item?") ){
			ito.prop('disabled',true);
			$.ajax({
				url : 'request/index.php',
				type : 'post',
				dataType: 'json',
				data: {
					action: 'removeOffer',
					id : ito.data('id')
				},
				success: function(){
					window.location.reload();
				}
			});
		}
	});
	
	$('#show_5_offer_hompage').on('change',function(){
		var ito = $(this);
		
		$.ajax({
			url : 'request/index.php',
			type : 'post',
			dataType: 'json',
			data: {
				action: 'show_5_offer_hompage',
				show_5_offer_hompage : (ito.is(':checked') ? 1 : 0)
			},
			success: function(){
				
			}
		});
		
	});
	
</script>