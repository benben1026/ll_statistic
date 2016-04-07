<div style="margin: 30px;">
	<table id="overview-topReplyForum">
		<thead>
			<tr>
				<th>No.</th><th>Thread Name</th><th>Platform</th><th>Num of Reply</th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>
</div>

<script type="text/javascript">
	function get_overview_topReplyForum(){
		$.ajax({
			url: '../learninglocker/getForumReplyStu',
			type: 'GET',
			dataType: 'json',
			success: function(data){
				if(!data['ok']){
					console.log('fail to get course information');
					return;
				}
				for(var i = 0; i < data['data']['moodle']['result'].length; i++){
					$('#overview-topReplyForum tbody').append('<tr><td>' + (i+1) + '</td><td><a target="_blank" href="' + data['data']['moodle']['result'][i]['_id']['forum_id'] + '">' + data['data']['moodle']['result'][i]['_id']['forum_name'] + '</a></td><td>KEEP Moodle</td><td>' + data['data']['moodle']['result'][i]['count'] + '</td></tr>')
				}
				$('#overview-topReplyForum').dataTable();
			},
			error: function(){

			}
		});
	}

	$(document).ready(get_overview_topReplyForum);
</script>