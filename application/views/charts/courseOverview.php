<div class="row">
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-tasks fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div id="moodle-course-num" class="huge"><img class="course-overview-loading" src="<?= base_url() ?>public/resource/loading3.gif" style="width:50px;"></div>
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
                        <div id="edx-course-num" class="huge"><img class="course-overview-loading" src="<?= base_url() ?>public/resource/loading3.gif" style="width:50px;"></div>
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

<input type="hidden" id="role" value="<?php echo $role == 'student' ? 'student' : 'teacher'; ?>" >
<div class="row">
	<div class="col-lg-10">
		<div class="panel panel-green">
			<div class="panel-heading">
                <i class="fa fa-bar-chart-o fa-fw"></i> Enrolled Courses List
            </div>
			<div style="margin: 30px;">
				<table id="enrolled-courses">
					<thead>
						<tr>
							<th>No.</th><th>Course Name</th><th>Platform</th><th>Role</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
			<div id="platform-distribution" style="height: 200px; margin: 0px 30px 30px 30px; display:none;"></div>
			<img id="platform-distribution-loading" src="<?= base_url() ?>public/resource/loadbar.gif" style="width: 50%; height: 15%; margin-left: 25%;">
		</div>
	</div>
</div>
<script type="text/javascript">
	var role = $('#role').val();
	var numOfCourseAccToRole = {};

	function get_courses_list(){
		$.ajax({
			url: '../course/courseList/',
			type: 'GET',
			dataType: 'json',
			success: function(data){
				if(data['ok']){
					render_enrolled_courses_table(data['data']);
					$('#moodle-course-num').html(numOfCourseAccToRole['moodle']);
					$('#edx-course-num').html(numOfCourseAccToRole['edx']);
					$('#platform-distribution').show();
					drawCourseInPlatform(data['data']);
					$('.course-overview-loading').parent().html('0');
					$('#platform-distribution-loading').hide();
				}
			},
			error: function(){

			}
		});
	}

	function render_enrolled_courses_table(data){
		var i = 1;
		var table = $('#enrolled-courses tbody');
		for(var lrs in data){
			var num = data[lrs]['total_results'];
			numOfCourseAccToRole[lrs] = 0;
			for(var $j = 0; $j < num; $j++){
				if(role == 'student'){
					if(data[lrs]['results'][$j]['role_name'] != role){
						continue;
					}
				}else{
					if(data[lrs]['results'][$j]['role_name'] == 'student'){
						continue;
					}
				}
				table.append($('<tr><td>' + i + '</td><td><a href="courseDetail?courseId=' + data[lrs]['results'][$j]['course_id'] + '&platform=' + lrs + '">' + data[lrs]['results'][$j]['course_name'] + '</a></td><td>' + lrs + '</td><td>' + data[lrs]['results'][$j]['role_name'] + '</td></tr>'));
				i++;
				numOfCourseAccToRole[lrs]++;
			}
		}
		$('#enrolled-courses').DataTable();
	}

	function drawCourseInPlatform(data){
		var yData = [];
		var xData = [];
		for(var lrs in data){
			yData.push(lrs);
			xData.push(numOfCourseAccToRole[lrs]);
		}

	    var myChart = echarts.init(document.getElementById('platform-distribution'));
	    var labelRight = {normal: {label : {position: 'right'}}};
	    var d = new Date();
	    var month = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
	    option = {
	    	color: ['#3eaddb', '#e54273', '#948d8b', '#f69b00'],
	        title: {
	            text: 'Courses in Platforms',
	            subtext: 'By ' + month[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear() + ', ' + d.getHours() + ':' + d.getMinutes()
	        },
	        tooltip : {
	            trigger: 'axis',
	            axisPointer : {            
	                type : 'shadow'        
	            }
	        },
	        grid: {
	            top: 80,
	            bottom: 30
	        },
	        xAxis : [
	            {
	                type : 'value',
	                position: 'top',
	                splitLine: {lineStyle:{type:'dashed'}},
	            }
	        ],
	        yAxis : [
	            {
	                type : 'category',
	                axisLine: {show: false},
	                axisLabel: {show: false},
	                axisTick: {show: false},
	                splitLine: {show: false},
	                data : yData
	            }
	        ],
	        series : [
	            {
	                name:'No. of Courses',
	                type:'bar',
	                stack: 'No.',
	                itemStyle : { normal: {
	                    borderRadius: 5,
	                    label : {
	                        show: true,
	                        position: 'left',
	                        formatter: '{b}'
	                    }
	                }},
	                data: xData
	            }
	        ]
	    };
	    myChart.setOption(option);
	}

	$(document).ready(get_courses_list);

</script>