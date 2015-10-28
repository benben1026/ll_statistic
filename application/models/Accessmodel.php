<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AccessModel extends CI_Model{
	function __construct(){
		parent::__construct();
	}

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
}