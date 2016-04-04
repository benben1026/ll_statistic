<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CourseInfo extends CI_Controller{
	function __construct(){
		parent::__construct();
		$this->load->model('courseinfomodel');
	}

	public function index($lrs_para = "all"){
		$result = $this->getUserCourseList($lrs_para);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($result));
	}

	protected function getUserCourseList($lrs_para){
		//this id should be return from the login information
		$keepId = "563a82e2-96ed-11e4-bf37-080027087aa9";
		$sessData = $this->checkCourseInfoSession();
		if($sessData !== false){
			return $sessData;
		}
		$lrs = strtolower($lrs_para);
		$url_para = "/user/" . $keepId;
		if($lrs == "all"){
			$url_list = array(
				"moodle" => $url_para,
				"edx" => $url_para
			);
		}else{
			$url_list = array(
				$lrs => $url_para,
			);
		}
		$output = $this->courseinfomodel->getData($url_list);
		if($lrs == "all" && $output['ok']){
			$session_data = array(
				"courseInfo" => $output,
				"courseInfoSessExp" => time(),
			);
			$this->session->set_userdata($session_data);
		}
		
		return $output;
	}

	private function checkCourseInfoSession(){
		$preTime = $this->session->userdata('courseInfoSessExp');
		if($preTime == null || time() - $preTime > 300){
			return false;
		}else{
			return $this->session->userdata('courseInfo');
		}
	}

}