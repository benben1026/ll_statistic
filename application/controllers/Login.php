<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends Acc_Controller {
	public function index(){
		if($this->user['id'] != null){
			$t['target'] = base_url() . "index.php/access/home";
			$this->load->view('jump', $t);
			return;
		}
		$data['title'] = "Please Login";
		$this->load->view('template/header', $data);
		$this->load->view('signin');
		$this->load->view('template/footer');
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
			$t['target'] = base_url() . "/index.php/access/home";
			$this->load->view('jump', $t);
		}else{
			print $result;
		}
	}
}