<div class="row">
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-tasks fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div id="courseBasic-totalEnroll" class="huge"><img class="course-basic-loading" src="<?= base_url() ?>public/resource/loading3.gif" style="width:50px;"></div>
                        <div>Total Enrolled Student</div>
                    </div>
                </div>
            </div>
            <a href="#courseEnrolldrop">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
	</div>
    <div class="col-lg-3 col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="fa fa-tasks fa-5x"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div id="courseBasic-totalDrop" class="huge"><img class="course-basic-loading" src="<?= base_url() ?>public/resource/loading3.gif" style="width:50px;"></div>
                        <div>Total Dropout Student</div>
                    </div>
                </div>
            </div>
            <a href="#courseEnrolldrop">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
</div>

<script type="text/javascript">
    $.ajax({
        url: '../learninglocker/getCourseEnrollStudent?courseId=' + $('#courseId').val() + '&platform=' + $('#platform').val(),
        type: 'get',
        dataType: 'json',
        success: function(data){
            if(!data['ok']){
                console.log('Fail to get CourseEnrollStudent information');
                return;
            }
            for(var i = 0; i < data['data'][$('#platform').val()]['result'].length; i++){
                if(data['data'][$('#platform').val()]['result'][i] && data['data'][$('#platform').val()]['result'][i]['_id']['verb'] == 'enrolled onto'){
                    $('#courseBasic-totalEnroll').html(data['data'][$('#platform').val()]['result'][i]['count']);
                }else if(data['data'][$('#platform').val()]['result'][i]){
                    $('#courseBasic-totalDrop').html(data['data'][$('#platform').val()]['result'][i]['count']);
                }
            }
            $('.course-basic-loading').parent().html('0');
        }
    });
</script>