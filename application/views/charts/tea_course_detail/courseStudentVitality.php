<div class="modal fade TeaStuView" tabindex="-1" role="dialog" aria-labelledby="TeaStuView">
	<div class="modal-dialog modal-lg" style="width: 90%;">
		<div class="modal-content" style="height: 700px">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="stuViewModalLabel">Student View</h4>
			</div>
			<div class="modal-body" style="height: 90%">
				<iframe id="teaViewStuFrame" style="width: 100%; height: 100%" frameBorder="0"></iframe>
			</div>
		</div>
	</div>
</div>
<button id="openModal" type="button" class="btn btn-primary" data-toggle="modal" data-target=".TeaStuView" style="display:none;">Large modal</button>
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

	function openTeaStuView(id, name){
		var url = "https://" + window.location.hostname + "/index.php/page/teaViewStu?courseId=" + $('#courseId').val() + "&platform=" + $('#platform').val() + "&keepId=" + id;
		$('#teaViewStuFrame').attr('src', url);
		$('#stuViewModalLabel').html(name + ' (Student View)');
		$('#openModal').click();

	}
</script>