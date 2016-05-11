<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Assignment extends CI_Controller{
	private $returnData = array(
		"ok" => false,
		"message" => "",
		"data" => null
	);

	function __contruct(){
		parent::__contruct();
	}

	private function getKey($platform) {
		switch ($platform) {
			case "edx":
					return "http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log";
			case "moodle":
					return "http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log";
		}
	}

	// get assignment list of a particular platform
	public function getAsgList(){
		$this->load->model('apimodel');
		$this->load->model('datamodel');

		// Auth checking
		if(!$this->apimodel->getAccessGranted()){
			$this->returnData['ok'] = false;
			$this->returnData['message'] = $this->apimodel->getMessage();
			printJson($this->returnData);
			return;
		}

		// date range
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

		//get context key, right now, it's just moodle
		$key = $this->getKey($this->apimodel->getPlatform());
		$match = array(
			"\$match" => array(
				"statement.actor.name" => array("\$eq" => $this->apimodel->getKeepId()),
				"statement.verb.id" => array("\$eq" => "http://adlnet.gov/expapi/verbs/completed"),
				"statement.context.extensions.".$key.".component" => array("\$eq" => "mod_assign"),
				"statement.context.extensions.".$key.".courseid" => array("\$eq" => $this->apimodel->getCourseId()),
			),
		);

		$sort = array(
			"\$sort" => array(
				"statement.timestamp" => -1,
			)
		);
		$group = array(
			"\$group" => array(
				"_id" => array(
					// "asg_name" => "\$statement.object.definition.name.en-us"
					"asg_name" => "\$statement.object.definition.description.en-us"
				)
			),
		);

		$pipeline = array(
			"moodle" => array($match, $sort, $group),
		);
		$result = $this->datamodel->getData($pipeline);
		$this->returnData = $result;
		printJson($this->returnData);
	}

	public function getAsgDis(){
		//one more parameter is required, asg
		$this->load->model('apimodel');
		$this->load->model('datamodel');
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
		$asg_name = str_replace("%20", " ", $this->input->get('asg'));
		if($asg_name == null){
			$this->returnData['ok'] = false;
			$this->returnData['message'] = 'Please specify assignemnt name';
			printJson($this->returnData);
			return;
		}

		//get context key, right now, it's just moodle
		$key = $this->getKey($this->apimodel->getPlatform());
		$match = array(
			"\$match" => array(
				"statement.context.extensions.".$key.".eventname" => array("\$eq" => "\\core\\event\\user_graded"),
				"statement.context.extensions.".$key.".courseid" => array("\$eq" => $this->apimodel->getCourseId()),
				"statement.verb.id" => array("\$eq" => "http://www.tincanapi.co.uk/verbs/evaluated"),
				"statement.object.definition.description.en-us" => array("\$eq" => $asg_name),
				// "statement.context.contextActivities.grouping" => array(
				// 	"\$elemMatch" => array("definition.description.en-us" => array("\$eq" => $asg_name))
				// ),
			)
		);
		$sort = array(
			"\$sort" => array(
				"statement.object.name" => 1,
				"statement.timestamp" => -1,
				//"statement.result.score.raw" => 1,
			)
		);
		$project = array(
			"\$project" => array(
				"_id" => 0,
				"statement.result.score" => 1,
				"statement.object.name" => 1,
				"statement.timestamp" => 1,
			)
		);
		$pipeline = array("moodle" => array($match, $sort, $project),);
		$result = $this->datamodel->getData($pipeline);
		if(!$result['ok']){
			$this->returnData = $result;
			printJson($this->returnData);
			return;
		}

		//instructor can make multiple assessments to one assignment,
		//so we need to delete the redundance record
		$newData = array();
		$lastId = 0;
		for($i = 0; $i < count($result['data']['moodle']['result']); $i++){
			if($lastId != $result['data']['moodle']['result'][$i]['statement']['object']['name']){
				array_push($newData, $result['data']['moodle']['result'][$i]);
				$lastId = $result['data']['moodle']['result'][$i]['statement']['object']['name'];
			}
		}
		$this->returnData['ok'] = true;
		$this->returnData['data'] = $newData;

		printJson($this->returnData);
	}
}
