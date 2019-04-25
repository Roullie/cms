<?php include '../lib/common.php';?>
<?php $App->redirecIfNotLogged(); $categories = $App->getCategories();?>
<?php $pgntion 	= $App->db->pgntion;?>
<?php include 'parts/header.php';?>
		<div class="container-fluid">
		  <div class="row">
			<div class="col-sm-3 col-lg-2">
				<?php include 'parts/nav.php';?>				
			</div>
			<div class="col-sm-9 col-lg-10">
				<div class="">
					<h2 class="header-title">
						Categories
						<a class="btn btn-success pull-right btn-sm" data-toggle="modal" data-target="#addCategoryModal">Add new Category</a>
					</h2>
				</div>
				<div class="">
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>Name</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($categories as $category){?>
								<tr>
									<td><a href="../c/<?=$category['name']?>" target="_blank"><?=$category['name']?></a></td>
									<td class="text-center">
										<a class="editCategory" data-id="<?=$category['id']?>">edit</a> | 
										<a class="removeCategory" data-id="<?=$category['id']?>">remove</a> 
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
					<div class="text-center" id="paginationContainer">
						<ul class="pagination">
							<?php for( $x = 1; $x <= $pgntion['totalPages']; $x++ ){?>
								<li class="<?=$x==$pgntion['page']?"active":""?>" >
									<a class="pagination-link" href="category.php?page=<?=$x?>" data-page=""><?=$x?></a>
								</li>
							<?php } ?>
						</ul>
					</div>
				</div>
				
			</div>
		  </div>
		</div>
		
		<div id="addCategoryModal" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header modal-header-primary">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="">Add Category</h4>
					</div>
					<div class="modal-body">
						<form id="addCategory">
							<input type="hidden" name="action" value="addCategory" />
							<input class="form-control" name="Category[name]" type="text" placeholder="Category Name" />
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-primary" id="btnAddCategory">Add</button>
					</div>
				</div>
			</div>
		</div>
		
		<div id="editCategoryModal" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header modal-header-primary">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="">Edit Category</h4>
					</div>
					<div class="modal-body">
						<form id="editCategoryForm">
							<div class="form-group">
								<input type="hidden" name="action" value="editCategory" />
								<input type="hidden" name="Category[id]" value="" />
								<label>Category Name</label>
								<input class="form-control" name="Category[name]" type="text" placeholder="Category Name" />
							</div>
							<div class="form-group">
								<label>
									<input type="checkbox" name="Category[show_banner]" /> Show Category Banner
								</label>
							</div>
							<div class="form-group">
								<input type="hidden" name="action" value="addCategory" />
								<label>Category Banner</label>
								<textarea class="tinymce-area" name="Category[banner]" id="bannerCode"></textarea>
							</div>
							
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-primary" id="btnEditCategory">Save</button>
					</div>
				</div>
			</div>
		</div>
		
<?php include 'parts/footer.php';?>
<script type="text/javascript">
	$('#btnAddCategory').click(function(){
		var ito = $(this);
		var form = $('#addCategory');
		
		if( $.trim( $('[name="Category[name]"]',form).val() ) ){
			$.ajax({
				url: 'request/index.php',
				type: 'post',
				dataType: 'json',
				data: form.serialize(),
				success: function(ret){
					if( ret.status == 1 ){
						window.location.reload();
					}
				},
				error: function(){
					
				}
			});
		}
		
	});
	$('#btnEditCategory').click(function(){
		var ito = $(this);
		var modal = $('#editCategoryModal');
		var form = $('#editCategoryForm',modal);
		
		if( $.trim( $('[name="Category[name]"]',form).val() ) ){
			$.ajax({
				url: 'request/index.php',
				type: 'post',
				dataType: 'json',
				data: {
					action: 'editCategory',
					Category : {
						id : $('[name="Category[id]"]',form).val(),
						name: $('[name="Category[name]"]',form).val(),
						show_banner: ($('[name="Category[show_banner]"]',form).is(':checked') ? 1 : 0),
						banner : tinymce.get('bannerCode').getContent(),
					}
				},
				success: function(ret){
					if( ret.status == 1 ){
						alert("Category Saved.");
						modal.modal('hide');
					}
				},
				error: function(){
					
				}
			});
		}
		
	});
	$('.editCategory').click(function(){
		var ito = $(this);
		var modal = $('#editCategoryModal');
		var form = $('#editCategoryForm',modal);
		
		$.ajax({
			url: 'request/index.php',
			type: 'post',
			dataType: 'json',
			data: {
				action: 'getCategoryInfo',
				id : ito.data('id')
			},
			success: function(ret){
				if( ret.status == 1 ){
					
					$('[name="Category[name]"]',form).val(ret.Category.name);
					$('[name="Category[id]"]',form).val(ret.Category.id);
					tinymce.get('bannerCode').setContent(ret.Category.banner);
					
					modal.modal('show');
					
				}
			},
			error: function(){
				
			}
		});
		
	});
	$('.removeCategory').click(function(){
		var ito = $(this);		
		if( confirm("Are you sure to remove this Category?") ){
			$.ajax({
				url: 'request/index.php',
				type: 'post',
				dataType: 'json',
				data: {
					action: 'removeCategory',
					id: ito.data('id')
				},
				success: function(ret){
					if( ret.status == 1 ){
						window.location.reload();
					}
				},
				error: function(){
					
				}
			});
		}		
	});
</script>