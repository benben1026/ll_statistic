<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">KEEPER Course Detail - <?php echo $course_name; ?></h1>
    </div>
</div>
<div class="row" style="margin-bottom: 10px;">
    <div class="col-lg-12">
        <form id="modelDateForm" class="form-inline">
        	<div class="form-group">
				<label for="model-date-from">From</label>
				<input type="text" class="form-control" id="model-date-from" style="height: 34px;">
			</div>
			<div class="form-group">
				<label for="model-date-to">To</label>
				<input type="text" class="form-control" id="model-date-to" style="height: 34px;">
			</div>
			<button type="submit" class="btn btn-default">Update</button>
    	</form>
    </div>
</div>
<script type="text/javascript">
	var modelRegisterFunList = [];
	$( "#model-date-from" ).datepicker({
    	dateFormat: "yy-mm-dd",
    	//defaultDate: +1
    });
    $( "#model-date-to" ).datepicker({
    	dateFormat: "yy-mm-dd",
    	//defaultDate: new Date()
    });
    $( "#model-date-from" ).datepicker("setDate", -30);
    $( "#model-date-to" ).datepicker("setDate", new Date());

    $('#modelDateForm').submit(function(e){
    	e.preventDefault();
    	for(var i = 0; i < modelRegisterFunList.length; i++){
    		modelRegisterFunList[i]();
    	}
    })
</script>