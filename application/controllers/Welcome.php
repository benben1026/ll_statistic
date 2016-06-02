<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('apimodel', 'api');
    }

	public function landing(){
		
		if($this->isAccessGranted()){
			redirect('/page/overview');
			return;
		}
		
		$this->load->view('landing');		
	}

	public function overview(){
		// TODO: Duplicate		
		if(!$this->isAccessGranted()){
			redirect('/Saml2Controller/login');
			return;
		}
		if(!$this->api->getCourseInfo()['ok']){
			printJson(array('ok' => false, 'message' => $this->api->getMessage(), 'data' => null));
			return;
		}

		$courseInfoData = $this->api->getCourseInfo()['data'];
		foreach($courseInfoData as $lrs => $result){
			for($i = 0; array_key_exists('total_results', $courseInfoData[$lrs]) && $i < (int)$courseInfoData[$lrs]['total_results']; $i++){
				if($courseInfoData[$lrs]['results'][$i]['role_name'] != "student"){
					$this->loadOverview('teacher');
					return;
				}
			}
		}
		$this->loadOverview('student');
		return;
	}

	public function courseDetail(){
		
		if(!$this->isAccessGranted()){
			redirect('/Saml2Controller/login');
			return;
		}
		$this->api->setPlatform($this->input->get('platform'));
		$this->api->setCourseId($this->input->get('courseId'));
		if(!$this->api->getValidParameter()){
			printJson(array('ok' => false, 'message' => $this->api->getMessage(), 'data' => null));
			return;
		}
		$courseInfoData = $this->api->getCourseInfo()['data'];
		if(isset($courseInfoData[$this->api->getPlatform()])){
			foreach($courseInfoData[$this->api->getPlatform()]['results'] as $course){
				if($course['course_id'] == $this->api->getCourseId()){

					$this->load->view('course_detail', array('role' => $course['role_name'],'title' => $course['course_name'], 'firstname' => $this->session->userdata('samlUserData')['firstname'][0]));

					return;
				}
			}
		}
		printJson(array('ok' => false, 'message' => 'You do not have access to this course', 'data' => null));
		return;
	}

	public function teaViewStu(){		
		
		if(!$this->isAccessGranted()){
			redirect('/Saml2Controller/login');
			return;
		}
		$this->load->view('template/header_include');
		$this->load->view('teastu_course_detail');
	}

	private function loadOverview($role){
		$this->load->view(	'overview', 
					array(	'title' => 'KEEPDashboard | Overview',
							'firstname' => $this->session->userdata('samlUserData')['firstname'][0],
							'role' => $role));
	}

	private function isAccessGranted(){		
		return $this->api->getAccessGranted();		
	}


}