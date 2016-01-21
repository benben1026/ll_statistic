<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AccessModel extends CI_Model{
	function __construct(){
		parent::__construct();
	}
	/*
	function checkGlobalAdminRight($user_id, $target_unit_id){
		$query = $this->db->query("SELECT unit.id, unit.pattern, unit.name FROM `role` LEFT JOIN `unit` ON unit.id=role.unit_id WHERE role.user_id=? AND role.active=1 AND role.admin_priv=1 ORDER BY unit.level DESC", array($user_id));
		$row = $query->result_array();
		if(empty($row)){
			return false;
		}

	}

	function grantGlobalAccess($unit_id, $user_id, $right){
		$query = $this->db->query("INSERT INTO `role`(unit_id, user_id, active, read_priv, write_priv, admin_priv) VALUES ?,?,?,?,?,?", array($unit_id, $user_id, 1, isset($right['read_priv']) ? $right['read_priv'] : 0, isset($right['wright_priv']) ? $right['wright_priv'] : 0, isset($right['admin_priv']) ? $right['admin_priv'] : 0,);
		return $query->result_array();
	}
	*/

	function getAllSubUnit($unit_id){

	}

	function checkSpecRights($user_id, $course_id, $right){
		$output;
		$query = $this->db->query("SELECT * FROM resource");
		$resources = $query->result_array();
		for($i = 0; $i < count($resources); $i++){
			$output[$resources[$i]['resource_type']] = false;
		}

		$query = $this->db->query("SELECT id FROM role WHERE user_id=?", array($user_id));
		$row = $query->result_array();
		if(empty($row)){
			return $output;
		}
		$sql = "SELECT * FROM role_has_priv_" . $right . " LEFT JOIN `resource` ON resource.id=resource_id WHERE course_id= " . $course_id;
		for($i = 0; $i < count($row) ; $i++){
			if($i == 0)
				$sql .= " AND (role_id=" . $row[$i]['id'];
			else
				$sql .= " OR role_id=" . $row[$i]['id'];
		}
		$sql .= ")";
		$query = $this->db->query($sql);
		$result = $query->result_array();
		if(empty($result))
			return $output;
		for($i = 0; $i < count($result); $i++){
			$output[$result[$i]['resource_type']] = true;
		}
		return $output;
	}

	//return 0 -> permission denied; 1 -> allow; 2 -> not found	
	function checkGlobalRight($user_id, $course_id, $right){
		//*use course id to get the related unit id*//
		//$unit_id = $course_id;
		$unit_id = 4;
		//******************************************//
		$query = $this->db->query("SELECT " . $right . "_priv FROM role WHERE unit_id=? AND user_id=? AND active=1;", array($unit_id, $user_id));
		//$query = $this->db->query("SELECT " . $right . "_priv FROM role WHERE unit_id=" . $unit_id . " AND user_id=" . $user_id . " AND active=1;");
		$row = $query->result_array();
		if(!empty($row) && $row[0][$right . "_priv"] == 1)
			return 1;
		else if(!empty($row) && $row[0][$right . "_priv"] == 0)
			return 0;

		$query = $this->db->query("SELECT pattern FROM unit WHERE id=?", array($unit_id));
		$row = $query->result_array();
		if(empty($row))
			return 2;
		$unit_id_list = explode("-", $row[0]['pattern']);
		for($i = 0; $i < count($unit_id_list); $i++){
			$query = $this->db->query("SELECT " . $right . "_priv FROM role WHERE unit_id=? AND user_id=? AND active=1", array($unit_id_list[$i], $user_id));
			$row = $query->result_array();
			if(!empty($row) && $row[0][$right . "_priv"] == 1)
				return 1;
			else if(!empty($row) && $row[0][$right . "_priv"] == 0)
				return 0;
		}
		return 2;
	}

	/*
	function getAllRights($userId){
		$query = $this->db->query("SELECT user.id, right.accessToken FROM `user` LEFT JOIN `role` ON user.id=role.userId LEFT JOIN `role_has_right` ON role_has_right.roleId=role.id LEFT JOIN `right` ON right.id=role_has_right.rightId WHERE user.id=?", array($userId));
		$row = $query->result_array();
		if(empty($row)){
			return false;
		}else{
			return $row;
		}
	}

	function checkRights($token, $userId){
		$rights = $this->getAllRights($userId);
		//return $rights;
		for($i = 0; $i < count($rights); $i++){
			if($rights[$i]['accessToken'] == $token)
				return true;
		}
		return false;
	}
	*/

	/***************************ADMIN***************************/
	function getRoles($user_id){
		$query = $this->db->query("SELECT role.id, unit.id AS unit_id, unit.name FROM role LEFT JOIN unit ON unit.id=role.unit_id WHERE active=1 AND role.user_id=" . $user_id);
		$rows = $query->result_array();
		// for($i = 0; $i < count($rows); $i++){

		// }
		return $rows;
	}

	function getAllUnits($user_id, $role_id){
		$query = $this->db->query("SELECT * FROM role LEFT JOIN unit ON role.unit_id=unit.id WHERE role.id=? AND role.user_id=?", array($role_id, $user_id));
		//$query = $this->db->query("SELECT * FROM unit WHERE id=?", array($unit_id));
		$rows = $query->result_array();
		if(empty($rows)){
			return array(
					'result' => false,
					'error' => 'Permission Denied',
				);
		}
		$query = $this->db->query("SELECT * FROM unit WHERE pattern LIKE '" . $rows[0]['pattern'] . "%' ORDER BY level");
		$rows = $query->result_array();
		return array(
				'result' => true,
				'data' => $rows,
			);
	}

	function getAllUsers($user_id, $role_id, $unit_id){
		$query = $this->db->query("SELECT * FROM role WHERE id=?", array($role_id));
		$role = $query->result_array();
		if(empty($role)){
			return array(
					'result' => false,
					'error' => 'Invalid Parameter',
				);
		}
		$query = $this->db->query("SELECT * FROM unit WHERE id=?", array($unit_id));
		$target_unit = $query->result_array();
		if(empty($target_unit)){
			return array(
					'result' => false,
					'error' => 'Invalid Parameter',
				);
		}
		$query = $this->db->query("SELECT * FROM unit WHERE id=" . $role[0]['unit_id']);
		$unit = $query->result_array();
		if(empty($unit)){
			return array(
					'result' => false,
					'error' => 'Invalid Parameter',
				);
		}

		$target_unit_pattern = $target_unit[0]['pattern'] . "-";
		$unit_pattern = $unit[0]['pattern'] . "-";

		if(strpos($target_unit_pattern, $unit_pattern) === false){
			print $unit_pattern . " | " . $target_unit_pattern;
			return array(
					'result' => false,
					'error' => 'You don\'t have the right to access the following content',
				);
		}

		$query = $this->db->query("SELECT user.name, role.id AS role_id, role.active, role.privilege, role.if_inherit FROM role LEFT JOIN user ON user.id = role.user_id WHERE unit_id=?", array($unit_id));
		$rows = $query->result_array();
		$this->load->helper('privilege_helper');
		$output = array();
		for($i = 0; $i < count($rows); $i++){
			$output[$i]['name'] = $rows[$i]['name'];
			$output[$i]['role_id'] = $rows[$i]['role_id'];
			$output[$i]['active'] = $rows[$i]['active'];
			$output[$i]['privilege'] = load_privilege_list($rows[$i]['privilege'], $rows[$i]['if_inherit']);
		}
		return array(
				'result' => true,
				'data' => $output,
			);
	}

	function updateGlobalPriv($user_id, $role_id, $target_role_id, $privilege){
		
		$this->load->helper('privilege_helper');
		$privilege_list = load_privilege();
		if(count($privilege) != count($privilege_list) + 1){
			return array(
					'result' => false,
					'error' => 'Invalid Privilege',
				);
		}
//print '3 ';

		$query = $this->db->query("SELECT * FROM role WHERE id=? AND user_id=?", array($role_id, $user_id));
		$rows = $query->result_array();
		if(empty($rows)){
			return array(
					'result' => false,
					'error' => 'Permission Denied',
				);
		}
		$unit_id = $rows[0]['unit_id'];

//print '4 ' . $unit_id;

		$query = $this->db->query("SELECT unit.pattern FROM role LEFT JOIN unit ON unit.id=role.unit_id WHERE role.id=?", array($target_role_id));
		$rows = $query->result_array();
		if(empty($rows)){
			return array(
					'result' => false,
					'error' => 'Invalid Role ID',
				);
		}
		$target_unit_pattern = $rows[0]['pattern'];
//print '5 '. $target_unit_pattern;

		$sql = "SELECT * FROM role LEFT JOIN unit ON unit.id = role.unit_id WHERE user_id=? AND ";
		$temp = $target_unit_pattern;
		$sql .= "(";
		$sql .= "unit.pattern='" . $temp . "'";
		while(true){
			$pos = strrpos($temp, "-");
			if($pos !== false){
				$sql .= " OR ";
			}else{
				break;
			}
			$temp = substr($temp, 0, $pos);
			$sql .= "unit.pattern='" . $temp . "'";
		}
		$sql .= ") ORDER BY level DESC";
//print $sql;
		$query = $this->db->query($sql, array($user_id));
		$rows = $query->result_array();
//print json_encode($rows);

		$flag = false;
		for($i = 0; $i < count($rows); $i++){
			$p_list = load_privilege_list($rows[$i]['privilege'], $rows[$i]['if_inherit']);
//print json_encode($p_list) . '\n';
			if($p_list['admin'] == 1){
				$flag = true;
				break;
			}else if($p_list['admin'] == -1){
				continue;
			}else{
				break;
			}
		}
		if(!$flag){
			return array(
					'result' => false,
					'error' => 'Permission Denied',
				);
		}

		if(update_privilege($privilege, $target_role_id)){
			return array(
					'result' => true,
				);
		}else{
			return array(
					'result' => false,
					'error' => 'Server Error',
				);
		}

	}
	/*************************END ADMIN*************************/

	/*************************** Privilege Process ***************************/
	function privProcess($priv_dec){
		$this->load->helper('privilege_helper');
		return load_privilege_list($priv_dec);
	}
	/************************* END Privilege Process *************************/
}