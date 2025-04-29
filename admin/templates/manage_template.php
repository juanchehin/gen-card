
<?php 
if(isset($_GET['id']) && $_GET['id'] > 0){
    $user = $conn->query("SELECT * FROM `id_format` where id ='{$_GET['id']}'");
    foreach($user->fetch_array() as $k =>$v){
        $$k = $v;
    }
}
?>
<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

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
	<div class="card-body">
		<div class="container-fluid">
			<form action="" id="template-form">
				<div class="row">
					<div class="col-4">
						<div id="msg"></div>
						<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
						<input type="hidden" name="template_image">
						<textarea name="template_code" class="d-none"></textarea>
						<div class="form-group">
							<label for="name" class="control-label">Template Name</label>
							<input type="text" name="name" id="name" class="form-control form-control-sm rounded-0" required value="<?php echo isset($name)? $name : "" ?>">
						</div>
						<div class="form-group border-bottom border-dark">
							<label for="" class="control-label">Base Size (Inches)</label>
							<div class="row mb-1">
								<label for="" class="control-label col-4">Height</label>
								<input type="number" setp="any" name="height" value="<?php echo isset($height)? $height : "3.5" ?>" class="form-control form-control-sm rounded-0 col-5">
							</div>
							<div class="row mb-1">
								<label for="" class="control-label col-4">Width</label>
								<input type="number" setp="any" name="width" value="<?php echo isset($width)? $width : "2.5" ?>" class="form-control form-control-sm rounded-0 col-5">
							</div>
						</div>
						<div class="form-group border-bottom border-dark mb-1 pb-1">
							<label for="" class="control-label">Background Image</label>
							<input type="file" name="img_src" onchange="displayImg(this,$(this))" class="form-control form-control-sm rounded-0">
						</div>
						<div class="form-group">
							<label for="" class="control-label">Field</label>
							<select id="select_field" class="custom-select custom-select-sm rounded-0">
								<option value="textfield">Text Field</option>
								<option value="image">Image</option>
							</select>
						</div>
						<div class="form-group">
							<button class="btn btn-sm btn-info rounded-0 p-1" id="add_field" type="button">Add Field</button>
						</div>
						<div id="field-form"></div>
					</div>
					<div class="col-8 d-flex justify-content-center bg-dark py-3 rounded align-items-center">
						<?php if(!isset($template_code)):?>
						<div id="id-card-field" class='border border-dark'>
							
						</div>
						<?php 
						else: 
							echo $template_code;
						?>
						<?php endif; ?>
					</div>
				</div>
			</form>
		</div>
		<?php if(isset($template_code)):
			echo '<script> $(function (){ data_func(); })</script>';
			endif; ?>

	</div>
	<div class="card-footer">
			<div class="col-md-12">
				<div class="row">
					<button class="btn btn-sm btn-primary mr-2" form="template-form">Guardar</button>
					<a class="btn btn-sm btn-secondary" href="./?page=templates">Cancelar</a>
				</div>
			</div>
		</div>
	<id id="preview"></id>
</div>
<style>
	img#cimg{
		height: 15vh;
		width: 15vh;
		object-fit: cover;
		border-radius: 100% 100%;
	}
</style>
<div id="align-text-clone" class="d-none">
	<select name="text_align" class="custom-select custom-select-sm">
		<option value="left">Left</option>
		<option value="right">Right</option>
		<option value="center">Center</option>
	</select>
