var global_role_id;
var global_unit_id;

function getRoleList(){
	$('#back-btn').hide();
	$('.admin-content').hide();
	$("#admin-role").show();
	var url = "http://benjamin-zhou.com/ll_statistic/index.php/access/getRoleList";
	$.ajax({
		url: url,
		type: 'GET',
		dataType: 'json',
		success: function(data){
			if(data.length == 0){
				alert('You don\'t have any active roles');
				return;
			}
			$('#admin-role table tbody').html('');
			for(var i = 0; i < data.length; i++){
				var button = '<button class="btn btn-primary" onclick="getAllSubUnits(' + data[i]['id'] + ')">Select</button>';
				var tr = $('<tr><td>' + (i + 1) + '</td>' + '<td>' + data[i]['name'] + '</td>' + '<td>' + button + '</td></tr>');
				$('#admin-role table tbody').append(tr);
			}
		},
		error: function(){
			alert('Server Error');
			return;
		}
	});
}

function getAllSubUnits(role_id){
	$('.admin-content').hide();
	$("#admin-subUnits").show();
	$('#back-btn').attr('onclick', 'getRoleList()');
	$('#back-btn').show();
	global_role_id = role_id;
	var url = "http://benjamin-zhou.com/ll_statistic/index.php/access/getAllUnits/" + role_id;
	$.ajax({
		url: url,
		type: 'GET',
		dataType: 'json',
		success: function(data){
			if(!data['result']){
				alert(data['error']);
				return;
			}
			$('#admin-subUnits table tbody').html('');
			for(var i = 0; i < data['data'].length; i++){
				var button = '<button class="btn btn-primary" onclick="getAllUsers(' + data['data'][i]['id'] + ')">Select</button>';
				var tr = $('<tr><td>' + (i + 1) + '</td>' + '<td>' + data['data'][i]['name'] + '</td>' + '<td>' + button + '</td></tr>');
				$('#admin-subUnits table tbody').append(tr);
			}
		},
		error: function(){
			alert('Server Error');
			return;
		}
	});
}

function getAllUsers(unit_id){
	$('.admin-content').hide();
	$("#admin-user").show();
	$('#back-btn').attr('onclick', 'getAllSubUnits(' + global_role_id + ')')
	global_unit_id = unit_id;
	var url = "http://benjamin-zhou.com/ll_statistic/index.php/access/getAllUsers/" + global_role_id + "/" + global_unit_id;
	$.ajax({
		url: url,
		type: 'GET',
		dataType: 'json',
		success: function(data){
			if(!data['result']){
				alert(data['error']);
				return;
			}
			var thead = "<tr><th>#</th><th>User Name</th><th>Active</th>";
			for(var key in data['data'][0]['privilege'])
				thead += "<th>" + key + "</th>";
			thead += "<th>Edit</th></tr>";
			$('#admin-user table thead').html('');
			$('#admin-user table thead').append(thead);
			$('#admin-user table tbody').html('');
			for(var i = 0; i < data['data'].length; i++){
				var tr = '<tr id="user-list-' + data['data'][i]['role_id'] + '"><td>' + (i + 1) + '</td><td>' + data['data'][i]['name'] + '</td>';
				tr += '<td><select class="form-control"><option' + (data['data'][i]['active'] == 1 ? " selected" : "") + ' value="1">Active</option><option' + (data['data'][i]['active'] == 0 ? " selected" : "") + ' value="0">Block</option></select></td>';
				for(var key in data['data'][i]['privilege']){
					tr += '<td><select class="form-control"><option' + (data['data'][i]['privilege'][key] == 1 ? " selected" : "") + ' value="1">Grant</option><option' + (data['data'][i]['privilege'][key] == 0 ? " selected" : "") + ' value="0">Deny</option><option' + (data['data'][i]['privilege'][key] == -1 ? " selected" : "") + ' value="-1">Inherit</option></select></td>';
				}
				tr += '<td><button class="btn btn-primary" onclick="updatePriv(' + data['data'][i]['role_id'] + ')">Confirm</button></td></tr>';
				$('#admin-user table tbody').append(tr);
			}

		},
		error: function(){
			alert('Server Error');
			return;
		}
	});
}

function updatePriv(target_role_id){
	var url = 'http://benjamin-zhou.com/ll_statistic/index.php/access/updateGlobalPrivilege';
	var p = $('#user-list-' + target_role_id + ' select');
	var privilege = [];
	for(var i = 0; i < p.length; i++){
		privilege.push(p[i].value);
	}
	$.ajax({
		url: url,
		type: 'POST',
		dataType: 'json',
		data: 'role_id=' + global_role_id + '&target_role_id=' + target_role_id + '&privilege=' + JSON.stringify(privilege),
		success: function(data){
			if(data['result']){
				alert("Update Successfully");
			}else{
				alert(data['error']);
				getAllUsers(global_unit_id);
			}
		},
		error: function(){
			alert('Sever Error, Please Try Later');
			getAllUsers(global_unit_id);
		}
	});
}

$(document).ready(getRoleList);