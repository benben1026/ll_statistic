// //Gold Price
// var forumActivity = [
//     [gd(2015, 1, 1), 10], [gd(2015, 1, 3), 30], [gd(2015, 1, 5), 18], [gd(2015, 1, 8), 31],
//     [gd(2015, 1, 10), 15], [gd(2015, 1, 12), 23], [gd(2015, 1, 13), 18], [gd(2015, 1, 15), 50], 
// ];
 
// //Change
// var assessmentActivity = [
//     [gd(2015, 1, 1), 63], [gd(2015, 1, 3), 68], [gd(2015, 1, 5), 60], [gd(2015, 1, 8), 44],
//     [gd(2015, 1, 10), 46], [gd(2015, 1, 12), 48], [gd(2015, 1, 13), 37], [gd(2015, 1, 15), 38], 
// ];
// var loginActivity = [
//     [gd(2015, 1, 1), 89], [gd(2015, 1, 3), 77], [gd(2015, 1, 5), 70], [gd(2015, 1, 8), 75],
//     [gd(2015, 1, 10), 82], [gd(2015, 1, 12), 87], [gd(2015, 1, 13), 68], [gd(2015, 1, 15), 71], 
// ];
// var ViewingActivity = [
//     [gd(2015, 1, 1), 45], [gd(2015, 1, 3), 34], [gd(2015, 1, 5), 54], [gd(2015, 1, 8), 32],
//     [gd(2015, 1, 10), 53], [gd(2015, 1, 12), 58], [gd(2015, 1, 13), 45], [gd(2015, 1, 15), 37], 
// ];
 
// var dataset = [
//     { label: "Forum Activity", data: forumActivity },
//     { label: "Assessment Activity", data: assessmentActivity },
// 	{ label: "Login Activity", data: loginActivity },
// 	{ label: "Viewing Activity", data: ViewingActivity }
//];

var dataset = [];
var options = {
            series: {
                lines: {
                    show: true
                },
                points: {
                    radius: 3,
                    fill: true,
                    show: true
                }
            },
            xaxis: {
                mode: "time",
                tickSize: [5, "day"],
                tickLength: 0,
                axisLabel: "2015",
                axisLabelUseCanvas: true,
                axisLabelFontSizePixels: 12,
                axisLabelFontFamily: 'Verdana, Arial',
                axisLabelPadding: 10
            },
            yaxes: [{
                axisLabel: "Number of activities",
                axisLabelUseCanvas: true,
                axisLabelFontSizePixels: 12,
                axisLabelFontFamily: 'Verdana, Arial',
                axisLabelPadding: 3,
            }
          ],
            legend: {
                noColumns: 0,
                labelBoxBorderColor: "#000000",
                position: "nw"
            },
            grid: {
                hoverable: true,
                borderWidth: 2,
                borderColor: "#633200",
                backgroundColor: { colors: ["#ffffff", "#EDF5FF"] }
            },
            colors: ["#FF0000", "#0022FF"]
        };

var activitiesPieChartOptions = {
	series: {
        pie: {
            show: true,
			radius: 1,
            label: {
                show: true,
                radius: 2/3,
                formatter: labelFormatter,
                threshold: 0.05
            }
        }
    },
    grid: {
        hoverable: true,
        clickable: true
    }
};
/*
$(document).ready(function () {
    $.plot($("#flot-placeholder"), dataset, options);
});
*/
function gd(year, month, day) {
    return new Date(year, month, day).getTime();
}

function labelFormatter(label, series) {
		return "<div style='font-size:8pt; text-align:center; padding:2px; color:black;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
}
		
// $(function() {
// // hard-code color indices to prevent them from shifting as
// 		// countries are turned on/off
// 		var i = 0;
// 		$.each(dataset, function(key, val) {
// 			val.color = i;
// 			++i;
// 		});

