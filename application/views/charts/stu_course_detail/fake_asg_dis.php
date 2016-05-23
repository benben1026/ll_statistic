<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-bar-chart-o fa-fw"></i> Assignment Score Distribution - Assignment 1
                <div class="pull-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                            Choose Assignment
                            <span class="caret"></span>
                        </button>
                        <ul id="fake-asg-list" class="dropdown-menu pull-right" role="menu">
                            <li style="background-color: #3eaddb"><a href="javascript:void(0)">Assignment 1</a></li>
                            <li><a href="javascript:void(0)">Assignment 2</a></li>
                            <li><a href="javascript:void(0)">Assignment 3</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div id="fake-stu-asg-dis" style="height:400px;"></div>
            </div>
            <!-- /.panel-body -->
        </div>
	</div>
</div>

<script type="text/javascript">
    function drawFakeScoreDisAsg(){
        var xAxis = [];
        for(var i = 1; i <= 20; i++){
            xAxis.push(i * 5 + "%");
        }
        var myChart = echarts.init(document.getElementById('fake-stu-asg-dis'));
        var option = {
            title: {
                
            },
            tooltip: {},
            color: ['#3eaddb', '#e54273', '#948d8b', '#f69b00'],
            xAxis: {
                data: xAxis,
                name: 'Score'
            },
            yAxis: [
            {
                type : 'value',
                name : 'Number of Students'
            }
            ],
            series: [{
                name: 'Number of Students',
                type: 'bar',
                data: [0, 0, 0, 0, 0, 1, 3, 5, 3, 7, 15, 20, 20, 22, 24, 20, 36, 20, 10, 5],
                markPoint: {
                    data: [
                        {name: 'Your Grade', value: 57, xAxis: 13, yAxis: 25}
                    ],
                },
                markLine: {
                    data: [
                        [
                            {name: 'Average', value: 'Average', xAxis: 14, yAxis: 0},
                            {name: 'End', xAxis: 14, yAxis: 40}
                        ],
                        [
                            {name: 'Median', value: 'Median', xAxis: 15, yAxis: 0},
                            {name: 'End', xAxis: 15, yAxis: 40}
                        ]
                    ]
                }
            }]
        };
        myChart.setOption(option);
    }
    drawFakeScoreDisAsg();
</script>