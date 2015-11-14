<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Access extends Acc_Controller{
	private $error_info;
	private $global_right;
	private $spec_right;

	public function index(){
		print json_encode($this->user);
	}

	public function getData($course_id, $action, $resource){
		$output;
		$res = $this->checkRight($course_id, $action);
		if($res && $this->global_right == 1){
			$outut['status'] = true;
			$output['scope'] = 'all';
		}else if($res){
			$outut['status'] = true;
			$output['scope'] = 'partial';
			$output['data'] = $this->spec_right;
		}else{
			$output['status'] = false;
			$output['error_info'] = $this->error_info;
		}
		print json_encode($output);

	}

	private function checkRight($course_id, $action){
		if($this->user == null){
			$this->error_info = "Please Login";
			return false;
		}
		print $this->user;
		$action = trim(strtolower($action));
		if($action != "read" && $action != "write" && $action != "admin"){
			$this->error_info = "Invalid Parameter";
			return false;
		}

		$this->load->model("accessmodel");
		$this->global_right = $this->accessmodel->checkGlobalRight($this->user, $course_id, $action);
		if($this->global_right == 1)
			return true;
		$this->spec_right = $this->accessmodel->checkSpecRights($this->user, $course_id, $action);
			return true;
	}

	// public function checkRight(){
	// 	if($this->user == null){
	// 		print 'please login';
	// 		return;
	// 	}
	// 	$postData = $this->input->post();
	// 	$this->load->model("accessmodel");
	// 	if(!isset($postData['accesstoken'])){
	// 		print 'Invalid Parameter';
	// 		return;
	// 	}
	// 	$res = $this->accessmodel->checkRights($postData['accesstoken'], $this->user);
	// 	//print json_encode($res);
	// 	if($res){
	// 		print 'OK';
	// 	}else{
	// 		print 'Access Denied';
	// 	}
	// } 
		// }
}