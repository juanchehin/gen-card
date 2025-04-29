<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<?php 
if(isset($_GET['template'])){
	$qry = $conn->query("SELECT * FROM `id_format` where id = '{$_GET['template']}' ");
	foreach($qry->fetch_assoc() as $k => $v){
		$$k = $v;
	}
}
if(isset($_GET['id'])){
	$qry = $conn->query("SELECT * FROM `generated_id` where id = '{$_GET['id']}' ");
	foreach($qry->fetch_assoc() as $k => $v){
		$meta[$k] = $v;
	}
}
?>
<style>
	#id-card-field{
		width:2.5in;
		height:3.5in;
		position:relative;
		background:#fff;
	}
	#id-card-field .field-item{
		position:absolute;
		margin: 3px 5px;
	}
	#id-card-field .field-item.focus::before{
		content:"0";
		position:relative;
		width:100%;
		height:100%;
		border: 1px pink;
	}
	#id-card-field .field-item[data-type="textfield"]{
		padding:3px 5px;
	}
	#id-card-field .field-item.img{
		width:50px;
		height:50px;
	}
	#id-card-field .field-item[data-type="image"]{
		cursor:pointer;
	}
	#id-card-field .field-item>img{
		width:100%;
		height:100%;
		object-fit:fill;
		object-position:center center;
	}
	.remove_field{
		cursor:pointer;
	}

</style>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title"><?php echo isset($id) ? "Tu estas usando ".$name." Template": (isset($meta['id']) ? "Update ID" : "") ?></h3>
	</div>
	<div class="card-body">
		<form action="" id="generate-form">
			<input type="hidden" name="id" value="<?php echo isset($meta['id']) ? $meta['id'] : "" ?>">
			<input type="hidden" name="name" value="<?php echo isset($meta['name']) ? $meta['name'] : "" ?>">
			<input type="hidden" name="id_ss" value="<?php echo isset($meta['id_ss']) ? $meta['id_ss'] : "" ?>">
			<textarea name="id_script" id="id_script" class="d-none"></textarea>
		<div class="container-fluid">
			<?php if(isset($id)): ?>
				<div class="w-100 d-flex bg-dark justify-content-center py-5">
					<?php echo $template_code ?>
				</div>
			<?php elseif(isset($meta['id_script'])): ?>
				<div class="w-100 d-flex bg-dark justify-content-center py-5">
					<?php echo $meta['id_script'] ?>
				</div>
			<?php endif; ?>
		</div>
		</form>
		<input type="file" id="upload" data-id="" onchange="displayImg(this,$(this))" class="d-none">
	</div>
	<div class="card-footer">
		<button class="btn btn-info rounded-0" type="button" id="save-generated">Guardar</button>
		<a class="btn btn-dark rounded-0" href="./">Cancelar</a>
	</div>
</div>
<script>
	function displayImg(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
				var id = _this.attr('data-id')
				console.log(id)
	        	$('img[data-id="'+id+'"]').attr('src',e.target.result);
				_this.attr('data-id','')
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}
	$(document).ready(function(){
		$('#save-generated').click(function(){
			uni_modal("File Name","generate/id_name.php<?php echo isset($meta['name']) ? "?name=".$meta['name'] :"" ?>")
		})
		if('<?php echo isset($_GET["template"]) ?>' != 1 && '<?php echo isset($_GET["id"]) ?>' != 1 ){
			setTimeout(() => {
				uni_modal("SELECT TEMPLATE","generate/select_template.php")
			}, 250);
		}else{
			$('.field-item[data-type="textfield"]').each(function(){
				$(this).attr('contenteditable',true)
			})
			$('.field-item[data-type="image"]').each(function(){
				$(this).click(function(){
					$('#upload').attr('data-id',$(this).attr('id'))
					$('#upload').trigger('click')
				})
			})

		}

		$('#generate-form').submit(function(e){
		e.preventDefault();
		var _this = $(this)
		start_loader()
		var wait_until =  new Promise((resolve, reject) => {
				html2canvas(document.getElementById('id-card-field')).then(function(canvas) {
					// console.log(canvas.toDataURL('image/png'))
					$('[name="id_ss"]').val(canvas.toDataURL('image/png'))
				resolve();
					// document.getElementById('preview').appendChild(canvas);
				});
			});
		wait_until.then(function(){
		$.ajax({
			url:_base_url_+'classes/Master.php?f=save_generate',
			data: new FormData($('#generate-form')[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
		    dataType: 'json',
			error:err=>{
					console.log(err)
					alert_toast("Ocurrio un problema",'error');
					end_loader();
				},
			success:function(resp){
				if(typeof resp =='object' && resp.status == 'success'){
					var el = $('<div>')
					var img = $('<img>')
						img.attr('src',resp.generated_url)
						el.append(img)
					var nw = window.open("","_blank","width=1200,height=800")
						nw.document.write(el.html())
						nw.document.close()
						setTimeout(() => {
							nw.print()
							setTimeout(() => {
								nw.close()
								location.href = './?page=generate/list';
								end_loader();
							}, 200);
						}, 200);
					
				}else if(resp.status == 'failed' && !!resp.msg){
					var el = $('<div>')
						el.addClass("alert alert-danger err-msg").text(resp.msg)
						_this.prepend(el)
						el.show('slow')
						$("html, body").animate({ scrollTop: 0 }, "fast");
				}else{
					alert_toast("Ocurrio un problema",'error');
					console.log(resp)
				}
                end_loader()
			}
		})
	})
	})
		
	})
	
</script>