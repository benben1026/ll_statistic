<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
	public function index(){

	}

	public function checkLogin(){
		$postData = $this->input->post();
		$this->load->model("loginmodel");
		if(isset($postData['email'], $postData['pwd'])){
			$result = $this->loginmodel->checkLogin($postData['email'], $postData['pwd']);
		}else{
			print 'Login Failed Because of Invalid Parameters';
			return;
		}
		if($result == 'success'){
			print 'login success';
		}else{
			print $result;
		}
	}
}