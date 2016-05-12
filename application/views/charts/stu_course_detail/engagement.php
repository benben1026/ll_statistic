<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-green">
            <div class="panel-heading">
                <i class="fa fa-bar-chart-o fa-fw"></i> Engagement                
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body" style="height: 320px;">
            	<img id="engagement_loading" src="<?= base_url() ?>public/resource/loadbar.gif" style="width: 50%; height: 10%; margin-left: 25%; margin-top: 10%">
                <div id="engagement" class="engagement_content" style="height: 300px; width:75%; float: left; display: none"></div>
                <div id="engagement_legend" class="engagement_content" style="width:20%; float: left; margin-left: 5%; margin-top: 20px; display: none"></div>
            </div>
            <!-- /.panel-body -->
        </div>
	</div>
</div>
<script type="text/javascript">
    function startLoading(){
    	$('#engagement_loading').show();
    	$('.engagement_content').hide();
    }
    function endLoading(){
    	$('#engagement_loading').hide();
    	$('.engagement_content').show();
    }
	var raw_data;
	function sendEngagementAjax(){
		startLoading();
		var url = '../engagement/detail?from=' + $('#date-from').val() + '&to=' + $('#date-to').val() + '&courseId=' + $('#courseId').val() + '&platform=' + $('#platform').val();
		if($('#keepId').val() != undefined){
            url += '&keepId=' + $('#keepId').val();
        }
		$.ajax({
			url: url,
			type: 'get',
			dataType: 'json',
			success: function(data){
				endLoading();
				if(!data['ok']){
					console.log(data['message']);
					return;
				}
				raw_data = data['data'];
				draw_legend(data['data']['ykeys']);
				//draw_engagement(data['data']);
			}
		});
	}
	sendEngagementAjax();
	registerFunList.push(sendEngagementAjax);

	function draw_legend(ykeys){
		$('#engagement_legend').html('');
		for(var i = 0; i < ykeys.length; i++){
			$('#engagement_legend').append('<input type="checkbox" class="engagement_legendlist" value="' + ykeys[i] + '" checked />' + ykeys[i] + '<br/>');
		}
		legend_update();
		$('.engagement_legendlist').click(legend_update);
	}

	function legend_update(){
		var tempData = {'ykeys': [], 'data': []};
		for(var i = 0; i < raw_data['ykeys'].length; i++){
			if($('.engagement_legendlist:eq(' + i + ')').is(':checked')){
				tempData['ykeys'].push(raw_data['ykeys'][i]);
			}
		}
		for(var i = 0; i < raw_data['data'].length; i++){
			var j = 0;
			var tempNode = {};
			for(var t in raw_data['data'][i]){
				if(t == 'date'){
					tempNode['date'] = raw_data['data'][i]['date'];
					continue;
				}
				if($('.engagement_legendlist:eq(' + j + ')').is(':checked')){
					tempNode[t] = raw_data['data'][i][t];
				}
				j++;
			}
			tempData['data'].push(tempNode);
		}
		draw_engagement(tempData);
	}

	function draw_engagement(data){
		$('#engagement').html('');
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