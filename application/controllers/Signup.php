<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Signup extends CI_Controller {
	public function index(){
		$postData = $this->input->post();
        $this->load->model("signupmodel");
        if(isset($postData['email'], $postData['pwd'])){
        	$result = $this->signupmodel->signup($postData['email'], $postData['pwd']);
        }else{
        	print 'fail (Invalid Parameter)';
        	return;
        }
        if($result){
        	print 'success';
        }else{
        	print 'fail';
        }
	}
}