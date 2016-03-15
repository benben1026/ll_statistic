function send(para){
	$.ajax({
		url: 'http://learnlock.benjamin-zhou.com/public/api/v1/statements/aggregate?pipeline=' + para,
		type: 'GET',
		dataType: 'json',
		beforeSend: function (xhr) {
			xhr.setRequestHeader("Content-type", "application/json");
		    xhr.setRequestHeader("Authorization", "Basic ZGM4MTk1ZGUzZmM4NTQ5NzU2N2MzNDdlMzk4YmJiMWU2MjhlNTQxNjo1MjE2NTEzYzY0ZGY1ZjBlMDhiMmI3NTQwNDAxZGFiOGQzN2M5ODFk");
		    xhr.setRequestHeader("X-Experience-API-Version", "1.0.1");
		},
		success: function(data){
			console.log(JSON.stringify(data));
		}
	});
}

function sendRequest(para){
	$.ajax({
		url: 'http://benjamin-zhou.com/ll_statistic/index.php/learninglocker/getTestData/' + para,
		type: 'GET',
		dataType: 'html',
		success: function(data){
			console.log(JSON.stringify(data));
		}
	});
}

function test(){
	var para = '[{"$match": {"statement.timestamp": {"$gt":"2016-01-01T00:00","$lt":"2016-03-01T00:00"}}}]';
	sendRequest(encodeURI(para));
}

function test2(){
	var para = [{
		"$match": {
			"statement.verb.id": {
				"$eq": "http://adlnet.gov/expapi/verbs/interacted"
			}
		}
	}];
	send(JSON.stringify(para));
}

function test3(){
	var para = [{
		"$match": {
				"statement.actor.name": {
					"$eq": "stud01"
				},
				"statement.verb.id": {
					"$eq": "http://id.tincanapi.com/verb/viewed"
				}
		}
	},{
		"$project": {
			"actor": 0
		}
	}];

	para = 'asdf';
	//console.log(JSON.stringify(para));
	//console.log(encodeURI(JSON.stringify(para)));
	sendRequest(JSON.stringify(para));
}