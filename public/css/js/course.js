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
/*
$(document).ready(function () {
    $.plot($("#flot-placeholder"), dataset, options);
});
*/
function gd(year, month, day) {
    return new Date(year, month, day).getTime();
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
    var url = 'http://benjamin-zhou.com/ll_statistic/index.php/access/getCourseInfo/' +  $('#courseId').val() + '/' + $('#datepicker_from').val() + '/' +  $('#datepicker_to').val();
    console.log(url);
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function(data){
            if(data['ok'] == 1){
                generateDataSet(data['data']['Login'], 'Login');
                generateDataSet(data['data']['Forum'], 'Forum');
                generateDataSet(data['data']['Assessment'], 'Assessment');
                generateDataSet(data['data']['Lecture Material'], 'Lecture Material');
                setPlot();
                plotAccordingToChoices();
            }else{
                alert(data['error']);
            }
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
    if(data.length == 0){
        return;
    }
    var date = [];
    for(var i = 0; i < data.length; i++){
        var t = [];
        t.push(getDate(data[i]['_id']['date']));
        t.push(data[i]['sum']);
        date.push(t);
    }
    dataset.push({'label': label + '&nbsp&nbsp', 'data': date});


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
    clickSubmit();
}

$(document).ready(init);
