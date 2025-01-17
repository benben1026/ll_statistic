<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Saml2Controller extends Saml_Controller {

	function __construct(){
		parent::__construct();
	}

	//Route: http://benjamin-zhou.com/ll_statistic/index.php/saml2Controller/login
	//       http://benjamin-zhou.com/ll_statistic/index.php/saml2Controller/login?returnTo=url-for-redirection
	//This function will be invoked when user wants to perform login at your site
	public function login(){
		$returnTo = $this->input->get('returnTo');
		$re = !$returnTo ? site_url() . "/page/overview" : $returnTo;

		//SSO starts here

		$this->auth->login($re);
	}

	//Route: http://benjamin-zhou.com/ll_statistic/index.php/saml2Controller/logout
	//This function will be invoked when user wants to perform logout at your site
	public function logout() {

		//Clear all temporary session
		$this->session->sess_destroy();

		$this->auth->logout();
		//$this->singleLogoutService();
	}

	//Route: http://benjamin-zhou.com/ll_statistic/index.php/saml2Controller/acs
	//This is a callback function which will be triggered by the IDP once SSO is finished
	//assertionConsumerService
	public function acs() {
		//Get RelayState
    	$RelayState = $this->input->post('RelayState');

		//Process the SAMLResponse returned by the IDP
		$this->auth->processResponse();

		//Check if there exists any errors
		$errors = $this->auth->getErrors();
		$errRes = $this->auth->getLastErrorReason();
		if(!empty($errors)){

		}

		//Make sure the user is authenticated by the idp
		//...

		//You may store some of the useful attributes like firstname into session temporary
    	//In my case, I simply store all returned attributes
    	$data = array(
    		'samlUserData' => $this->auth->getAttributes(),
    		'samlSessExp' => $this->auth->getSessionExpiration(),
    	);
    	$this->session->set_userdata($data);
    	//this->keepid = $this->session->userdata('samlUserData')['keepid'][0];

    	//The get parameter RelayState usually define the specific link that will be redirected after the SSO has completed
    	//Remember the $returnTo that we have passed in the login function?
    	//What we have passed in will be reflected on RelayState
    	if($RelayState){
    		redirect($RelayState);
    	}else{
    		redirect('/page/overview');
    	}
	}

	//Route: http://benjamin-zhou.com/ll_statistic/index.php/saml2Controller/logout
	//This callback function will be triggered by the IDP once SLO is initiated by other SP
	public function singleLogoutService() {
		//Clear all temporary session
		$this->session->sess_destroy();

		//Process SLO request
		$this->auth->processSLO();

		//------------------------------------------
		//redirect to landing page
		redirect('/page/landing');
	}

	//Route: http://benjamin-zhou.com/ll_statistic/index.php/saml2Controller/metadata
	//Return the saml2 metadata of your SP in xml format
	public function getMetadata(){
		$settings = $this->auth->getSettings();
		$metadata = $settings->getSPMetadata();
		$errors = $settings->validateMetadata($metadata);

		if(empty($errors)){
			$this->output->set_content_type('application/xml');
			$this->output->set_output($metadata);
		}else{
			print 'error';
		}
	}
}