</div>
<script>
	$(function(){
		$('[name="height"],[name="width"]').keyup(function(){
			var height = $('[name="height"]').val();
			var width = $('[name="width"]').val();
			$('#id-card-field').css({height:height+'in',width:width+'in'})
		})
	})
	function displayImg(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
				var _base64, type;
				var data = e.target.result
					data = data.split(';base64,')
	        	$('#id-card-field').css({'background': 'url('+(e.target.result)+')','background-repeat':'no-repeat','background-size':'cover'});
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}
	$('#template-form').submit(function(e){
		e.preventDefault();
		var _this = $(this)
		start_loader()
		$('#form-field').html('')
		var wait_until =  new Promise((resolve, reject) => {
			$('[name="template_code"]').val($('#id-card-field').parent().html())
				html2canvas(document.getElementById('id-card-field')).then(function(canvas) {
					// console.log(canvas.toDataURL('image/png'))
					$('[name="template_image"]').val(canvas.toDataURL('image/png'))
				resolve();
					// document.getElementById('preview').appendChild(canvas);
				});
			});
		wait_until.then(function(){

		$.ajax({
			url:_base_url_+'classes/Master.php?f=save_template',
			data: new FormData($('#template-form')[0]),
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
					location.href = './?page=templates';
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
	$(function(){
		$('.select2').select2()
		$('.select2-selection').addClass('form-control form-control-sm rounded-0 rounded-0')

		$('#add_field').click(function(){
			var _ft = $('#select_field').val()
			var _this = $(this)
				show_form(_ft,_this)
		})
	})
	function show_form(_ft,_this,__id = ''){
		if(_ft == 'textfield'){
			var id = (__id != "" ? __id :"TextField"+ ($('#id-card-field .field-item').length + 1))
			var fg = $("<div class='form-group pb-1 mb-1 border-bottom border-dark form-item' data-id='"+id+"'>")
			var _title = id;
			var input;
			fg.append("<label class='control-label'>"+_title+"<a class='badge badge-danger ml-2 remove_field' data-id='"+id+"'> Remove</a></label>")
			// Field ID NAME
			input = $("<div class='row'>")
			input.find('input').val(id)
			fg.append(input)
			// TExt
			input.append('<label class="col-4">Text</label><input class="form-control form-control-sm rounded-0 col-7" name="text_value" data-id="'+id+'"/>')
			input.append('<div class="clearfix col-12 mb-2"></div>')
			// Font Color
			input.append('<label class="col-4">Font Color</label><input class="form-control form-control-sm rounded-0 col-7 colorpicker1" name="font_color" data-id="'+id+'"/>')
			input.append('<div class="clearfix col-12 mb-2"></div>')
			// font style
			input.append('<label class="col-4">Font Style</label><div class="col-8"><label class="d-flex align-items-center"><input type="checkbox" class="mr-2" value="bold" name="style" data-id="'+id+'"/> <b>Bold</b></label><label class="d-flex align-items-center"><input type="checkbox" class="mr-2" value="italic" name="style" data-id="'+id+'"/> <i>Italic</i></label></div>')
			// width
			input.append('<label class="col-4">Width</label><input class="form-control form-control-sm rounded-0 col-4 " type="number" step="any" name="size[]" data-size="width" data-id="'+id+'"/><label class="col-4">(%)</label>')
			input.append('<div class="clearfix col-12 mb-2"></div>')

			// text-align
			var text_select = $('#align-text-clone').clone()
				text_select.find('select').attr('data-id',id)
				text_select.find('select').addClass('col-7')
			input.append('<label class="col-4">Align Text</label>'+text_select.html())
			input.append('<div class="clearfix col-12 mb-2"></div>')
			
			// Borders
			input.append('<label class="col-4">Border</label><div class="col-8"><label class="d-flex align-items-center"><input type="checkbox" class="mr-2" value="top" name="border" data-id="'+id+'"/> Top</label><label class="d-flex align-items-center"><input type="checkbox" class="mr-2" value="bottom" name="border" data-id="'+id+'"/> bottom</label><label class="d-flex align-items-center"><input type="checkbox" class="mr-2" value="left" name="border" data-id="'+id+'"/> Left</label><label class="d-flex align-items-center"><input type="checkbox" class="mr-2" value="right" name="border" data-id="'+id+'"/> Right</label></div>')
			// Border Color
			input.append('<label class="col-4">Borde Color</label><input class="form-control form-control-sm rounded-0 col-7 colorpicker1" name="border_color" data-id="'+id+'"/>')
			input.append('<div class="clearfix col-12 mb-2"></div>')
			// Element Position
			input.append('<label class="col-4">Position</label><input class="form-control form-control-sm rounded-0 col-4 " type="number" step="any" name="position[]" data-pos="top" data-id="'+id+'"/><label class="col-4">Top (%)</label>')
			input.append('<div class="clearfix col-12 mb-2"></div>')
			input.append('<label class="col-4"></label><input class="form-control form-control-sm rounded-0 col-4 " type="number" step="any" name="position[]" data-pos="left" data-id="'+id+'"/><label class="col-4">Left (%)</label>')

			fg.append(input)
			is_form_exists = $("#field-form .form-item[data-id='"+id+"']").length;
			if(__id == "" || (__id != "" && is_form_exists <= 0))
			$("#field-form").html(fg);
			if(__id != ""){
				$('[name="font_color"]').val(_this.css('color')).trigger('change')
				if(_this.css('font-weight') > 400)
				$('input[name="style"][value="bold"]').attr('checked',true)
				if(_this.css('font-style') == "italic")
				$('input[name="style"][value="italic"]').attr('checked',true)
				if(_this.css('border-top').includes("px solid") == true)
					$('input[name="border"][value="top"]').attr('checked',true)
				if(_this.css('border-bottom').includes("px solid") == true)
					$('input[name="border"][value="bottom"]').attr('checked',true)
				if(_this.css('border-left').includes("px solid") == true)
					$('input[name="border"][value="left"]').attr('checked',true)
				if(_this.css('border-right').includes("px solid") == true)
					$('input[name="border"][value="right"]').attr('checked',true)
				$('[name="border_color"]').val(_this.css('border-color')).trigger('change')
				if(_this.css('text-align') != "")
					$('[name="text_align"]').val(_this.css('text-align')).trigger('change')
				$('[name="text_value"]').val(_this.text())

				var parent = _this.parent()
					var pos = {};
					var nt ,nl;
					style =_this.attr('style')
					style = style.replace(/ /g,'')
					style = style.split(";")
					Object.keys(style).map(k=>{
						if(style[k] != ''){
							prop = style[k].split(':')
							prop1 = prop[0];
							prop2 = !!prop[1] ? prop[1] : '';
							pos[prop1] = prop2
						}
					})
					var left = !!pos.left ? (pos.left).replace("%",'') : 0;
					var top = !!pos.top ? (pos.top).replace("%",'') : 0;
					nt = top
					nl = left
					$('input[name="position[]"][data-pos="top"]').val(nt).trigger("change")
					$('input[name="position[]"][data-pos="left"]').val(nl).trigger("change")
			}

			// field Item
			var item = $('<div class="field-item" data-type="'+_ft+'">');
				item.attr('id', id)
				item.text(id)
			if(__id == ''){
				$('#id-card-field').append(item);
			}
			data_func();
		}else{
			var id = (__id != "" ? __id :"ImageField"+ ($('#id-card-field .field-item').length + 1))
			var fg = $("<div class='form-group pb-1 mb-1 border-bottom border-dark form-item' data-id='"+id+"'>")
			var _title = id;
			var input;
			fg.append("<label class='control-label'>"+_title+"</label>")
			// Field ID NAME
			input = $("<div class='row'>")
			input.find('input').val(id)
			fg.append(input)
			// File input
			input.append('<label class="col-4">Font Color</label><input type="file" class="form-control form-control-sm rounded-0 col-7" name="filename" data-id="'+id+'"/>')
			input.append('<div class="clearfix col-12 mb-2"></div>')
			// Element Size
			input.append('<label class="col-4">Image Size</label><input class="form-control form-control-sm rounded-0 col-4 " type="number" step="any" name="size[]" data-size="height" data-id="'+id+'"/><label class="col-4">Height (%)</label>')
			input.append('<div class="clearfix col-12 mb-2"></div>')
			input.append('<label class="col-4"></label><input class="form-control form-control-sm rounded-0 col-4 " type="number" step="any" name="size[]" data-size="width" data-id="'+id+'"/><label class="col-4">Width (%)</label>')
			input.append('<div class="clearfix col-12 mb-2"></div>')
			// Borders
			input.append('<label class="col-4">Border</label><div class="col-8"><label class="d-flex align-items-center"><input type="checkbox" class="mr-2" value="top" name="border" data-id="'+id+'"/> Top</label><label class="d-flex align-items-center"><input type="checkbox" class="mr-2" value="bottom" name="border" data-id="'+id+'"/> bottom</label><label class="d-flex align-items-center"><input type="checkbox" class="mr-2" value="left" name="border" data-id="'+id+'"/> Left</label><label class="d-flex align-items-center"><input type="checkbox" class="mr-2" value="right" name="border" data-id="'+id+'"/> Right</label></div>')
			// Border Color
			input.append('<label class="col-4">Borde Color</label><input class="form-control form-control-sm rounded-0 col-7 colorpicker1" name="border_color" data-id="'+id+'"/>')
			input.append('<div class="clearfix col-12 mb-2"></div>')
			// Element Position
			input.append('<label class="col-4">Position</label><input class="form-control form-control-sm rounded-0 col-4 " type="number" step="any" name="position[]" data-pos="top" data-id="'+id+'"/><label class="col-4">Top (%)</label>')
			input.append('<div class="clearfix col-12 mb-2"></div>')
			input.append('<label class="col-4"></label><input class="form-control form-control-sm rounded-0 col-4 " type="number" step="any" name="position[]" data-pos="left" data-id="'+id+'"/><label class="col-4">Left (%)</label>')

			fg.append(input)
			is_form_exists = $("#field-form .form-item[data-id='"+id+"']").length;
			if(__id == "" || (__id != "" && is_form_exists <= 0))
			$("#field-form").html(fg);
			if(__id != ""){
				$('[name="font_color"]').val(_this.css('color')).trigger('change')
				if(_this.css('font-weight') > 400)
				$('input[name="style"][value="bold"]').attr('checked',true)
				if(_this.css('font-style') == "italic")
				$('input[name="style"][value="italic"]').attr('checked',true)
				if(_this.css('border-top').includes("px solid") == true)
					$('input[name="border"][value="top"]').attr('checked',true)
				if(_this.css('border-bottom').includes("px solid") == true)
					$('input[name="border"][value="bottom"]').attr('checked',true)
				if(_this.css('border-left').includes("px solid") == true)
					$('input[name="border"][value="left"]').attr('checked',true)
				if(_this.css('border-right').includes("px solid") == true)
					$('input[name="border"][value="right"]').attr('checked',true)
				$('[name="border_color"]').val(_this.css('border-color')).trigger('change')
				



				var parent = _this.parent()
					var pos = {};
					var nt ,nl;
					style =_this.attr('style')
					if(style !== undefined ){
					style = style.replace(/ /g,'')
					style = style.split(";")
					Object.keys(style).map(k=>{
						if(style[k] != ''){
							prop = style[k].split(':')
							prop1 = prop[0];
							prop2 = !!prop[1] ? prop[1] : '';
							pos[prop1] = prop2
						}
					})
					var left = !!pos.left ? (pos.left).replace("%",'') : 0;
					var top = !!pos.top ? (pos.top).replace("%",'') : 0;
					var height = !!pos.height ? (pos.height).replace("%",'') : 0;
					var width = !!pos.width ? (pos.width).replace("%",'') : 0;
					nt = top
					nl = left
					$('input[name="position[]"][data-pos="top"]').val(nt).trigger("change")
					$('input[name="position[]"][data-pos="left"]').val(nl).trigger("change")
					$('input[name="size[]"][data-size="height"]').val(height).trigger("change")
					$('input[name="size[]"][data-size="width"]').val(width).trigger("change")
				}
			}

			// field Item
			var item = $('<div class="field-item img" data-type="'+_ft+'">');
				item.attr('id', id)
				item.append("<img  accept='image/*' data-id='"+id+"' src='<?php echo validate_image('') ?>'/>")
			if(__id == ''){
				$('#id-card-field').append(item);
			}
			data_func();
		}
	}
	function data_func(){
		$('.colorpicker1').colorpicker({format: 'hex'})
		$('[name="font_color"]').on('input change keyup keypress',function(){
			var el_id = $(this).attr('data-id');
			var color = $(this).val()
			$('#'+el_id).css({"color":color});
		})
		$('[name="border"]').change(function(){
			var pos = $(this).val()
			var el_id = $(this).attr('data-id');
			var _style = "border-"+pos;
			if($(this).is(":checked") == true){
				$('#'+el_id).css(_style,"1px solid");
			}else{
				$('#'+el_id).css(_style,"none");
			}
		})
		$('[name="style"]').change(function(){
			var val = $(this).val()
			var style = $(this).attr('name')
			var el_id = $(this).attr('data-id');
			if($(this).is(":checked") == true){
				if(val == 'bold')
					$('#'+el_id).css("font-weight","bolder");
				else
					$('#'+el_id).css("font-style","italic");
			}else{
				if(val == 'bold')
					$('#'+el_id).css("font-weight","unset");
				else
					$('#'+el_id).css("font-style","unset");
			}
		})
		$('[name="border_color"]').on('input change keyup keypress',function(){
			var el_id = $(this).attr('data-id');
			var color = $(this).val()
			$('#'+el_id).css("border-color",color);
		})
		$('[name="text_value"]').on('input change keyup keypress',function(){
			var el_id = $(this).attr('data-id');
			var txt = $(this).val()
			$('#'+el_id).text(txt);
		})
		$('[name="position[]"]').on('input keypress keyup change',function(){
			var el_id = $(this).attr('data-id');
			var pos = $(this).attr('data-pos')
			var val = $(this).val()
			$('#'+el_id).css(pos,val+"%");

		})
		$('[name="size[]"]').on('input keypress keyup change',function(){
			var el_id = $(this).attr('data-id');
			var pos = $(this).attr('data-size')
			var val = $(this).val()
			$('#'+el_id).css(pos,val+"%");

		})
		$('[name="text_align"]').change(function(){
			var el_id = $(this).attr('data-id');
			var val = $(this).val()
			$('#'+el_id).css('text-align',val);

		})
		$('[name="filename"]').change(function(){
			var id = $(this).attr('data-id')
			input = document.querySelector('input[name="filename"][data-id="'+id+'"]')
			if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
				var _base64, type;
				var data = e.target.result
					data = data.split(';base64,')
	        	$('img[data-id="'+id+'"]').attr("src",e.target.result);
	        }

				reader.readAsDataURL(input.files[0]);
			}
		})
		$('.remove_field').click(function(){
			var id = $(this).attr('data-id')
			$('.field-item#'+id).remove()
			$('#field-form').html('')
		}) 
		$('.field-item').on('mousedown',function(){
			var _ft = $(this).attr('data-type')
			var _this = $(this)
			show_form(_ft,_this,_this.attr('id'))
		if(_this.hasClass('ui-draggable') == false){
			_this.draggable({
				containment: "parent",
				stop: function( event, ui ) {
					var id = event.target.id 
					var parent = $('#'+id).parent()
					var p_height = parent.height()
					var p_width = parent.width()
					var pos = {};
					var nt ,nl;
					style =$('#'+id).attr('style')
					style = style.replace(/ /g,'')
					style = style.split(";")
					Object.keys(style).map(k=>{
						if(style[k] != ''){
							prop = style[k].split(':')
							prop1 = prop[0];
							prop2 = !!prop[1] ? prop[1] : '';
							pos[prop1] = prop2
						}
					})
					var left = !!pos.left ? (pos.left).replace("px",'') : 0;
					var top = !!pos.top ? (pos.top).replace("px",'') : 0;
					nt = ((parseFloat(top)/parseFloat(p_height)) * 100)
					nl = ((parseFloat(left)/parseFloat(p_width)) * 100)
					$('input[name="position[]"][data-pos="top"]').val(nt).trigger("change")
					$('input[name="position[]"][data-pos="left"]').val(nl).trigger("change")
				}
			})
		}
	})

	}



</script>