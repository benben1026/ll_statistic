<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CourseInfo extends CI_Controller{
	function __construct(){
		parent::__construct();
		$this->load->model('courseinfomodel');
	}

	public function test(){
		$url_list = array(
			'moodle' => '/',
			'edx' => '/',
		);
		$result = $this->courseinfomodel->getData($url_list);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($result));
	}

	public function index($lrs_para = "all"){
		$result = $this->getUserCourseList($lrs_para);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($result));
	}

	protected function getUserCourseList($lrs_para){		
//KEEP ID 
		//this id should be return from the login information -- TO MODIFY
		//demo account
		//$keepId = "563a82e2-96ed-11e4-bf37-080027087aa9";
		//teacher account
		//$keepId = "fb9de522-167c-4444-98c3-d56742e53814";
		
		//production account (instructor of data structure on edx)
		//$keepId = "e9fed8e0-cfcc-11e4-8b2a-080027087aa9";

		//production account (student on moodle)
		//$keepId = "990600a6-fc56-48d0-b7d0-f72d73390a26";

		//$keepId = "990600a6-fc56-48d0-b7d0-f72d73390a26";
		//$keepId = "59f8bbbf-9f17-48bd-92e0-9a44cca76f5f";
		$keepId = $this->session->userdata('samlUserData')['keepid'][0];
		if($keepId == null){
			return;
		}
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