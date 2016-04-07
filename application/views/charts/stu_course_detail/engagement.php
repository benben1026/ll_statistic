<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-bar-chart-o fa-fw"></i> Engagement
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div id="engagement" style="height: 300px;"></div>
            </div>
            <!-- /.panel-body -->
        </div>
	</div>
</div>
<script type="text/javascript">
	var courseId = '95';
	$.ajax({
		url: '../learninglocker/getEngagement?courseId=' + courseId,
		type: 'get',
		dataType: 'json',
		success: function(data){
			if(!data['ok']){
				console.log('fail to get engagement');
				return;
			}
			draw_engagement(data['data']);
		}
	});

	function draw_engagement(data){
		new Morris.Line({
		  // ID of the element in which to draw the chart.
		  element: 'engagement',
		  // Chart data records -- each entry in this array corresponds to a point on
		  // the chart.
		  data: data['data'],
		  // The name of the data record attribute that contains x-values.
		  xkey: 'date',
		  // A list of names of data record attributes that contain y-values.
		  ykeys: data['ykeys'],
		  // Labels for the ykeys -- will be displayed when you hover over the
		  // chart.
		  labels: data['ykeys']
		});
	}
</script>