<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once (dirname(__FILE__) . "/CourseInfo.php");

class Page extends CourseInfo{
	private $courseInfo;

	function __construct(){
		parent::__construct();
		$this->courseInfo = $this->getUserCourseList("all");
	}

	public function overviewTea(){
		$this->load->view('template/header', array('title' => 'Overview', 'firstname' => $this->session->userdata('samlUserData')['firstname'][0]));
		$this->load->view('tea_overview', array('role' => 'teacher'));
		$this->load->view('template/footer');
	}

	public function overviewStu(){
		$this->load->view('template/header', array('title' => 'Overview', 'firstname' => $this->session->userdata('samlUserData')['firstname'][0]));
		$this->load->view('stu_overview', array('role' => 'student'));
		$this->load->view('template/footer');
	}

	public function courseDetail(){
		$courseId = $this->input->get('courseId');
		$platform = $this->input->get('platform');
		//temporarily disable course accessable check --TO MODIFY
		// if(isset($this->courseInfo['data'][$platform])){
		// 	foreach($this->courseInfo['data'][$platform]['results'] as $course){
		// 		if($course['course_id'] == $courseId){
		// 			$this->load->view('template/header', array('title' => 'Course Detail', 'firstname' => $this->session->userdata('samlUserData')['firstname'][0]));
		// 			if($course['role_name'] == 'student'){
		// 				$this->load->view('stu_course_detail', array('course_name' => $course['course_name'], ));
		// 			}else{
		// 				$this->load->view('tea_course_detail');
		// 			}
		// 			$this->load->view('template/footer');
		// 			return;
		// 		}
		// 	}
		// }
		//echo 'You Do Not Have the Access To This Course';
		$this->load->view('template/header', array('title' => 'Course Detail', 'firstname' => $this->session->userdata('samlUserData')['firstname'][0]));
		$this->load->view('stu_course_detail', array('course_name' => 'NEWCOURSE3', ));
		$this->load->view('template/footer');
		return;
	}
}