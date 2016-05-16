<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-green">
            <div class="panel-heading">
                <i class="fa fa-bar-chart-o fa-fw"></i> Assignment Score Distribution
                <div class="pull-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                            Choose Assignment
                            <span class="caret"></span>
                        </button>
                        <ul id="asg-list" class="dropdown-menu pull-right" role="menu">
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div id="stu-asg-dis" style="height:400px;"></div>
            </div>
            <!-- /.panel-body -->
        </div>
	</div>
</div>

<script type="text/javascript">
	// var platform = "moodle";
	var platform = "<?php echo $_GET['platform']; ?>";
	// var courseId = "128";
	var courseId = "<?php echo $_GET['courseId']; ?>";
	$.ajax({
		url: '../assignment/getAsgList?courseId=' + courseId + '&platform=' + platform,
		type: 'get',
		dataType: 'json',
		success: function(data){
			if(!data['ok']){
				$('#stu-asg-dis').html(data['message']);
				return;
			}
			for(var i = 0; i < data['data'][platform]['result'].length; i++){
				if(i == 0){
					getAsgDis(data['data'][platform]['result'][i]['_id']['asg_name']);
				}
				$('#asg-list').append('<li><a href="javascript:getAsgDis(\'' + data['data'][platform]['result'][i]['_id']['asg_name'] + '\')">' + data['data'][platform]['result'][i]['_id']['asg_name'] + '</a></li>')
			}
		}
	});

	function getAsgDis(asg){		
		var url = '../assignment/getAsgDis?courseId=' + courseId + '&platform=' + platform + '&asg=' + asg;
		if($('#keepId').val() != undefined){
	        url += '&keepId=' + $('#keepId').val();
	    }
		$.ajax({
			url: url,
			type: 'get',
			dataType: 'json',
			success: function(data){
				if(!data['ok']){
					$('#stu-asg-dis').html(data['message']);
					return;
				}
				var temp = [0, 0, 0, 0, 0];
				for(var i = 0; i < data['data'].length; i++){
					if(data['data'][i]['statement']['result']['score']['raw'] == undefined){
						continue;
					}
					var rate = data['data'][i]['statement']['result']['score']['raw'] / data['data'][i]['statement']['result']['score']['max'];
					if(rate < 0.2){
						temp[0]++;
					}else if(rate < 0.4){
						temp[1]++;
					}else if(rate < 0.6){
						temp[2]++;
					}else if(rate < 0.8){
						temp[3]++;
					}else{
						temp[4]++;
					}
				}
				drawScoreDisAsg(temp, asg);
			}
		});
	}


	function drawScoreDisAsg(data, asg){
        var myChart = echarts.init(document.getElementById('stu-asg-dis'));
        var option = {
            title: {
                text: asg
            },
            tooltip: {},
            xAxis: {
                data: ["0%-20%","21%-40%","41%-60%","61%-80%","81%-100%"]
            },
            yAxis: [
            {
                type : 'value',
                name : 'Number of Students'
            }
			],
            series: [{
                name: asg,
                type: 'bar',
                data: data
            }]
        };
        myChart.setOption(option);
	}
</script>
