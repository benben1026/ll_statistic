<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Course extends CI_Controller{
	private $returnData = array(
		"ok" => false,
		"message" => "",
		"data" => null
	);

	function __contruct(){
		parent::__contruct();
	}

	// Get the course list
	public function courseList(){
		$this->load->model('apimodel');

		// Auth checking
		if(!$this->apimodel->getAccessGranted()){
			$this->returnData['ok'] = false;
			$this->returnData['message'] = $this->apimodel->getMessage();
			printJson($this->returnData);
			return;
		}

		// Get and build the data
		// Return in JSON format
		$this->returnData['ok'] = true;
		$this->returnData['data'] = $this->apimodel->getCourseInfo()['data'];
		printJson($this->returnData);
	}


	public function addDrop($type){
		$this->load->model('apimodel');
		$this->load->model('datamodel');

		// Auth checking
		if(!$this->apimodel->getAccessGranted()){
			$this->returnData['ok'] = false;
			$this->returnData['message'] = $this->apimodel->getMessage();
			printJson($this->returnData);
			return;
		}
		$this->apimodel->setFromDate($this->input->get('from'));
		$this->apimodel->setToDate($this->input->get('to'));
		//!important: please set platform before courseId
		$this->apimodel->setPlatform($this->input->get('platform'));
		$this->apimodel->setCourseId($this->input->get('courseId'));
		if(!$this->apimodel->getValidParameter()){
			$this->returnData['ok'] = false;
			$this->returnData['message'] = $this->apimodel->getMessage();
			printJson($this->returnData);
			return;
		}
		if($type == 'timeline'){
			$this->addDropTimeline();
		}else if ($type == 'num'){
			$this->addDropNum();
		}
	}

	private function addDropTimeline(){
		$platform = $this->apimodel->getPlatform();
		$key = $this->getKey($platform);

		$edx_match = array(
			"\$match" => array(
				"statement.timestamp" => array(
							"\$gte" => $this->apimodel->getFromDate(),
							"\$lt" => $this->apimodel->getToDate(),
						),
					"\$or" => array(
						array("statement.verb.id" => array("\$eq" => "http://www.tincanapi.co.uk/verbs/enrolled_onto_learning_plan")),
						array("statement.verb.id" => array("\$eq" => "http://activitystrea.ms/schema/1.0/leave")),
					),
					"statement.context.extensions.".$key.".courseid" => array("\$eq" => $this->apimodel->getCourseId()),
					"statement.context.extensions.".$key.".role" => array("\$eq" => "student"),
				)
			);
		$edx_group = array(
				"\$group" => array(
					"_id" => array(
						"event" => "\$statement.verb.display.en-us",
						"date" => array("\$substr"=>array("\$statement.timestamp", 0, 10),),
					),
					"count" => array("\$sum" => 1)
				)
			);
		$sortDate = array(
				"\$sort" => array(
					"_id.date" => 1
				)
			);
		$pipeline[$platform] = array($edx_match, $edx_group, $sortDate);
		$result = $this->datamodel->getData($pipeline);
		$this->returnData['ok'] = $result['ok'];
		$this->returnData['message'] = $result['message'];
		if(!$this->returnData['ok']){
			return;
		}
		$dataProcess = array();
		$preDate = "";
		$lastIndex = 0;
		for($i = 0; $i < count($result['data'][$platform]['result']); $i++){
			if($result['data'][$platform]['result'][$i]['_id']['date'] != $preDate){
				$temp = array(
					"date" => $result['data']['edx']['result'][$i]['_id']['date'],
					"Enroll" => 0,
					"Drop" => 0
				);
				array_push($dataProcess, $temp);
			}
			if($result['data'][$platform]['result'][$i]['_id']['event'] == "enrolled onto"){
				$dataProcess[count($dataProcess) - 1]['Enroll'] = $result['data'][$platform]['result'][$i]['count'];
			}else{
				$dataProcess[count($dataProcess) - 1]['Drop'] = $result['data'][$platform]['result'][$i]['count'];
			}
		}
		$this->returnData['data'] = $dataProcess;

		printJson($this->returnData);
	}

	private function addDropNum(){
		$platform = $this->apimodel->getPlatform();
		$key = $this->getKey($platform);

		$match = array(
			"\$match" => array(
				"statement.context.extensions.".$key.".courseid" => array("\$eq" => $this->apimodel->getCourseId()),
				"statement.context.extensions.".$key.".role" => array("\$eq" => "student"),
				"\$or" => array(
					array("statement.verb.id" => array("\$eq" => "http://www.tincanapi.co.uk/verbs/enrolled_onto_learning_plan")),
					array("statement.verb.id" => array("\$eq" => "http://activitystrea.ms/schema/1.0/leave")),
				),
				"statement.timestamp" => array(
					"\$gte" => $this->apimodel->getFromDate(),
					"\$lt" => $this->apimodel->getToDate(),
				),
			)
		);
		$group = array(
			"\$group" => array(
				"_id" => array(
					"verb" => "\$statement.verb.display.en-us"
				),
				"count" => array("\$sum" => 1),
			)
		);
		$pipeline[$platform] = array($match, $group);
		$result = $this->datamodel->getData($pipeline);
		$this->returnData['ok'] = $result['ok'];
		$this->returnData['message'] = $result['message'];
		$this->returnData['data'] = $result['data'];
		printJson($this->returnData);
	}

	private function getKey($platform) {
		switch ($platform) {
	    case "edx":
	        return "http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log";
	    case "moodle":
	        return "http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log";
		}
	}
}
