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
		// return array(
		// 	"ok" => true,
		// 	"message" => "",
		// 	"moodle" => array(
		// 		"error" => array("code" => "404", "message" => "Not Found"),
		// 	),
		// 	"edx" => array(
		// 		"total_results" => "2",
		// 		"results" => array(
		// 			array(
		// 				"course_id" => "course-v1:cuhk+csci2100a+2015_2",
		// 				"course_name" => "Data Structures",
		// 				"role_name" => "instructor"
		// 			),
		// 			array(
		// 				"course_id" => "course-v1:keep+guide03+2015_1",
		// 				"course_name" => "KEEP Open edX Course Management",
		// 				"role_name" => "student"
		// 			)
		// 		),
		// 	),
		// );
		
		//this id should be return from the login information -- TO MODIFY
		//demo account
		//$keepId = "563a82e2-96ed-11e4-bf37-080027087aa9";
		//teacher account
		//$keepId = "fb9de522-167c-4444-98c3-d56742e53814";
		
		//production account (instructor of data structure on edx)
		$keepId = "e9fed8e0-cfcc-11e4-8b2a-080027087aa9";


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