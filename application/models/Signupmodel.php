<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SignupModel extends CI_Model{
	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    function signup($email, $password){
    	$query = $this->db->query("SELECT * FROM user WHERE email=?", array($email));
    	$row = $query->result_array();
    	if(!empty($row)){
    		return false;
    	}
    	$query = $this->db->query("INSERT INTO user(email, password) VALUES (?, ?)", array($email, md5(md5($password))));
    	return $query;
    }
}