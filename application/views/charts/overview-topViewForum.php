<div class="row">
	<div class="col-lg-10">
		<div class="panel panel-default">
			<div class="panel-heading">
                <i class="fa fa-bar-chart-o fa-fw"></i> Top Viewing Forum
            </div>
			<div style="margin: 30px;">
				<table id="overview-topViewForum">
					<thead>
						<tr>
							<th>No.</th><th>Thread Name</th><th>Platform</th><th>Num of Views</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	function get_overview_topViewForum(){
		$.ajax({
			url: '../learninglocker/getForumViewingStu',
			type: 'GET',
			dataType: 'json',
			success: function(data){
				if(!data['ok']){
					console.log('fail to get course information');
					return;
				}
				for(var i = 0; i < data['data']['moodle']['result'].length; i++){
					//$('#overview-topViewForum tbody').append('<a target="_blank" href="' + data['data']['moodle']['result'][i]['_id']['forum_id'] + '"><tr><td>' + (i+1) + '</td><td>' + data['data']['moodle']['result'][i]['_id']['forum_name'] + '</td><td>KEEP Moodle</td><td>' + data['data']['moodle']['result'][i]['count'] + '</td></tr></a>')
					$('#overview-topViewForum tbody').append('<tr><td>' + (i+1) + '</td><td><a target="_blank" href="' + data['data']['moodle']['result'][i]['_id']['forum_id'] + '">' + data['data']['moodle']['result'][i]['_id']['forum_name'] + '</a></td><td>KEEP Moodle</td><td>' + data['data']['moodle']['result'][i]['count'] + '</td></tr>')
				}
				$('#overview-topViewForum').dataTable();
			},
			error: function(){

			}
		});
	}

	$(document).ready(get_overview_topViewForum);
</script>