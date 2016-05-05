<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class API extends CI_Controller {	
	protected $role;
	protected $courseId;
	protected $platform;

	protected $fromDate;
	protected $toDate;

	private $keepId;
	private $courseInfo;
	private $accessGranted;
	private $message;

	function __construct(){
		parent::__construct();
	}

	public function index(){
		$this->load->model('apimodel');
		echo $this->apimodel->getKeepId();
		echo json_encode($this->apimodel->getCourseInfo());
	}

	protected function setCourseInfo(){

	}

}