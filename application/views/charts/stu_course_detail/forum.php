<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-green">
            <div class="panel-heading">
                <i class="fa fa-bar-chart-o fa-fw"></i><i id="forumTableName"></i>
                <div class="pull-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                            Show
                            <span class="caret"></span>
                        </button>
                        <ul id="asg-list" class="dropdown-menu pull-right" role="menu">
                            <li><a href="javascript:void(0)" onclick="getCourseForumView()">Top View Forum</a></li>
                            <li><a href="javascript:void(0)" onclick="getCourseForumReply()">Top Reply Forum</a></li>
                            <li><a href="javascript:void(0)" onclick="getCourseForumActive()">Latest Active Forum</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
            	<div class="courseForumTableContainer">
	                <table id="courseForumTableView">
		                <thead>
		                	<tr>
		                		<th>No.</th><th>Thread Name</th><th>Num of View</th>
		                	</tr>
	                	</thead>
	                	<tbody>
	                		
	                	</tbody>
	                </table>
                </div>
                <div class="courseForumTableContainer">
	                <table id="courseForumTableReply">
		                <thead>
		                	<tr>
		                		<th>No.</th><th>Thread Name</th><th>Num of Reply</th>
		                	</tr>
	                	</thead>
	                	<tbody>
	                		
	                	</tbody>
	                </table>
                </div>
                <div class="courseForumTableContainer">
	                <table id="courseForumTableActive">
		                <thead>
		                	<tr>
		                		<th>No.</th><th>Thread Name</th><th>Date</th>
		                	</tr>
	                	</thead>
	                	<tbody>
	                		
	                	</tbody>
	                </table>
                </div>
            </div>
            <!-- /.panel-body -->
        </div>
	</div>
</div>

<script type="text/javascript">
	var datatableView = $('#courseForumTableView').DataTable();
	var datatableReply = $('#courseForumTableReply').DataTable();
	var datatableActive = $('#courseForumTableActive').DataTable();
	$('.courseForumTableContainer').hide();
	function getCourseForumView(){
		$('#asg-list li').css('background-color', '#ffffff');
		$('#asg-list li:nth-child(1)').css('background-color', '#3eaddb');
		$('.courseForumTableContainer').hide();
		datatableView.clear().draw();
		$($('.courseForumTableContainer')[0]).show();
		$('#forumTableName').html('Top Viewing Threads');
		datatableView.ajax.url('../forum/detail/view?courseId=' + $('#courseId').val() + '&platform=' + $('#platform').val() + '&from=' + $('#date-from').val() + '&to=' + $('#date-to').val()).load();
	}

	function getCourseForumReply(){
		$('#asg-list li').css('background-color', '#ffffff');
		$('#asg-list li:nth-child(2)').css('background-color', '#3eaddb');
		$('.courseForumTableContainer').hide();
		datatableReply.clear().draw();
		$($('.courseForumTableContainer')[1]).show();
		$('#forumTableName').html('Top Replying Threads');
		datatableReply.ajax.url('../forum/detail/reply?courseId=' + $('#courseId').val() + '&platform=' + $('#platform').val() + '&from=' + $('#date-from').val() + '&to=' + $('#date-to').val()).load();
	}

	function getCourseForumActive(){
		$('#asg-list li').css('background-color', '#ffffff');
		$('#asg-list li:nth-child(3)').css('background-color', '#3eaddb');
		$('.courseForumTableContainer').hide();
		datatableActive.clear().draw();
		$($('.courseForumTableContainer')[2]).show();

		$('#forumTableName').html('Latest Active Threads');
		datatableActive.ajax.url('../forum/detail/active?courseId=' + $('#courseId').val() + '&platform=' + $('#platform').val() + '&from=' + $('#date-from').val() + '&to=' + $('#date-to').val()).load();	
	}
    getCourseForumView();
    registerFunList.push(getCourseForumView);

</script>