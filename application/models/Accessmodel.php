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
		$unit_id = $course_id;
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
}