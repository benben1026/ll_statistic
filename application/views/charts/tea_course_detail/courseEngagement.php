<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-bar-chart-o fa-fw"></i> Student Engagement                
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
	var raw_data = [];
	function sendEngagementAjax(){
		$('#courseEngagement_loading').show();
    	$('.courseEngagement_content').hide();
		$.ajax({
			url: '../engagement/detail?courseId=' + $('#courseId').val() + '&platform=' + $('#platform').val() + '&from=' + $('#date-from').val() + '&to=' + $('#date-to').val(),
			type: 'get',
			dataType: 'json',
			success: function(data){
				$('#courseEngagement_loading').hide();
    			$('.courseEngagement_content').show();
				if(!data['ok']){
					$('#courseEngagement').html(data['message']);
					return;
				}
				if(data['data']['data'].length == 0){
					raw_data['data'] = [{'date': $('#date-from').val()}, {'date': $('#date-to').val()}];
					for(var i = 0; i < data['data']['ykeys'].length; i++){
						raw_data['data'][0][data['data']['ykeys'][i]] = 0;
						raw_data['data'][1][data['data']['ykeys'][i]] = 0;
					}
					raw_data['ykeys'] = data['data']['ykeys'];
				}else{
					raw_data = data['data'];
				}				
				draw_legend(data['data']['ykeys']);
				//draw_engagement(data['data']);
			}
		});
	}
	sendEngagementAjax();
	registerFunList.push(sendEngagementAjax);

	function draw_legend(ykeys){
		$('#courseEngagement_legend').html('');
		for(var i = 0; i < ykeys.length; i++){
			$('#courseEngagement_legend').append('<input type="checkbox" class="courseEngagement_legendlist" value="' + ykeys[i] + '" checked /> ' + ykeys[i] + '<br/>');
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
