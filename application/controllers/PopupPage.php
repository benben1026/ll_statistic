<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class PopupPage extends CI_Controller{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('apimodel', 'api');
    }

	public function teaViewStu(){		
		
		if(!$this->isAccessGranted()){
			redirect('/Saml2Controller/login');
			return;
		}
		$this->load->view('_partial/head');
		$this->load->view('course_detail', array('role' => 'student', 'popup' => true));
	}

	private function isAccessGranted(){		
		return $this->api->getAccessGranted();		
	}

}