<div class="row">
	<div class="col-lg-10">
		<div class="panel panel-green">
			<div class="panel-heading">
				<i class="fa fa-bar-chart-o fa-fw"></i> Total File Viewing
			</div>
			<div class="panel-body" style="height: 420px;">
				<img id="totalFileView_loading" src="<?= base_url() ?>public/resource/loadbar.gif" style="width: 50%; height: 10%; margin-left: 25%; margin-top: 10%">
				<div id="total-file-view" style="height: 400px; display: none;"></div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	var dataArray = [];
	var linkList = [];
	function getFileViewing(){
		$.ajax({
			url: '../file/overview',
			type: 'get',
			dataType: 'json',
			success: function(data){
				if(!data['ok']){
					$('#courseFileView_loading').hide();					
					$('#total-file-view').html('Fail to get data');
					$('#total-file-view').show();
					console.log('Fail to get data');
					return;
				}
				$('#totalFileView_loading').hide();
				$('#total-file-view').show();
				var dataArray = [];
				for(var i = 0; i < 7 && i < data['data'].length; i++){
					var tempFileName = data['data'][i]['_id']['file_name'].length > 18 ? data['data'][i]['_id']['file_name'].substring(0, 10) + '...' + data['data'][i]['_id']['file_name'].substring(data['data'][i]['_id']['file_name'].length - 4) : data['data'][i]['_id']['file_name'];
					dataArray.push({'filename': tempFileName, 'platform': data['data'][i]['_id']['platform'], 'number': data['data'][i]['count']});
					linkList.push(data['data'][i]['_id']['file_id']);
				}
				drawFileViewing(dataArray);
			}
		});
	}

	function drawFileViewing(dataArray){
   		var linkListIndex = 0;
		Morris.Bar({
			element: 'total-file-view',
			data: dataArray,
			xkey: 'filename',
			ykeys: ['number'],
			labels: ['Number of Veiws'],
			xLabelAngle: 15,
			hoverCallback: function (index, options, content, row) {
				linkListIndex = index;
				return dataArray[index]['filename'] + ": " + dataArray[index]['number'] + "<br>" + dataArray[index]['platform'];
			}
		});
		$('#total-file-view rect').click(function(){
			window.open(linkList[linkListIndex], '_blank')
		})
	}
	getFileViewing();
</script>