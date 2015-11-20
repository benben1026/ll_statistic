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
                tickSize: [1, "day"],
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

function clickSubmit(fun){
    var url = 'http://benjamin-zhou.com/ll_statistic/index.php/access/get' + fun + '/' +  $('#dateYFrom').val() + '/' +  $('#dateMFrom').val() + '/' +  $('#dateDFrom').val() + '/' +  $('#dateYTo').val() + '/' +  $('#dateMTo').val() + '/' +  $('#dateDTo').val();
    console.log(url);
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        async: false,
        success: function(data){
            if(data['ok'] == 1){
                generateDataSet(data['result'], data['label']);
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
    dataset.push({'label': label, 'data': date});


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
    clickSubmit('Login');
    clickSubmit('LecMat');
    clickSubmit('Assessment');
    clickSubmit('Forum');
    setPlot();
    plotAccordingToChoices();
}

$(window).load(init);