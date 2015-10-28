<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Access extends Acc_Controller{
	public function index(){
		print json_encode($this->user);
	}

	public function checkRight(){
		if($this->user == null){
			print 'please login';
			return;
		}
		$postData = $this->input->post();
		$this->load->model("accessmodel");
		if(!isset($postData['accesstoken'])){
			print 'Invalid Parameter';
			return;
		}
		$res = $this->accessmodel->checkRights($postData['accesstoken'], $this->user);
		//print json_encode($res);
		if($res){
			print 'OK';
		}else{
			print 'Access Denied';
		}
	} 
}