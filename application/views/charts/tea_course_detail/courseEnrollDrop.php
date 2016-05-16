<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-green">
            <div class="panel-heading">
                <i class="fa fa-bar-chart-o fa-fw"></i> Course Enrollment &amp Dropout
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body" style="height: 400px;">
            	<img id="courseEnrolldrop_loading" src="<?= base_url() ?>public/resource/loadbar.gif" style="width: 50%; height: 10%; margin-left: 25%; margin-top: 10%">
                <div id="courseEnrolldrop" class="courseEnrolldrop_content" style="height: 380px; width:100%; display: none"></div>
            </div>
            <!-- /.panel-body -->
        </div>
	</div>
</div>

<script type="text/javascript">
    
    function sendEnrollDropAjax(){
        $('#courseEnrolldrop_loading').show();
        $('.courseEnrolldrop_content').hide();
        $.ajax({
            url: '../course/addDrop/timeline?from=' + $('#date-from').val() + '&to=' + $('#date-to').val() + '&courseId=' + $('#courseId').val() + '&platform=' + $('#platform').val(),
            type: 'get',
            dataType: 'json',
            success: function(data){
                $('#courseEnrolldrop_loading').hide();
                $('.courseEnrolldrop_content').show();
                if(!data['ok']){
                  $('#courseEnrolldrop').html(data['message']);
                    return;
                }
                if(data['data'].length == 0){
                    draw_enrolldrop([{date: $('#date-from').val(), Enroll:0, Drop: 0}, {date: $('#date-to').val(), Enroll:0, Drop: 0}]);
                }else{
                    draw_enrolldrop(data['data']);
                }
            }
        });
    }
    sendEnrollDropAjax();
    registerFunList.push(sendEnrollDropAjax);
    function draw_enrolldrop(data){
        $('#courseEnrolldrop').html('');
        new Morris.Line({
          // ID of the element in which to draw the chart.
          element: 'courseEnrolldrop',
          // Chart data records -- each entry in this array corresponds to a point on
          // the chart.
          data: data,
          // The name of the data record attribute that contains x-values.
          xkey: 'date',
          // A list of names of data record attributes that contain y-values.
          ykeys: ['Enroll', 'Drop'],
          // Labels for the ykeys -- will be displayed when you hover over the
          // chart.
          labels: ['Enroll', 'Drop'],
        });
    }
</script>