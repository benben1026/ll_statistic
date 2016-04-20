<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-green">
			<div class="panel-heading">
				<i class="fa fa-bar-chart-o fa-fw"></i> Course File Viewing
			</div>
			<div class="panel-body" style="height: 420px;">
				<img id="courseFileView_loading" src="<?= base_url() ?>public/resource/loadbar.gif" style="width: 50%; height: 10%; margin-left: 25%; margin-top: 10%">
				<div id="tea-file-view" style="height: 400px; display: none;"></div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	var dataArray = [];
	var linkList = [];
	function getCourseFileViewing(){
		$.ajax({
			url: '../learninglocker/getCourseFileView?courseId=' + $('#courseId').val() + '&platform=' + $('#platform').val(),
			type: 'get',
			dataType: 'json',
			success: function(data){
				if(!data['ok']){
					$('#courseFileView_loading').hide();					
					$('#tea-file-view').html('Fail to get data');
					$('#tea-file-view').show();
					console.log('Fail to get data');
					return;
				}
				$('#courseFileView_loading').hide();
				$('#tea-file-view').show();
				var dataArray = [];
				for(var i = 0; i < 8 && i < data['data'][$('#platform').val()]['result'].length; i++){
					//xAxis.push('<a target="_blank" href="' + data['data'][$('#platform').val()]['result'][i]['_id']['file_id'] + '">' + data['data'][$('#platform').val()]['result'][i]['_id']['file_name'] + '</a>');
					// xAxis.push(data['data'][$('#platform').val()]['result'][i]['_id']['file_name']);
					// seriesData.push(data['data'][$('#platform').val()]['result'][i]['count']);
					var tempFileName = data['data'][$('#platform').val()]['result'][i]['_id']['file_name'].length > 18 ? data['data'][$('#platform').val()]['result'][i]['_id']['file_name'].substring(0, 10) + '...' + data['data'][$('#platform').val()]['result'][i]['_id']['file_name'].substring(data['data'][$('#platform').val()]['result'][i]['_id']['file_name'].length - 4) : data['data'][$('#platform').val()]['result'][i]['_id']['file_name'];
					dataArray.push({'filename': tempFileName, 'number': data['data'][$('#platform').val()]['result'][i]['count']});
					linkList.push(data['data'][$('#platform').val()]['result'][i]['_id']['file_id']);
				}
				drawFileViewing(dataArray);
			}
		});
	}

	function drawFileViewing(dataArray){
   		var linkListIndex = 0;
		Morris.Bar({
			element: 'tea-file-view',
			data: dataArray,
			xkey: 'filename',
			ykeys: ['number'],
			labels: ['Number of Veiws'],
			xLabelAngle: 15,
			hoverCallback: function (index, options, content, row) {
				linkListIndex = index;
				return dataArray[index]['filename'] + ": " + dataArray[index]['number'];
			}
		});
		$('#tea-file-view rect').click(function(){

			//window.location.href = linkList[linkListIndex];
			window.open(linkList[linkListIndex], '_blank')
		})
	}
	getCourseFileViewing();
</script>