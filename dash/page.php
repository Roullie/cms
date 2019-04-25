<?php include '../lib/common.php';?>
<?php 
	$info = $App->getPage( $App->Data->getVar('id') );
	$others = $App->otherPages();
	$categories = $App->getCategories();
	
	if( $App->Data->getVar('compid') ){
		$compilation = $App->db->SelectCompilations(array(
			'id' => $App->Data->getVar('compid')
		),true);
		if( $compilation ){
			$info['content'] = $compilation['compiled'];
		}
	}
?>
<?php include 'parts/header.php';?>
	<style>
		span.numCount{
			float:right;
		}
	</style>
	<div class="container-fluid">
	  <div class="row">
		<div class="col-sm-3 col-lg-2">
			<?php include 'parts/nav.php';?>				
		</div>
		<div class="col-sm-9 col-lg-10">
			<div class="">
				<h2 class="header-title">Page Information</h2>
				<form class="form-horizontal" id="pageForm">
					<?php if($info['id']){ ?>
						<input type="hidden" name="id" value="<?=$info['id']?>" />
					<?php }?>
					<div class="col-md-9">							
						<div class="form-group">
							<label class="control-label col-sm-2" for="title">Title</label>
							<div class="col-sm-10 ">
								<input type="text" class="form-control countChars" name="title" value="<?=$info['title']?>" />
								<span class="numCount"><?=strlen($info['title'])?> characters</span>
							</div>
						</div>
						
						<div class="form-group">
							<label class="control-label col-sm-2" for="url">URL</label>
							<div class="col-sm-10 ">
								<input type="text" class="form-control" name="url" value="<?=$info['url']?>" />
							</div>
						</div>	
						
						<div class="form-group">
							<label class="control-label col-sm-2" for="category_id">Category</label>
							<div class="col-sm-10 ">
								<select class="form-control" name="category_id">
									<option value="0">Uncategorized</option>
									<?php foreach($categories as $category){?>
										<option value="<?=$category['id']?>" <?=$info['category_id']==$category['id']?"selected":""?>><?=$category['name']?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						
						<div class="form-group">
							<label class="control-label col-sm-2" for="tag">Tags</label>
							<div class="col-sm-10 ">
								<input type="text" class="form-control" name="tags" value="<?=$info['tags']?>" />
							</div>
						</div>
						
						<div class="form-group">
							<label class="control-label col-sm-2" for="tagline">Tag Line</label>
							<div class="col-sm-10 ">
								<textarea class="form-control countChars" rows="3" name="tagline"><?=$info['tagline']?></textarea>
								<span class="numCount"><?=strlen($info['tagline'])?> characters</span>
							</div>
						</div>
						
						<div class="form-group">
							<label class="control-label col-sm-2" for="meta_title">Meta Title</label>
							<div class="col-sm-10 ">
								<input type="text" class="form-control" name="meta_title" value="<?=$info['meta_title']?>" />
							</div>
						</div>
						
						<div class="form-group">
							<label class="control-label col-sm-2" for="meta_description">Meta Description</label>
							<div class="col-sm-10 ">
								<textarea class="form-control" rows="3" name="meta_description"><?=$info['meta_description']?></textarea>
							</div>
						</div>
						
						<div class="form-group">
							<label class="control-label col-sm-2" for="meta_keywords">Meta Keywords</label>
							<div class="col-sm-10 ">
								<textarea class="form-control" rows="3" name="meta_keywords"><?=$info['meta_keywords']?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="show_home"></label>
							<label class="col-sm-10 ">
								<input type="checkbox" name="show_home" <?=$info['show_home']?"checked":""?> /> Show on Homepage
							</label>
						</div>
						
					</div>
					<div class="clearfix"></div>
					
					<div class="col-md-9">							
						<div class="">
							<h3 class="sidebar-titles">
								Content
							</h3>
							<textarea class="tinymce-area" name="content" id="page_content"><?=str_replace("<p><br>","<p>",$info['content'])?></textarea>
						</div>							
					</div>
					<div class="col-md-3">
					
						<div class="sidebar-box">
							<h3 class="sidebar-titles">
								Sidebar Custom
							</h3>
							<textarea class="form-control" style="height:250px;resize:none;" name="custom_sidebar"><?=$info['custom_sidebar']?></textarea>
						</div>
					
						<div class="sidebar-box">
							<h3 class="sidebar-titles">
								Sidebar Pages
							</h3>
							<div class="" style="height:250px;border: 1px solid #e4e4e4;overflow:auto;">
								<?php foreach($others as $other){?>
									<label class="pages-selection"><input type="checkbox" class="sidebar_links" name="sidebar_links[]" value="<?=$other['id']?>" <?=in_array($other['id'],$info['sidebar_links'])?"checked":""?>/> <?=$other['title']?></label>
								<?php } ?>
							</div>
						</div>
						
						
						
					</div>
					<div class="clearfix"></div>
					
					<div class="col-md-12">
						
						<h3 class="sidebar-titles">
							Custom Bottom
						</h3>
						<textarea class="tinymce-area" style="height:240px;resize:none;" id="custom_bottom" name="custom_bottom"><?=$info['custom_bottom']?></textarea>
						
					</div>
					<div class="clearfix"></div>
					
					<div class="text-center" style="padding:20px;">
						<button class="btn btn-primary" style="width:180px;" id="savePage">Save</button>
					</div>
					
				</form>
				
			</div>
			
		</div>
	  </div>
	</div>
<?php include 'parts/footer.php';?>
<script type="text/javascript">
	
	$('#savePage').click(function(e){
		e.preventDefault();
		
		var ito = $(this);
		var form = $('#pageForm');
		
		ito.prop('disabled',true);
		
		var PageData = {
			title: $('[name="title"]',form).val(),
			url: $('[name="url"]',form).val(),
			tags: $('[name="tags"]',form).val(),
			tagline: $('[name="tagline"]',form).val(),
			meta_description: $('[name="meta_description"]',form).val(),
			meta_keywords: $('[name="meta_keywords"]',form).val(),
			meta_title: $('[name="meta_title"]',form).val(),
			show_home: ($('[name="show_home"]',form).is(':checked') ? 1 : 0 ),
			category_id: $('[name="category_id"]',form).val(),
			custom_sidebar: $('[name="custom_sidebar"]',form).val(),
			custom_bottom: tinymce.get('custom_bottom').getContent(),
			content: tinymce.get('page_content').getContent(),
			sidebar_links: []
		};
		$('.sidebar_links:checked').each(function(i,o){
			PageData.sidebar_links.push($(o).val());
		});
		if( $('[name="id"]',form).length ){
			PageData.id = $('[name="id"]',form).val();
		}
		
		$.ajax({
			beforeSend: function(){
				console.log(tinymce.get('page_content').getContent());
			},
			url: 'request/index.php',
			type: 'post',
			dataType: 'json',
			data: {
				action: 'savePage',
				Page: PageData
			},
			success: function(ret){
				if( ret.status == 1 ){
					if( $('[name="id"]',form).length == 0 ){
						form.prepend('<input type="hidden" name="id" value="'+ret.Page.id+'" />');					
					}
					alert('Page Saved.');
					window.location.href = "page.php?id=" + ret.Page.id;
				}
			},
			error: function(req, textStatus, errorThrown) {
				//this is going to happen when you send something different from a 200 OK HTTP
				console.log(req);
				alert('An error occured: ' + textStatus + ' ' +errorThrown);
				ito.prop('disabled',false);
			}
		});
		
	});
	$('.countChars').on('keyup',function(){
		var ito = $(this);
		ito.next().text(ito.val().length + " characters");
	});
</script>
