<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('printJson'))
{
	function printJson($value){
		$ci  =& get_instance();
		$ci->output->set_content_type('application/json');
		$ci->output->set_output(json_encode($value));
	}
}