// 		// insert checkboxes 
// 		var choiceContainer = $("#choices");
// 		$.each(dataset, function(key, val) {
// 			choiceContainer.append("<br/><input type='checkbox' name='" + key +
// 				"' checked='checked' id='id" + key + "'></input>" +
// 				"<label for='id" + key + "'>"
// 				+ val.label + "</label>");
// 		});

// 		choiceContainer.find("input").click(plotAccordingToChoices);

// 		function plotAccordingToChoices() {

// 			var data = [];

// 			choiceContainer.find("input:checked").each(function () {
// 				var key = $(this).attr("name");
// 				if (key && dataset[key]) {
// 					data.push(dataset[key]);
// 				}
// 			});

// 			if (data.length > 0) {
// 				$.plot($("#flot-placeholder"), data, options);
// 			}
// 		}

// 		plotAccordingToChoices();
		
// 		});

function clickSubmit(){
    dataset = [];
    //var url = 'http://benjamin-zhou.com/ll_statistic/index.php/access/get' + fun + '/' +  $('#dateYFrom').val() + '/' +  $('#dateMFrom').val() + '/' +  $('#dateDFrom').val() + '/' +  $('#dateYTo').val() + '/' +  $('#dateMTo').val() + '/' +  $('#dateDTo').val();
    //var url = 'http://benjamin-zhou.com/ll_statistic/index.php/access/get' + fun + '/' +  $('#datepicker_from').val() + '/' +  $('#datepicker_to').val();
    var url = 'http://benjamin-zhou.com/ll_statistic/index.php/access/getPersonalStat/' +  $('#datepicker_from').val() + '/' +  $('#datepicker_to').val();
    console.log(url);
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        //async: false,
        success: function(data){
            if(data['ok'] == 1){
                generateDataSet(data['data']['Login'], 'Login');
                generateDataSet(data['data']['Forum'], 'Forum');
                generateDataSet(data['data']['Assessment'], 'Assessment');
                generateDataSet(data['data']['Lecture Material'], 'Lecture Material');
                setPlot();
                plotAccordingToChoices();
                //generateDataSet(data['result'], data['label']);
				
				//plot pie chart
				var pieChartDataSet = generatePieChartDataSet(data['data']);
				$.plot('#activities-pie-chart', pieChartDataSet, activitiesPieChartOptions);
            }
            else
                alert("Fail to get data");
        },
        error: function(){
            alert("Fail to get data");
        }

    })
}

function getVerb(input){
    var tokens = input.split("/");
    if(input[input.length - 1] == '/'){
        return tokens[tokens.length - 2];
    }else{
        return tokens[tokens.length - 1];
    }
}

function getDate(rawDate){
    var t = rawDate.split('-');
    return new Date(parseInt(t[0]), parseInt(t[1]) - 1, parseInt(t[2])).getTime();
}

function generateDataSet(data, label){
    //dataset = [];
    // for(var i = 0; i < data.length; i++){
    //     var date = [];
    //     for(var j = 0; j < data[i]['date'].length; j++){
    //         var t = [];
    //         t.push(getDate(data[i]['date'][j]['date']));
    //         t.push(data[i]['date'][j]['count']);
    //         date.push(t);
    //     }
    //     var label = getVerb(data[i]['_id']) + " Activity";
    //     dataset.push({'label': label, 'data': date});
    // }

    var date = [];
    for(var i = 0; i < data.length; i++){
        var t = [];
        t.push(getDate(data[i]['_id']['date']));
        t.push(data[i]['sum']);
        date.push(t);
    }
    dataset.push({'label': label + '&nbsp&nbsp', 'data': date});
}

function generatePieChartDataSet(data){
	var pieChartDataSet = [];
	console.log(data);
	for(var type in data){
		console.log(type);
		var sum = 0;
		for(var i = 0; i < data[type].length; i++){
			console.log(data[type][i]);
			sum += parseInt(data[type][i]['sum']);
		}
		pieChartDataSet.push({'label': type, 'data': sum});
		console.log(sum);
	}
	console.log(pieChartDataSet);
	return pieChartDataSet;
}

