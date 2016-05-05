<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-green">
            <div class="panel-heading">
                <i class="fa fa-bar-chart-o fa-fw"></i> Course Enrollment &amp Dropout                    
                <form id="course-enrolldrop-dateForm" class="form-inline" style="float: right; margin-top: -2px;">
                    <div class="form-group" >
                        <label for="course-enrolldrop-datepicker-from">From</label>
                        <input type="text" class="form-control" id="course-enrolldrop-datepicker-from" value="2015/09/01" style="height: 23px;">
                    </div>
                    <div class="form-group">
                        <label for="course-enrolldrop-datepicker-to">To</label>
                        <input type="text" class="form-control" id="course-enrolldrop-datepicker-to" value="2015/10/31" style="height: 23px;">
                    </div>
                    <button type="submit" class="btn btn-xs btn-default">Update</button>
                </form>
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
    $( "#course-enrolldrop-datepicker-from" ).datepicker({
        dateFormat: "yy-mm-dd",
        //defaultDate: +1
    });
    $( "#course-enrolldrop-datepicker-to" ).datepicker({
        dateFormat: "yy-mm-dd",
        //defaultDate: new Date()
    });
    $( "#course-enrolldrop-datepicker-from" ).datepicker("setDate", -90);
    $( "#course-enrolldrop-datepicker-to" ).datepicker("setDate", new Date());

    $('#course-enrolldrop-dateForm').submit(function(e){
        e.preventDefault();
        sendEnrollDropAjax();
    })
    
    function sendEnrollDropAjax(){
        $('#courseEnrolldrop_loading').show();
        $('.courseEnrolldrop_content').hide();
        $.ajax({
            url: '../course/addDrop/timeline?from=' + $('#course-enrolldrop-datepicker-from').val() + '&to=' + $('#course-enrolldrop-datepicker-to').val() + '&courseId=' + $('#courseId').val() + '&platform=' + $('#platform').val(),
            type: 'get',
            dataType: 'json',
            success: function(data){
                $('#courseEnrolldrop_loading').hide();
                $('.courseEnrolldrop_content').show();
                if(!data['ok']){
                    console.log('fail to get engagement');
                    return;
                }
                draw_enrolldrop(data['data']);
            }
        });
    }
    sendEnrollDropAjax();

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