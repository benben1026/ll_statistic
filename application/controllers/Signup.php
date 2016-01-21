<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Signup extends CI_Controller {
	public function index(){
		$this->load->model("signupmodel");
		$data['units'] = $this->signupmodel->getUnitList();
		$data['title'] = "Sign Up";
		$this->load->view('template/header', $data);
		$this->load->view('signup', $data);
		$this->load->view('template/footer');
	}

	public function getUnitList(){
		$this->load->model("signupmodel");
		print json_encode($this->signupmodel->getUnitList());
	}

	public function processSignup(){
		$this->load->model("signupmodel");
		$postData = $this->input->post();
		//print json_encode($postData);
		if(isset($postData['email'], $postData['pwd'], $postData['name'], $postData['unitId'])){
			$result = $this->signupmodel->signup($postData['email'], $postData['pwd'], $postData['name'], $postData['unitId']);
		}else{
			print 'fail (Invalid Parameter)';
			$t['target'] = base_url() . "index.php/signup";
			$this->load->view('jump', $t);
			return;
		}
		if($result){
			print 'success';
			$this->load->model("loginmodel");
			$this->loginmodel->checkLogin($postData['email'], $postData['pwd']);
			$t['target'] = base_url() . "index.php/access/home";
			$this->load->view('jump', $t);
		}else{
			print "fail: " . $this->signupmodel->error_info;
		}
	}
}