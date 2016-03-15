<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class XAPI extends CI_Controller {
	public function index(){
		$url = 'http://learnlock.benjamin-zhou.com/public/data/xAPI/statements';
		//$url = 'http://learnlock.benjamin-zhou.com/public';
		//$data = array('key1' => 'value1', 'key2' => 'value2');

		// use key 'http' even if you send the request to https://...
		$options = array(
		    'http' => array(
		        'header'  => "Content-type: application/json\r\n"
		        	. "Authorization: Basic ZGM4MTk1ZGUzZmM4NTQ5NzU2N2MzNDdlMzk4YmJiMWU2MjhlNTQxNjo1MjE2NTEzYzY0ZGY1ZjBlMDhiMmI3NTQwNDAxZGFiOGQzN2M5ODFk\r\n"
		        	. "X-Experience-API-Version: 1.0.1",
		        'method'  => 'GET',
		        'content' => '',
		        //'content' => http_build_query($data),
		    ),
		);

		// $options['http']['method'] = "GET";
		// $options['http']['header'] = "Authorization: BasicZGM4MTk1ZGUzZmM4NTQ5NzU2N2MzNDdlMzk4YmJiMWU2MjhlNTQxNjo1MjE2NTEzYzY0ZGY1ZjBlMDhiMmI3NTQwNDAxZGFiOGQzN2M5ODFk\r\n";
		// $options['http']['header'] = "X-Experience-API-Version: 1.0.1";

		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		if ($result === FALSE) { /* Handle error */ }

		var_dump($result);
	}

	private function sendGetResquest($para){
		$url = 'http://learnlock.benjamin-zhou.com/public/data/xAPI/' + para;
		$options = array(
		    'http' => array(
		        'header'  => "Content-type: application/json\r\n"
		        	. "Authorization: Basic ZGM4MTk1ZGUzZmM4NTQ5NzU2N2MzNDdlMzk4YmJiMWU2MjhlNTQxNjo1MjE2NTEzYzY0ZGY1ZjBlMDhiMmI3NTQwNDAxZGFiOGQzN2M5ODFk\r\n"
		        	. "X-Experience-API-Version: 1.0.1",
		        'method'  => 'GET',
		        'content' => '',
		    ),
		);
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		if ($result === FALSE) { 
			return false;
		}
		return json_decode($result);
	}

	public function getStatement(){
		
	}
}