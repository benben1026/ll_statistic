<form method="POST" action="http://benjamin-zhou.com/ll_statistic/index.php/Signup/processSignup">
	Email <input type="text" name="email" /> <br/>
	Password <input type="password" name="pwd" /><br/>
	Name <input type="text" name="name" /><br/>
	Units <select name="unitId">
	<?php
		for($i = 0; $i < count($units); $i++){
			echo "<option value=\"" . $units[$i]['id'] . "\">" . $units[$i]['name'] . "</option>";
		}
	?>
	</select>
	<br/>
	<input type="submit" />
</form>