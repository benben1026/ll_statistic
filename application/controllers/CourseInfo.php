<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CourseInfo extends CI_Controller{
	function __construct(){
		parent::__construct();
		$this->load->model('courseinfomodel');
	}

	public function index(){
		echo 'test';
	}

	public function getUserCourseList($keepId, $lrs_para = "all"){
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
//echo json_encode($url_list);
		echo json_encode($this->courseinfomodel->getData($url_list));
	}

}