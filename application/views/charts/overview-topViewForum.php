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
							<th>No.</th>
							<th>Thread Name</th>
							<th>Course Name</th>
							<th>Platform</th>
							<th>Num of Views</th>
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
		var datatable = $('#overview-topViewForum').DataTable( {
			"columns": [
				{ "width": "10%" },
				{ "width": "30%" },
				{ "width": "30%" },
				{ "width": "15%" },
				{ "width": "15%" },
			],
			"order": [[ 4, 'des' ]]
		} );
		datatable.ajax.url('../forum/overview/view').load();
		datatable.on( 'order.dt search.dt', function () {
	        datatable.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
	            cell.innerHTML = i+1;
	        } );
	    } ).draw();
		// $.ajax({
		// 	url: '../learninglocker/getForumViewingStu',
		// 	type: 'GET',
		// 	dataType: 'json',
		// 	success: function(data){
		// 		if(!data['ok']){
		// 			console.log('fail to get course information');
		// 			return;
		// 		}
		// 		var lrs;
		// 		if(data['data']['edx']['ok']){
		// 			lrs = 'edx';
		// 		}else if(data['data']['moodle']['ok']){
		// 			lrs = 'moodle';
		// 		}else{
		// 			$('#overview-topViewForum tbody').append('<tr><td></td><td></td><td></td><td></td></tr>');
		// 			$('#overview-topViewForum').dataTable();
		// 			return;
		// 		}
		// 		for(var i = 0; i < data['data'][lrs]['result'].length; i++){
		// 			$('#overview-topViewForum tbody').append('<tr><td>' + (i+1) + '</td><td><a target="_blank" href="' + data['data'][lrs]['result'][i]['_id']['forum_id'] + '">' + data['data'][lrs]['result'][i]['_id']['forum_name'] + '</a></td><td>' + lrs + '</td><td>' + data['data'][lrs]['result'][i]['count'] + '</td></tr>')
		// 		}
		// 		$('#overview-topViewForum').dataTable();
		// 	},
		// 	error: function(){

		// 	}
		// });
	}

	$(document).ready(get_overview_topViewForum);
</script>