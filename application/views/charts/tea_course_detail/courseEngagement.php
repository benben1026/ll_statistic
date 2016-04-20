<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-green">
            <div class="panel-heading">
                <i class="fa fa-bar-chart-o fa-fw"></i> Student Engagement
                
                	<form id="course-engagement-dateForm" class="form-inline" style="float: right; margin-top: -2px;">
	                	<div class="form-group">
							<label for="course-engagement-datepicker-from">From</label>
							<input type="text" class="form-control" id="course-engagement-datepicker-from" value="2015/09/01" style="height: 23px;">
						</div>
						<div class="form-group">
							<label for="course_engagement-datepicker-to">To</label>
							<input type="text" class="form-control" id="course-engagement-datepicker-to" value="2015/10/31" style="height: 23px;">
						</div>
						<button type="submit" class="btn btn-xs btn-default">Update</button>
                	</form>
                
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body" style="height: 700px;">
            	<img id="courseEngagement_loading" src="<?= base_url() ?>public/resource/loadbar.gif" style="width: 50%; height: 10%; margin-left: 25%; margin-top: 20%">
                <div id="courseEngagement" class="courseEngagement_content" style="height: 680px; width:75%; float: left; display: none"></div>
                <div id="courseEngagement_legend" class="courseEngagement_content" style="width:20%; float: left; margin-left: 5%; margin-top: 10px; display: none"></div>
            </div>
            <!-- /.panel-body -->
        </div>
	</div>
</div>
<script type="text/javascript">
	$( "#course-engagement-datepicker-from" ).datepicker({
    	dateFormat: "yy-mm-dd",
    	//defaultDate: +1
    });
    $( "#course-engagement-datepicker-to" ).datepicker({
    	dateFormat: "yy-mm-dd",
    	//defaultDate: new Date()
    });
    $( "#course-engagement-datepicker-from" ).datepicker("setDate", -14);
    $( "#course-engagement-datepicker-to" ).datepicker("setDate", new Date());

    $('#course-engagement-dateForm').submit(function(e){
    	e.preventDefault();
    	sendEngagementAjax();
    })
	var raw_data;
	function sendEngagementAjax(){
		$('#courseEngagement_loading').show();
    	$('.courseEngagement_content').hide();
		$.ajax({
			url: '../learninglocker/getCourseEngagement/' + $('#course-engagement-datepicker-from').val() + '/' + $('#course-engagement-datepicker-to').val() + '?courseId=' + $('#courseId').val() + '&platform=' + $('#platform').val(),
			type: 'get',
			dataType: 'json',
			success: function(data){
				$('#courseEngagement_loading').hide();
    			$('.courseEngagement_content').show();
				if(!data['ok']){
					console.log('fail to get engagement');
					return;
				}
				raw_data = data['data'];
				draw_legend(data['data']['ykeys']);
				//draw_engagement(data['data']);
			}
		});
	}
	sendEngagementAjax();

	function draw_legend(ykeys){
		$('#courseEngagement_legend').html('');
		for(var i = 0; i < ykeys.length; i++){
			$('#courseEngagement_legend').append('<input type="checkbox" class="courseEngagement_legendlist" value="' + ykeys[i] + '" checked />' + ykeys[i] + '<br/>');
		}
		legend_update();
		$('.courseEngagement_legendlist').click(legend_update);
	}

	function legend_update(){
		var tempData = {'ykeys': [], 'data': []};
		for(var i = 0; i < raw_data['ykeys'].length; i++){
			if($('.courseEngagement_legendlist:eq(' + i + ')').is(':checked')){
				tempData['ykeys'].push(raw_data['ykeys'][i]);
			}
		}
		// $('.courseEngagement_legendlist').each(function(index){
		// 	if($(this).is(":checked")){
		// 		tempData['ykeys'].push(raw_data['ykeys'][index]);
		// 	}
		// });
		for(var i = 0; i < raw_data['data'].length; i++){
			var j = 0;
			var tempNode = {};
			for(var t in raw_data['data'][i]){
				if(t == 'date'){
					tempNode['date'] = raw_data['data'][i]['date'];
					continue;
				}
				if($('.courseEngagement_legendlist:eq(' + j + ')').is(':checked')){
					tempNode[t] = raw_data['data'][i][t];
				}
				j++;
			}
			tempData['data'].push(tempNode);
		}
		draw_engagement(tempData);
	}

	function draw_engagement(data){
		$('#courseEngagement').html('');
		new Morris.Line({
		  // ID of the element in which to draw the chart.
		  element: 'courseEngagement',
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