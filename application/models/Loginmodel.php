<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class LoginModel extends CI_Model{
	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    function checkLogin($email, $password){
    	$query = $this->db->query("SELECT * FROM user WHERE email=?", array($email));
    	$row = $query->result_array();
    	if(empty($row) || $row[0]['password'] != md5(md5($password))){
    		if(empty($row)){
    			return 'email not exist';
    		}
    		return 'password incorrect';
    	}
		$data = array(
			'userId' => $row[0]['id'],
			'username' => $row[0]['name'],
		);
		$this->session->set_userdata($data);
		return 'success';
    }

    function logout(){
		$this->session->sess_destroy();
	}
}