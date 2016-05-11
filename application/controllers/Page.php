<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page extends CI_Controller{
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
		$this->load->model('apimodel');
		$this->apimodel->setPlatform($this->input->get('platform'));
		$this->apimodel->setCourseId($this->input->get('courseId'));
		if(!$this->apimodel->getValidParameter()){
			printJson(array('ok' => false, 'message' => $this->apimodel->getMessage(), 'data' => null));
			return;
		}
		$courseInfoData = $this->apimodel->getCourseInfo()['data'];
		if(isset($courseInfoData[$this->apimode->getPlatform()])){
			foreach($courseInfoData[$this->apimode->getPlatform()]['results'] as $course){
				echo $courseInfoData[$this->apimode->getPlatform()]['results'][0]['course_id'];
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
		printJson(array('ok' => false, 'message' => 'You do not have access to this course', 'data' => null));
		printJson($this->returnData);
		return;
}
}
