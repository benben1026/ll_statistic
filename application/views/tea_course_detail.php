<input type="hidden" id="courseId" value="<?php echo $_GET['courseId']; ?>" />
<input type="hidden" id="platform" value="<?php echo $_GET['platform']; ?>" />
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">KEEPER Course Detail - <?php echo $course_name; ?></h1>
        </div>
    </div>
    <div class="row" style="margin-bottom: 10px;">
        <div class="col-lg-12">
            <form id="dateForm" class="form-inline">
            	<div class="form-group">
					<label for="date-from">From</label>
					<input type="text" class="form-control" id="date-from" style="height: 34px;">
				</div>
				<div class="form-group">
					<label for="date-to">To</label>
					<input type="text" class="form-control" id="date-to" style="height: 34px;">
				</div>
				<button type="submit" class="btn btn-default">Update</button>
        	</form>
        </div>
    </div>
    <script type="text/javascript">
    	var registerFunList = [];
    	$( "#date-from" ).datepicker({
	    	dateFormat: "yy-mm-dd",
	    	//defaultDate: +1
	    });
	    $( "#date-to" ).datepicker({
	    	dateFormat: "yy-mm-dd",
	    	//defaultDate: new Date()
	    });
	    $( "#date-from" ).datepicker("setDate", -30);
	    $( "#date-to" ).datepicker("setDate", new Date());

	    $('#dateForm').submit(function(e){
	    	e.preventDefault();
	    	for(var i = 0; i < registerFunList.length; i++){
	    		registerFunList[i]();
	    	}
	    })
    </script>
<?php
	include_once "charts/tea_course_detail/courseBasic.php";
	include_once "charts/tea_course_detail/courseStudentVitality.php";
	include_once "charts/tea_course_detail/courseEngagement.php";
	//include_once "charts/tea_course_detail/courseQuiz.php";
	//include_once "charts/tea_course_detail/fake_asg_dis.php";
	include_once "charts/tea_course_detail/courseFileView.php";
	include_once "charts/stu_course_detail/forum.php";
	include_once "charts/tea_course_detail/courseEnrollDrop.php";

?>
</div>