<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Acc_Controller extends CI_Controller {
    protected $user = array();

    function __construct(){
    	parent::__construct();

    	$this->user['id'] = $this->session->userdata('userId');
    	$this->user['name'] = $this->session->userdata('username');
    	//$this->$user['id'] = $_SESSION['userId'];
    	//$this->$user['name'] = $_SESSION['username'];
    }
}