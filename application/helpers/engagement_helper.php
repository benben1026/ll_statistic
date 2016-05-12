<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('load_engagement_list'))
{
	function load_engagement_list($profile = 'default'){
      	$CI =& get_instance();        
		$CI->config->load('engagement');
		return $CI->config->item($profile, 'stu_engage_classify');
	}
}

if (!function_exists('get_engagement_verbs')) {
	// return of a unique verbs array
	function get_engagement_verbs($profile = 'default') {
		
		$list = load_engagement_list($profile);
		
		$verbs = array();
		
		foreach ($list as $category => $statementArray) {
			foreach ($statementArray as $statement) {
				// action always at first item
				$currentVerb = $statement[0];
				
				// Check if the verbs exist
				if (!in_array($currentVerb, $verbs)) {
					// append the new value to array
					$verbs[] = $currentVerb;
				}
			}// end foreach
		}// end foreach
		
		return $verbs;
	}
}