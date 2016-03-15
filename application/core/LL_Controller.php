<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Acc_Controller extends CI_Controller {
    protected $user = array();

    function __construct(){
    	parent::__construct();

    	$this->user['id'] = $this->session->userdata('userId');
    	$this->user['name'] = $this->session->userdata('username');
    	//$this->$user['id'] = $_SESSION['userId'];
    	//$this->$user['name'] = $_SESSION['username'];
    }
}

class Saml_Controller extends CI_Controller {
	protected $auth;
	protected $CI;
	protected $keepid;
	protected $fullname;
	//protected $user = array();

	function __construct(){
		parent::__construct();

		require_once __DIR__ . '/vendor/autoload.php';
		$this->CI =& get_instance();
		$this->CI->load->config('saml');
		$this->auth = new OneLogin_Saml2_Auth($this->CI->config->item('saml'));
	}
}