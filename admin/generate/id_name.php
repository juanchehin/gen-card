<?php
require_once('../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT l.*,concat(u.lastname,' ',u.firstname,' ',u.middlename) as `name`,lt.code,lt.name as lname from `leave_applications` l inner join `users` u on l.user_id=u.id inner join `leave_types` lt on lt.id = l.leave_type_id  where l.id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
    $lt_qry = $conn->query("SELECT meta_value FROM `employee_meta` where user_id = '{$user_id}' and meta_field = 'employee_id' ");
    $employee_id = ($lt_qry->num_rows > 0) ? $lt_qry->fetch_array()['meta_value'] : "N/A";
}
?>
<style>
    p,label{
        margin-bottom:5px;
    }
    #uni_modal .modal-footer{
        display:none !important;
    }
    
</style>
<div class="container-fluid">
    <form action="" id="save-from">
            <div class="form-group">
                <label for="filename" class="control-label">File Name</label>
                <input type="text" required name="filename" pattern="[a-zA-Z0-9]+" id="filename" value="<?php echo isset($_GET['name']) ? $_GET['name'] : "" ?>" class="form-control">
            </div>
            <div class="form-group d-flex justify-content-end">
                    <button class="btn btn-info btn-sm round-0 mr-2">Save</button>
                    <button class="btn btn-dark btn-sm round-0" type="button" data-dismiss="modal">Cancel</button>
            </div>
    </form>
</div>
<script>
    $(function(){
         $('.select2').select2()
        $('#save-from').submit(function(e){
			e.preventDefault();
            $('[name="name"]').val($('#filename').val())
            $('#id_script').val($('#id-card-field').parent().html())
            $('#generate-form').submit();
            $('.modal').modal('hide')
		})
    })
</script>
