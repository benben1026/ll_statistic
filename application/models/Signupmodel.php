<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SignupModel extends CI_Model{
    public $error_info;

	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    function getUnitList(){
        $query = $this->db->query("SELECT * FROM unit ORDER BY level");
        $row = $query->result_array();
        return $row;
    }

    function signup($email, $password, $name, $unit_id){
    	$query = $this->db->query("SELECT * FROM user WHERE email=?", array($email));
    	$row = $query->result_array();
    	if(!empty($row)){
            $this->error_info = "Email Address Exists";
    		return false;
    	}
        $query = $this->db->query("SELECT * FROM unit WHERE id=?", array($unit_id));
        $row = $query->result_array();
        if(empty($row)){
            $this->error_info = "Invalid Unit Id";
            return false;
        }
    	$query = $this->db->query("INSERT INTO user(email, password, name) VALUES (?, ?, ?)", array($email, md5(md5($password)), $name));
    	if(!$query){
            $this->error_info = "Fail To Create User Info";
            return false;
        }
        $user_id = $this->db->insert_id();
        $query = $this->db->query("INSERT INTO role(user_id, unit_id, active, read_priv, write_priv, admin_priv) VALUES (?,?,?,?,?,?)", array($user_id, $unit_id, 1, 0, 0, 0));
        if(!$query){
            $this->error_info = "Fail To Create Role Info";
            return false;
        }
        return true;
    }
}