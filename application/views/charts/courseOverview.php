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
<div id="platform-distribution" style="height: 200px; margin: 0px 30px 30px 30px;"></div>

<script type="text/javascript">
	function get_courses_list(){
		$.ajax({
			url: 'http://localhost/fyp/ll_statistic/index.php/courseinfo',
			type: 'GET',
			dataType: 'json',
			success: function(data){
				if(data['ok']){
					render_enrolled_courses_table(data['data']);
					drawCourseInPlatform(data['data']);
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
			for(var $j = 0; $j < num; $j++){
				table.append($('<tr><td>' + i + '</td><td>' + data[lrs]['results'][$j]['course_name'] + '</td><td>' + lrs + '</td><td>' + data[lrs]['results'][$j]['role_name'] + '</td></tr>'));
				i++;
			}
		}
		$('#enrolled-courses').DataTable();
	}

	function drawCourseInPlatform(data){
		var yData = [];
		var xData = [];
		for(var lrs in data){
			yData.push(lrs);
			xData.push(data[lrs]['total_results']);
		}

	    var myChart = echarts.init(document.getElementById('platform-distribution'));
	    var labelRight = {normal: {label : {position: 'right'}}};
	    var d = new Date();
	    var month = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
	    option = {
	        title: {
	            text: 'Courses in Platforms',
	            subtext: 'By ' + month[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear() + ', ' + d.getHours() + ':' + d.getMinutes()
	        },
	        tooltip : {
	            trigger: 'axis',
	            axisPointer : {            // 坐标轴指示器，坐标轴触发有效
	                type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
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