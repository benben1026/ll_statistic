<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CourseinfoModel extends CI_Model{
	private $config = array(
		"moodle" => array(
			"domain" => "https://moodle.keep.edu.hk/api/v1",
			//key before encode "ph6cruyuMepReh7Hup87deB8aDRERe"
			"key" => "cGg2Y3J1eXVNZXBSZWg3SHVwODdkZUI4YURSRVJlOg=="
		),
		"edx" => array(
			"domain" => "https://edx.keep.edu.hk/api/v1",
			//key before encode "wAVaspaRuwrURuTrU2ar4CrAvUy5th"
			"key" => "d0FWYXNwYVJ1d3JVUnVUclUyYXI0Q3JBdlV5NXRoOg=="
		),
	);

	private $output = array(
		"ok" => false,
		"message" => "",
		"data" => array(),
	);

	function __construct(){
		parent::__construct();
	}

	//url should be an array with the following format:
	//array("moodle" => "moodle url", "edx" => "edx url")
	function getData($url = array()){
		if(empty($url)){
			$this->output['message'] = 'Invalid URL';
			return $this->output;
		}
		$this->output['ok'] = true;
		foreach($url as $lrs => $lrs_url){
			if($this->config[$lrs] == null){
				//array_push($this->output['data'], array($lrs => null));
				$this->output['data'][$lrs] = null;
				continue;
			}
			//array_push($this->output['data'], array($lrs => $this->sendRequest($lrs, $lrs_url)));
			$this->output['data'][$lrs] = $this->sendRequest($lrs, $lrs_url);
		}
		return $this->output;
	}

	function sendRequest($lrs, $lrs_url){
		//$proxy = "192.168.1.149:8000";

		$header = array();
		$header[] = 'Authorization: Basic ' . $this->config[$lrs]['key'];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		//curl_setopt($ch, CURLOPT_PROXY, $proxy);
		curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
		curl_setopt($ch, CURLOPT_URL, $this->config[$lrs]['domain'] . $lrs_url);
		$result = curl_exec($ch);
		if(curl_error($ch))
		{
		    return 'curl_error:' . curl_error($ch);
		}
		curl_close($ch);
		return json_decode($result, TRUE);
	}
}