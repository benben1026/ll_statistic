<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('getKey'))
{
	function getKey($platform) {
		switch ($platform) {
	    case "edx":
	        return "http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log";	        
	    case "moodle":
	        return "http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log";    	    
		}
	}
}