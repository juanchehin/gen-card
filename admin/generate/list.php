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
		<h3 class="card-title">Lista de Tarjetas Generadas</h3>
		<div class="card-tools">
			<a href="?page=generate" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Crear Nuevo</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
        <div class="row row-cols-3 gx-3">
			<?php 
			$qry = $conn->query("SELECT * FROM `generated_id` order by `name` asc");
			while($row = $qry->fetch_assoc()):
			?>
				<div class="col p-1">
						<div class="card">
							<img src="<?php echo validate_image($row['image_path']) ?>" alt="" class="img-top img-template img-fluid">
							<div class="card-body">
								<a href="?page=generate/index&id=<?php echo $row['id'] ?>">
									<h5><?php echo $row['name'] ?></h5>
								</a>
								<div class="d-flex justify-content-end">
									<button class="badge badge-success border-0 py-1 px-2 print_id mr-2" type="button" data-path="<?php echo validate_image($row['image_path']) ?>"><i class="fa fa-print"></i></button>
									<a class="badge badge-primary border-0 py-1 px-2 download_id mr-2" href="<?php echo validate_image($row['image_path']) ?>" download data-id="<?php echo $row['id'] ?>"><i class="fa fa-download"></i></a>
									<button class="badge badge-danger border-0 py-1 px-2 delete_id" type="button" data-id="<?php echo $row['id'] ?>"><i class="fa fa-trash"></i></button>
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
		$('.delete_id').click(function(){
			_conf("Are you sure to delete this Generated ID permanently?","delete_id",[$(this).attr('data-id')])
		})
        $('.print_id').click(function(){
            var path = $(this).attr('data-path')
            var el = $('<div>')
					var img = $('<img>')
						img.attr('src',path)
						el.append(img)
					var nw = window.open("","_blank","width=1200,height=800")
						nw.document.write(el.html())
						nw.document.close()
						setTimeout(() => {
							nw.print()
							setTimeout(() => {
								nw.close()
								end_loader();
							}, 200);
						}, 200);
        })
		
	})
	function delete_id($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_id",
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