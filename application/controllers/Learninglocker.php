<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Learninglocker extends CI_Controller{
	private $domain ;
	private $authentication;

	function __construct(){
		parent::__construct();
		$this->load->config('learninglocker');
		$ll_config = $this->config->item('learninglocker');
		$this->domain = $ll_config['domain'];
		$this->authentication = $ll_config['auth'];
	}

	public function index(){
		print $this->domain;
	}

	public function getEngagement($keepId, $from, $to){
		//$from = DateTime::createFromFormat('Y-m-d', $from);
		//$to = DateTime::createFromFormat('Y-m-d', $to);
		$from = $from . "T00:00";
		$to = $to . "T00:00";
		$match = array(
			"\$match" => array(
				"statement.actor.name" => array(
					//"\$eq" => "stud01"
					"\$eq" => $keepId
				),
				"statement.verb.id" => array(
					"\$eq" => "http://id.tincanapi.com/verb/viewed"
				),
				"statement.timestamp" =>array(
					//"\$gte"=>new MongoDate(strtotime($from->format('Y-m-d H:i:s'))),
					//"\$lt"=>new MongoDate(strtotime($to->format('Y-m-d H:i:s'))),
					"\$gte" => $from,
					"\$lt" => $to,
					//"\$gte" => "2015-01-01T00:00",
      				//"\$lt" => "2016-12-28T00:00"
				),
			),
		);
		$sort = array(
			"\$sort" => array(
				"statement.timestamp" => -1,
			)
		);
		$group = array(
			"\$group"=>array(
				"_id"=>array(
					"date"=>array(
						"\$substr"=>array(
							"\$statement.timestamp", 0, 10,
						),
					),
				),
				"value"=>array(
					"\$sum"=>1,
				),
			),
		);
		$project = array(
			"\$project" => array(
				"_id" => 0,
			)
		);
		$limit = array(
			"\$limit" => 5,
		);
		$pipeline = array(
			$match, $sort, $group, 
		); 
		$this->load->model('datamodel');
		$output = array();
		
		foreach($this->authentication as $key => $value){
			$temp = $this->datamodel->getData($this->domain, $value, "?pipeline=" . json_encode($pipeline) . "");
			$temp = get_object_vars($temp);
			$temp['LRS'] = $key;
			$result = array();
			$element = array();
			foreach($temp['result'] as $k => $v){
				$v = get_object_vars($v);
				$v['_id'] = get_object_vars($v['_id']);
				$element = array(
					"date" => $v['_id']['date'],
					"value" => $v['value'],
				);
				array_push($result, $element);
			}
			$temp['result'] = $result;
			if($temp !== FALSE){
				array_push($output, $temp);
			}
		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($output));
		
	}

	public function getTestData($para){
		$this->load->model('datamodel');
		$output = array();
		foreach($authentication as $key => $value){
			$temp = $this->datamodel->getData($this->domain, $value, "?pipeline=" . $para);
			if($temp !== FALSE){
				array_push($output, $temp);
			}
		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($output));
	}
}