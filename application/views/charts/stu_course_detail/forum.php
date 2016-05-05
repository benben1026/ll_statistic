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
		datatableView.ajax.url('../forum/detail/view?courseId=' + $('#courseId').val() + '&platform=' + $('#platform').val()).load();
	}

	function getCourseForumReply(){
		$('#asg-list li').css('background-color', '#ffffff');
		$('#asg-list li:nth-child(2)').css('background-color', '#3eaddb');
		$('.courseForumTableContainer').hide();
		datatableReply.clear().draw();
		$($('.courseForumTableContainer')[1]).show();
		$('#forumTableName').html('Top Replying Threads');
		datatableReply.ajax.url('../forum/detail/reply?courseId=' + $('#courseId').val() + '&platform=' + $('#platform').val()).load();
	}

	function getCourseForumActive(){
		$('#asg-list li').css('background-color', '#ffffff');
		$('#asg-list li:nth-child(3)').css('background-color', '#3eaddb');
		$('.courseForumTableContainer').hide();
		datatableActive.clear().draw();
		$($('.courseForumTableContainer')[2]).show();

		$('#forumTableName').html('Latest Active Threads');
		datatableActive.ajax.url('../forum/detail/active?courseId=' + $('#courseId').val() + '&platform=' + $('#platform').val()).load();	}


    // function getCourseForum(type){
	   //  var title = type == 'view' ? 'Top Viewing Threads' : (type == 'reply' ? 'Top Replying Threads' : 'Latest Active Threads');
	   //  var header = type == 'view' ? 'Num of View' : (type == 'reply' ? 'Num of Reply' : 'Date');
    //     $('#forumTableName').html(title);
    //     //$('#courseForumTable tr:eq(0) th:eq(2)').text(header);
    //     if(type == 'view'){
    //     	$('#forumTableName').html('Top Viewing Threads');
    //     	$('#courseForumTable thead tr').html('<td>No.</td><td>Thread Name</td><td>Num of View</td>');
    //     }else if(type == 'reply'){
    //     	$('#forumTableName').html('Top Replying Threads');
    //     	$('#courseForumTable thead tr').html('<td>No.</td><td>Thread Name</td><td>Num of Reply</td>');
    //     }else if(type == 'active'){
    //     	$('#forumTableName').html('Latest Active Threads');
    //     	$('#courseForumTable thead tr').html('<td>No.</td><td>Thread Name</td><td>Date</td>');
    //     }
	   //  datatable.ajax.url('../learninglocker/getCourseForum?courseId=' + $('#courseId').val() + '&platform=' + $('#platform').val() + '&type=' + type).load();
	   //  datatable.draw();
    // }
    getCourseForumView();

</script>