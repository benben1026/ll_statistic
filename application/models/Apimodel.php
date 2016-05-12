<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ApiModel extends CI_Model{
	//the following variables will be provided by user
	private $role;
	private $courseId;
	private $platform;
	private $fromDate;
	private $toDate;

	//the following variables will be initialized or set by backend
	private $keepId;
	private $courseInfo;
	private $accessGranted;
	private $validParameter = true;
	private $message;

	function __construct(){
		parent::__construct();
		$this->keepId = $this->session->userdata('samlUserData')['keepid'][0];
		if($this->keepId == null){
			$this->accessGranted = false;
			$this->message = "Please Login";
			return;
		}else{
			$this->accessGranted = true;
		}

		// MARK: Set fake course info for testing purpose
		$this->courseInfo = array(
			'ok' => true,
			'message' => '',
			'data' => array(
				// for fake student studentone
				'moodle' => array(
					'total_results' => "3",
					'results' => array(
						array(
							'course_id' => '128',
							'course_name' => 'New Course 99',
							'role_name' => 'teacher'
						),
						array(
							'course_id' => '95',
							'course_name' => 'NEWCOURSE3',
							'role_name' => 'teacher'
						),
						array(
							'course_id' => '128',
							'course_name' => 'nc99',
							'role_name' => 'teacher'
						)
					)
				),
				'edx' => array(
					'total_results' => "2",
					'results' => array(
						array(
							'course_id' => 'course-v1:cuhk+cuhkmit001+cuhkmitjoint',
							'course_name' => 'CUHK-MIT Joint Workshop on E-Learning and Big Data',
							'role_name' => 'student'
						),
						array(
							'course_id' => 'course-v1:keep+eval11+2016_1',
							'course_name' => ' 2016 TEST COURSE #11',
							'role_name' => 'student'
						),
					)
				)
			)
		);
		/*
		$this->load->model('courseinfomodel');
		$preTime = $this->session->userdata('courseInfoSessExp');
		$id = $this->session->userdata('courseInfoId');
		if($id == null || $id != $this->keepId || $preTime == null || time() - $preTime > 300){
			$url_list = array(
				"moodle" => "/user/" . $this->keepId,
				"edx" => "/user/" . $this->keepId
			);
			$output = $this->courseinfomodel->getData($url_list);
			if($output['ok']){
				$this->courseInfo = $output;
				$session_data = array(
					"courseInfoId" => $this->keepId,
					"courseInfo" => $output,
					"courseInfoSessExp" => time(),
				);
				$this->session->set_userdata($session_data);
			}else{
				$this->message = $output['message'];
			}
		}else{
			$this->courseInfo = $this->session->userdata('courseInfo');
		}
		*/
	}

	function setRole($role){
		$this->role = $role;
	}

	function getRole(){
		return $this->role;
	}

	function setCourseId($courseId){
		if($courseId == null){
			$this->validParameter = false;
			$this->message = "Invalid Parameter";
			return;
		}
		$this->courseId = str_replace(" ", "+", $courseId);
//echo json_encode($this->courseInfo);
		foreach($this->courseInfo['data'][$this->platform]['results'] as $course){
			if($course['course_id'] == $this->courseId){
				$this->setRole($course['role_name']);
				return;
			}
		}
		$this->validParameter = false;
		$this->message = "You do not have access to this course";
		return;
	}

	function getCourseId(){
		return $this->courseId;
	}

	function setPlatform($platform){
		$this->platform = $platform;
	}

	function getPlatform(){
		return $this->platform;
	}

	function setFromDate($fromDate){
		if($fromDate == null){
			$this->fromDate = "2015-01-01T00:00";
		}else{
			$this->fromDate = $fromDate . "T00:00";
		}
	}

	function getFromDate(){
		return $this->fromDate;
	}

	function setToDate($toDate){
		if($toDate == null){
			$this->toDate = "2016-12-31T00:00";
		}else{
			$this->toDate = $toDate . "T00:00";
		}
	}

	function getToDate(){
		return $this->toDate;
	}

	function setKeepId($keepId){
		if($this->role != 'student'){
			// Check if this student enrolled in this course
			$stuCourseList = $this->courseinfomodel->getData(array($this->getPlatform() => '/user/'.$keepId));
			if(!$stuCourseList['ok']){
				$this->validParameter = false;
				$this->message = "You do not have access to this student";
				return;
			}
			if(array_key_exists('results', $stuCourseList['data'][$this->getPlatform()])){
				foreach($stuCourseList['data'][$this->getPlatform()]['results'] as $course){
					if($course['course_id'] == $this->getCourseId()){
						$this->keepId = $keepId;
						$this->setRole('student');
						return;
					}
				}
			}
			$this->validParameter = false;
			$this->message = "You do not have access to this student";
		}else{
			$this->validParameter = false;
			$this->message = "Only teachers have the access to this view";
		}
	}

	function getKeepId(){
		return $this->keepId;
	}

	function getCourseInfo(){
		return $this->courseInfo;
	}

	function getAccessGranted(){
		return $this->accessGranted;
	}

	function getValidParameter(){
		return $this->validParameter;
	}

	function getMessage(){
		return $this->message;
	}

	function getCourseNameByCourseId($courseId, $platform) {

		$courses = $this->courseInfo['data'][$platform];

		if (intval($courses['total_results']) > 0 ) {
			foreach ($courses['results'] as $course) {
				if($course['course_id'] == $courseId) {
					return $course['course_name'];
				}
			}// end foreach
		}// end if
	}
}
