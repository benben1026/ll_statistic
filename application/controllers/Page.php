<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page extends CI_Controller{

	function __construct(){
		parent::__construct();
	}

	public function landing(){
		$this->load->model('apimodel');
		if($this->apimodel->getAccessGranted()){
			redirect('/page/overview');
			return;
		}
		//$this->load->view('template/landing_header');
		$this->load->view('landing');
		//$this->load->view('template/footer');
	}

	public function overview(){
		$this->load->model('apimodel');
		if(!$this->apimodel->getAccessGranted()){
			redirect('/Saml2Controller/login');
			return;
		}
		if(!$this->apimodel->getCourseInfo()['ok']){
			printJson(array('ok' => false, 'message' => $this->apimodel->getMessage(), 'data' => null));
			return;
		}
		foreach($this->apimodel->getCourseInfo()['data'] as $lrs => $result){
			for($i = 0; array_key_exists('total_results', $this->apimodel->getCourseInfo()['data'][$lrs]) && $i < (int)$this->apimodel->getCourseInfo()['data'][$lrs]['total_results']; $i++){
				if($this->apimodel->getCourseInfo()['data'][$lrs]['results'][$i]['role_name'] != "student"){
					$this->overviewTea();
					return;
				}
			}
		}
		$this->overviewStu();
		return;
	}

	public function overviewTea(){
		$this->load->model('apimodel');
		if(!$this->apimodel->getAccessGranted()){
			redirect('/Saml2Controller/login');
			return;
		}
		$this->load->view('template/header', array('title' => 'KEEPER | Overview', 'firstname' => $this->session->userdata('samlUserData')['firstname'][0]));
		$this->load->view('tea_overview', array('role' => 'teacher'));
		$this->load->view('template/footer');
	}

	public function overviewStu(){
		$this->load->model('apimodel');
		if(!$this->apimodel->getAccessGranted()){
			redirect('/Saml2Controller/login');
			return;
		}
		$this->load->view('template/header', array('title' => 'KEEPER | Overview', 'firstname' => $this->session->userdata('samlUserData')['firstname'][0]));
		$this->load->view('stu_overview', array('role' => 'student'));
		$this->load->view('template/footer');
	}

	public function courseDetail(){
		$this->load->model('apimodel');
		if(!$this->apimodel->getAccessGranted()){
			redirect('/Saml2Controller/login');
			return;
		}
		$this->apimodel->setPlatform($this->input->get('platform'));
		$this->apimodel->setCourseId($this->input->get('courseId'));
		if(!$this->apimodel->getValidParameter()){
			printJson(array('ok' => false, 'message' => $this->apimodel->getMessage(), 'data' => null));
			return;
		}
		$courseInfoData = $this->apimodel->getCourseInfo()['data'];
		if(isset($courseInfoData[$this->apimodel->getPlatform()])){
			foreach($courseInfoData[$this->apimodel->getPlatform()]['results'] as $course){
				if($course['course_id'] == $this->apimodel->getCourseId()){
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
		printJson(array('ok' => false, 'message' => 'You do not have access to this course', 'data' => null));
		return;
	}
}
