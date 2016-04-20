<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-green">
            <div class="panel-heading">
                <i class="fa fa-bar-chart-o fa-fw"></i> Personal Performance
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body" style="height: 420px">
                <img id="personalPerformance_loading" src="<?= base_url() ?>public/resource/loadbar.gif" style="width: 50%; height: 10%; margin-left: 25%; margin-top: 10%">
                <div id="stu-performance" style="height:400px; width: 50%; float: left; display: none;"></div>
                <div id="stu-performance2" style="height:400px; width: 50%; float: left; display: none;"></div>
            </div>
            <!-- /.panel-body -->
        </div>
	</div>
</div>

<script type="text/javascript">
    $.ajax({
        url: '../learninglocker/getPersonalPerformance?courseId=' + $('#courseId').val() + '&platform=' + $('#platform').val(),
        type: 'get',
        dataType: 'json',
        success: function(data){
            $('#personalPerformance_loading').hide();
            $('#stu-performance').show();
            $('#stu-performance2').show();
            drawPersonalPerformance(data['personal'], data['average']);
        }
    });

    function drawPersonalPerformance(personal, average){
        var personalPerformanceChart = echarts.init(document.getElementById("stu-performance"));
        option = {
            title: {
                text: ''
            },
            tooltip: {},
            legend: {
                data: ['Personal Engagement', 'Classmate Average Engagement']
            },
            color: ['#3eaddb', '#e54273', '#948d8b', '#f69b00'],
            radar: {
                // shape: 'circle',
                indicator: [
                   { name: 'View Courseware', max: Math.max(personal[0], average[0]) * 1.2 },
                   { name: 'Watch Video', max: Math.max(personal[1], average[1]) * 1.2},
                   { name: 'View Discussion', max: Math.max(personal[2], average[2]) * 1.2},
                   { name: 'Create Discussion', max: Math.max(personal[3], average[3]) * 1.2},
                   { name: 'Response to Discussion', max: Math.max(personal[4], average[4]) * 1.2},
                   { name: 'Vote Discussion', max: Math.max(personal[5], average[5]) * 1.2},
                   { name: 'Complete Quiz', max: Math.max(personal[6], average[6]) * 1.2}
                   // { name: 'View Courseware', max: 500 },
                   // { name: 'Watch Video', max: 50},
                   // { name: 'View Discussion', max: 1000},
                   // { name: 'Create Discussion', max: 50},
                   // { name: 'Response to Discussion', max: 100},
                   // { name: 'Vote Discussion', max: 100},
                   // { name: 'Complete Quiz', max: 50}
                ]
            },
            series: [{
                name: 'Personal vs Average',
                type: 'radar',
                // areaStyle: {normal: {}},
                data : [
                    {
                        value : personal,
                        name : 'Personal Engagement'
                    },
                     {
                        value : average,
                        name : 'Classmate Average Engagement'
                    }
                ]
            }]
        };
        if (option && typeof option === "object"){
            personalPerformanceChart.setOption(option, true);
        }

        var personalPerformanceChart2 = echarts.init(document.getElementById("stu-performance2"));
        option2 = {
            title: {
                text: ''
            },
            tooltip: {},
            legend: {
                data: ['Personal Academic Performance', 'Average Academic Performance']
            },
            color: ['#652c99', '#f69b00'],
            radar: {
                // shape: 'circle',
                indicator: [
                   { name: 'Assignment 1', max: 100 },
                   { name: 'Assignment 2', max: 100 },
                   { name: 'Assignment 3', max: 100 },
                   { name: 'Quiz 1', max: 100 },
                   { name: 'Quiz 2', max: 100 }
                ]
            },
            series: [{
                name: 'Personal vs Average',
                type: 'radar',
                // areaStyle: {normal: {}},
                data : [
                    {
                        value : [80, 95, 95, 73, 84],
                        name : 'Personal Academic Performance'
                    },
                     {
                        value : [67.5, 88.5, 84, 85, 70],
                        name : 'Average Academic Performance'
                    }
                ]
            }]
        };
        if (option2 && typeof option2 === "object"){
            personalPerformanceChart2.setOption(option2, true);
        }
    }
</script>