<?php include '../lib/common.php';?>
<?php
	$App->redirecIfNotLogged(); 
	$pages = $App->getPages();
	$pgntion = $App->db->pgntion;
	
	foreach( $pages as $key => $page ){
		$pages[$key]['stats']['info']['users'] = 0;
		$pages[$key]['stats']['info']['visits'] = 0;
	}
	
	$params = array_map(function($page) use($App){
		return array(
			"url" => urlencode($App->fixSource($page['url'],$App->home)),
			"title" => $page['title']
		);
	},$pages);	
	
	$info = get("https://libnik.com/statstrack/api/?action=urlInfoWithTitles&" . http_build_query(array('urltitles'=>$params)));
	$infos = (json_decode($info['html'],true));
	
	foreach( $pages as $key => $page ){
		$filtered = array_values(array_filter($infos['info'],function($inf) use($page,$App){
			$pageprsd = parse_url($App->fixSource($page['url'],$App->home));
			$infoprsd = parse_url($inf['url']);
			
			return $pageprsd['host'].$pageprsd['path'] 
				== $infoprsd['host'].$infoprsd['path'];
		}));
		
		if( $filtered ){
			$pages[$key]['stats']['info']['users'] = $filtered[0]['users'];
			$pages[$key]['stats']['info']['visits'] = $filtered[0]['visits'];
			$pages[$key]['stats']['info']['optin'] = $filtered[0]['optin'];
			$pages[$key]['stats']['info']['value'] = $filtered[0]['value'];
		}
	}
	
?>
<?php $pgntion 	= $App->db->pgntion;?>
<?php include 'parts/header.php';?>
	<style>
		#pageTable_info{display:none;}
		table.dataTable thead th, 
		.dataTables_wrapper.no-footer .dataTables_scrollBody{
			border-bottom:none;
		}
		.table-bordered>tbody>tr>td, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>td, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>thead>tr>th {
			border: 1px solid #ccc;
			border-top: none;
			border-left: none;
		}
		.table-bordered>tbody>tr>td:nth-child(1){
			border-left:1px solid #ccc;
		}
		table.dataTable.no-footer{border-bottom:0;}
	</style>
		<div class="container-fluid">
		  <div class="row">
			<div class="col-sm-3 col-lg-2">
				<?php include 'parts/nav.php';?>				
			</div>
			<div class="col-sm-9 col-lg-10">
				<div class="">
					<h2 class="header-title">
						Pages
						<a class="btn btn-success pull-right btn-sm" href="page.php">Add new Page</a>
					</h2>
				</div>
				<div class="">
					<table class="table table-bordered table-striped" id="pageTable">
						<thead>
							<tr>
								<th class="text-center">Title</th>
								<th class="text-center">Visits</th>
								<th class="text-center">Users</th>
								<th class="text-center">Optins</th>
								<th class="text-center">Value</th>
								<th class="text-center">Last Modified</th>
								<th class="text-center">Date Added</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($pages as $page){?>
								<tr>
									<td><a href="../<?=$page['url']?>&st=nt" target="_blank"><?=$page['title']?></a></td>
									<td class="text-center"><?=$page['stats']['info']['visits']?></td>
									<td class="text-center"><?=$page['stats']['info']['users']?></td>
									<td class="text-center"><?=$page['stats']['info']['optin']?></td>
									<td class="text-center"><?=$page['stats']['info']['value']?></td>
									<td class="text-center" data-sort="<?=strtotime($page['updated'])?>"><?=date('M d Y H:i',strtotime($page['updated']))?></td>
									<td class="text-center" data-sort="<?=strtotime($page['created'])?>"><?=date('M d Y H:i',strtotime($page['created']))?></td>
									<td class="text-center">
										<a href="page.php?id=<?=$page['id']?>">edit</a> | 
										<a class="removePage" data-id="<?=$page['id']?>">remove</a> 
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
					<div class="text-center">
						<?php $page = $pgntion['page']?>
						<ul class="pagination">
							<?php for($x=1;$x<=$pgntion['totalPages'];$x++){?>
								<li class="<?=$x==$page?"active":""?>"><a href="?page=<?=$x?>"><?=$x?></a></li>
							<?php } ?>
						</ul>
					</div>
				</div>
				
			</div>
		  </div>
		</div>
<?php include 'parts/footer.php';?>
<script type="text/javascript">
	$('.removePage').click(function(){
		var ito = $(this);
		
		if( confirm("Are you sure to remove this Page?") ){
			$.ajax({
				url: 'request/index.php',
				type: 'post',
				dataType: 'json',
				data: {
					action: 'removePage',
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
	var pageTable = $('#pageTable').DataTable({
		searching:false,
		paging:false,
		order: [[ 6, "desc" ]]
	});
</script>