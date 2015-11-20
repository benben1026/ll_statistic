<script type="text/javascript" src="<?= base_url() ?>/public/js/jquery.js"></script>

<form method="POST" action="http://benjamin-zhou.com/ll_statistic/index.php/Signup/processSignup">
	Email <input type="text" name="email" /> <br/>
	Password <input type="password" name="pwd" /><br/>
	Name <input type="text" name="name" /><br/>
	<input type="hidden" id="unitId" name="unitId" />
	<div id="unitTag"></div>
	<select id="unitSelect" onchange="selectOnchange()"></select>
	<!-- Units <select name="unitId">
	<?php
		//for($i = 0; $i < count($units); $i++){
		//	echo "<option value=\"" . $units[$i]['id'] . "\">" . $units[$i]['name'] . "</option>";
		//}
	?>
	</select> -->
	<br/>
	<input type="submit" />
</form>

<script type="text/javascript">
	var dataset;
	var prefix;
	var level;
	function getUnitList(){
		$.ajax({
			url: 'http://benjamin-zhou.com/ll_statistic/index.php/signup/getUnitList',
			type: 'GET',
			dataType: 'json',
			success: function(data){
				dataset = data;
				prefix = data[0]['pattern'] + '-';
				level = data[0]['level'];
				generateSelect();
			},
			error: function(){

			}
		});
	}

	function generateSelect(){
		var select = $('#unitSelect');
		select.html('');
		select.append('<option value="" disabled selected>Select Unit</option>');
		for(var i = 0; i < dataset.length; i++){
			if(dataset[i]['pattern'].slice(0, prefix.length) != prefix || dataset[i]['level'] != parseInt(level) + 1){
				continue;
			}
			select.append($('<option value="' + dataset[i]['id'] + '">' + dataset[i]['name'] + '</option>'));
		}
		if(select.html() == '<option value="" disabled="" selected="">Select Unit</option>'){
			select.hide();
		}else{
			select.show();
		}

	}

	function selectOnchange(inputId){
		var id = inputId ? inputId : $('#unitSelect').val();
		var span;
		$('#unitId').val(id);
		for(var i = 0; i < dataset.length; i++){
			if(dataset[i]['id'] == id){
				level = dataset[i]['level'];
				prefix = dataset[i]['pattern'] + '-';
				span = $('<span onclick="spanOnclick(this)">->' + dataset[i]['name'] + '</span>');
				break;
			}
		}
		for(var i = 0; i < dataset.length; i++){
			if(dataset[i]['pattern'] ==  prefix.slice(0, prefix.substr(0, prefix.length - 1).lastIndexOf("-"))){
				span.attr("id", dataset[i]['id']);
				break;
			}
		}
		if(span.html() != '-&gt;' + dataset[0]['name']){
			$('#unitTag').append(span);
		}
		generateSelect();
	}

	function spanOnclick(event){
		var id = parseInt($(event).attr('id'));
		$(event).prev().remove();
		$(event).nextAll().addBack().remove();
		selectOnchange(id);
	}

	$(window).load(getUnitList);
</script>