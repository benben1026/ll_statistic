<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-green">
            <div class="panel-heading">
                <i class="fa fa-bar-chart-o fa-fw"></i>Student Vitality
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <table id="courseStudentVitality">
	                <thead>
	                	<tr>
	                		<th>Name</th><th>Viewed a Courseware</th><th>Watched a Video</th><th>Viewed a Thread</th><th>Created a Thread</th><th>Replied to a Thread</th><th>Voted a Thread</th><th>Completed a Problem</th><th>Total Score</th>
	                	</tr>
                	</thead>
                	<tbody>
                		
                	</tbody>
                </table>
            </div>
            <!-- /.panel-body -->
        </div>
	</div>
</div>

<script type="text/javascript">	
	var datatableStudentVitality = $('#courseStudentVitality').DataTable({
		"order": [[ 8, "desc" ]],
	});
	
	function getCourseStudentVitality(){
		datatableStudentVitality.clear().draw();
		datatableStudentVitality.ajax.url('../performance/stuVitality?courseId=' + $('#courseId').val() + '&platform=' + $('#platform').val() + '&type=view&from=' + $('#date-from').val() + '&to=' + $('#date-to')).load();
	}
	registerFunList.push(getCourseStudentVitality);
	getCourseStudentVitality();
</script>