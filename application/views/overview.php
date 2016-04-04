<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">KEEPer Overview</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-tasks fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div id="moodle-course-num" class="huge">0</div>
                            <div>KEEP Moodle Course</div>
                        </div>
                    </div>
                </div>
                <a href="#enrolled-courses">
                    <div class="panel-footer">
                        <span class="pull-left">View Details</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
		</div>
		<div class="col-lg-3 col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-tasks fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div id="edx-course-num" class="huge">0</div>
                            <div>KEEP edX Course</div>
                        </div>
                    </div>
                </div>
                <a href="#enrolled-courses">
                    <div class="panel-footer">
                        <span class="pull-left">View Details</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-10">
			<div class="panel panel-default">
				<div class="panel-heading">
                    <i class="fa fa-bar-chart-o fa-fw"></i> Enrolled Courses List
                </div>
<?php
	include_once "charts/courseOverview.php";
?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$.ajax({
		url: '../courseInfo',
		type: 'GET',
		dataType: 'json',
		success: function(data){
			if(!data['ok']){
				console.log('fail to get course infomation');
				return;
			}
			$('#moodle-course-num').html(data['data']['moodle']['total_results']);
			$('#edx-course-num').html(data['data']['edx']['total_results']);
			for(var i = 0; i < data['data']['moodle']['total_results']; i++){
				$('#moodle-course-list').append($('<li><a href="../courseDetail?courseId=' + data['data']['moodle']['results'][i]['course_id'] + '">' + data['data']['moodle']['results'][i]['course_name'] + '</a></li>'))
			}
			for(var i = 0; i < data['data']['edx']['total_results']; i++){
				$('#edx-course-list').append($('<li><a href="../courseDetail?courseId=' + data['data']['edx']['results'][i]['course_id'] + '">' + data['data']['edx']['results'][i]['course_name'] + '</a></li>'))
			}
		},
		error: function(){

		}
	});
</script>