<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<style>
    .img-template{
        height:30vh;
		background:#000000b8;
        object-fit:scale-down;
        object-position:center center;
    }
	.delete_template{
		position:relative;
		z-index:2;
	}
</style>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">List of Templates</h3>
		<div class="card-tools">
			<a href="?page=templates/manage_template" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Create New Template</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
        <div class="row row-cols-3 gx-3">
			<?php 
			$qry = $conn->query("SELECT * FROM `id_format` order by `name` asc");
			while($row = $qry->fetch_assoc()):
			?>
				<div class="col p-1">
						<div class="card">
							<img src="<?php echo validate_image('uploads/templates/template_'.$row['id'].'.png') ?>" alt="" class="img-top img-template img-fluid">
							<div class="card-body">
								<a href="?page=templates/manage_template&id=<?php echo $row['id'] ?>">
									<h5><?php echo $row['name'] ?></h5>
								</a>
								<div class="d-flex justify-content-end">
									<button class="badge badge-danger border-0 py-1 px-2 delete_template" type="button" data-id="<?php echo $row['id'] ?>"><i class="fa fa-trash"></i></button>
								</div>
							</div>
						</div>
				</div>
			<?php endwhile; ?>
		</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('.delete_template').click(function(){
			_conf("Are you sure to delete this Template permanently?","delete_template",[$(this).attr('data-id')])
		})
		$('.reset_password').click(function(){
			_conf("You're about to reset the password of the user. Are you sure to continue this action?","reset_password",[$(this).attr('data-id')])
		})
		$('.table').dataTable();
	})
	function delete_template($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_template",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
	
</script>