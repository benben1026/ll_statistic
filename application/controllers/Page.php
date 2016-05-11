<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include_once (dirname(__FILE__) . "/CourseInfo.php");

class Page extends CourseInfo{
	private $courseInfo;

	function __construct(){
		parent::__construct();
		$this->courseInfo = $this->getUserCourseList("all");
	}

	public function landing(){
		if($this->session->userdata('samlUserData')['login'][0] != null){
			redirect('/page/overview');
			return;
		}
		//$this->load->view('template/landing_header');
		$this->load->view('landing');
		//$this->load->view('template/footer');
	}

	public function overview(){
		if($this->session->userdata('samlUserData')['login'][0] == null){
			redirect('/Saml2Controller/login');
			return;
		}
		if(!$this->courseInfo['ok']){
			echo 'Sever Temporarily Not Available';
			return;
		}
		foreach($this->courseInfo['data'] as $lrs => $result){
			for($i = 0; array_key_exists('total_results', $this->courseInfo['data'][$lrs]) && $i < (int)$this->courseInfo['data'][$lrs]['total_results']; $i++){
				if($this->courseInfo['data'][$lrs]['results'][$i]['role_name'] != "student"){
					$this->overviewTea();
					return;
				}
			}
		}
		$this->overviewStu();
		return;
	}

	public function overviewTea(){
		if($this->session->userdata('samlUserData')['login'][0] == null){
			redirect('/Saml2Controller/login');
			return;
		}
		$this->load->view('template/header', array('title' => 'KEEPER | Overview', 'firstname' => $this->session->userdata('samlUserData')['firstname'][0]));
		$this->load->view('tea_overview', array('role' => 'teacher'));
		$this->load->view('template/footer');
	}

	public function overviewStu(){
		if($this->session->userdata('samlUserData')['login'][0] == null){
			redirect('/Saml2Controller/login');
			return;
		}
		$this->load->view('template/header', array('title' => 'KEEPER | Overview', 'firstname' => $this->session->userdata('samlUserData')['firstname'][0]));
		$this->load->view('stu_overview', array('role' => 'student'));
		$this->load->view('template/footer');
	}

	public function courseDetail(){
		$courseId = str_replace(" ", "+", $this->input->get('courseId'));
		$platform = $this->input->get('platform');
		//temporarily disable course accessable check --TO MODIFY
		if(isset($this->courseInfo['data'][$platform])){
			foreach($this->courseInfo['data'][$platform]['results'] as $course){
				if($course['course_id'] == $courseId){
					$this->load->view('template/header', array('title' => 'Course Detail', 'firstname' => $this->session->userdata('samlUserData')['firstname'][0]));
					if($course['role_name'] == 'student'){
						$this->load->view('stu_course_detail', array('course_name' => $course['course_name'], ));
					}else{
						$this->load->view('tea_course_detail', array('course_name' => $course['course_name'], ));
					}
					$this->load->view('template/footer');
					return;
				}
			}
		}
		echo 'You Do Not Have the Access To This Course';
		// $this->load->view('template/header', array('title' => 'Course Detail', 'firstname' => $this->session->userdata('samlUserData')['firstname'][0]));
		// $this->load->view('stu_course_detail', array('course_name' => 'NEWCOURSE3', ));
		// $this->load->view('template/footer');
		return;
	}

	public function teaViewStu(){
		$this->load->view('template/header_include');
		$this->load->view('teastu_course_detail');
	}
}