function setPlot(){
    var i = 0;
    $.each(dataset, function(key, val) {
        val.color = i;
        ++i;
    });

    // insert checkboxes 
    $("#choices").html('');
    var choiceContainer = $("#choices");
    $.each(dataset, function(key, val) {
        choiceContainer.append("<br/><input type='checkbox' name='" + key +
            "' checked='checked' id='id" + key + "'></input>" +
            "<label for='id" + key + "'>"
            + val.label + "</label>");
    });

    choiceContainer.find("input").click(plotAccordingToChoices);
}

function plotAccordingToChoices() {

    var data = [];

    $("#choices").find("input:checked").each(function () {
        var key = $(this).attr("name");
        if (key && dataset[key]) {
            data.push(dataset[key]);
        }
    });

    if (data.length > 0) {
        $.plot($("#flot-placeholder"), data, options);
    }
}

function init(){
    dataset = [];
    //clickSubmit();
    draw();
    drawCourseInPlatform();
    // clickSubmit('Login');
    // clickSubmit('LecMat');
    // clickSubmit('Assessment');
    // clickSubmit('Forum');
    //setPlot();
    //plotAccordingToChoices();
}

function draw(){
    // 基于准备好的dom，初始化echarts实例
        var myChart = echarts.init(document.getElementById('testChart1'));

        // 指定图表的配置项和数据
        var option = {
            title: {
                text: 'Your Activities'
            },
            tooltip: {},
            legend: {
                data:['Login', 'Forum', 'Assessment', 'Lecture Material']
            },
            xAxis: {
                data: ["Sep 1","Sep 5","Sep 10","Sep 15","Sep 20","Sep 25"]
            },
            yAxis: {},
            series: [{
                name: 'Login',
                type: 'line',
                data: [5, 20, 36, 10, 10, 20]
            },
            {
                name: 'Forum',
                type: 'line',
                data: [10, 5, 3, 1, 2, 5]
            },
            {
                name: 'Assessment',
                type: 'line',
                data: [15, 30, 40, 5, 7, 10]
            },
            {
                name: 'Lecture Material',
                type: 'line',
                data: [2, 6, 4, 2, 8, 3]
            }]
        };

        // 使用刚指定的配置项和数据显示图表。
        myChart.setOption(option);
}

function drawCourseInPlatform(){
    var myChart = echarts.init(document.getElementById('testChart2'));
    var labelRight = {normal: {label : {position: 'right'}}};
    var d = new Date();
    var month = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    option = {
        title: {
            text: 'Courses in Platforms',
            subtext: 'By ' + month[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear() + ', ' + d.getHours() + ':' + d.getMinutes()
        },
        tooltip : {
            trigger: 'axis',
            axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
            }
        },
        grid: {
            top: 80,
            bottom: 30
        },
        xAxis : [
            {
                type : 'value',
                position: 'top',
                splitLine: {lineStyle:{type:'dashed'}},
            }
        ],
        yAxis : [
            {
                type : 'category',
                axisLine: {show: false},
                axisLabel: {show: false},
                axisTick: {show: false},
                splitLine: {show: false},
                data : ['ILC', 'KEEPMoodle', 'CourseBuilder', 'ewant', 'Udacity', 'FutureLearn', 'xuetangX', 'Coursera', 'edX']
            }
        ],
        series : [
            {
                name:'No. of Courses',
                type:'bar',
                stack: 'No.',
                itemStyle : { normal: {
                    borderRadius: 5,
                    label : {
                        show: true,
                        position: 'left',
                        formatter: '{b}'
                    }
                }},
                data:[5, 8, 2, 1, 2, 3, 2, 1, 1]
            }
        ]
    };
    myChart.setOption(option);
}

$(document).ready(init);