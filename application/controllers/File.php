<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class File extends CI_Controller{
	private $returnData = array(
		"ok" => false,
		"message" => "",
		"data" => null
	);

	function __contruct(){
		parent::__contruct();
	}

	//type can be either 'num' or 'timeline'
	public function overview($type = 'num'){
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
		if(!$this->apimodel->getValidParameter()){
			$this->returnData['ok'] = false;
			$this->returnData['message'] = $this->apimodel->getMessage();
			printJson($this->returnData);
			return;
		}
		if($type == 'num'){
			$this->overviewNum();
		}else if($type == 'timeline'){
			$this->overviewTimeline();
		}
		printJson($this->returnData);
	}

	public function detail(){
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
		if($this->apimodel->getPlatform() == 'moodle'){
			$this->getDataFromMoodle();
		}else if($this->apimodel->getPlatform() == 'edx'){
			$this->getDataFromEdx();
		}
		printJson($this->returnData);
	}

	private function overviewNum(){
		$pipeline = array();
		if(isset($this->apimodel->getCourseInfo()['data']['moodle']['total_results'])){
			$moodleCourseId = array();
			for($i = 0; $i < $this->apimodel->getCourseInfo()['data']['moodle']['total_results']; $i++){
				$t = array("statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.courseid" => array("\$eq" => $this->apimodel->getCourseInfo()['data']['moodle']['results'][$i]['course_id']));
				array_push($moodleCourseId, $t);
			}
			$moodle_match = array(
				"\$match" => array(
					"\$or" => $moodleCourseId,

					"statement.verb.id" => array(
						"\$eq" => "http://id.tincanapi.com/verb/viewed"
					),
				),
			);
			$moodle_group = array(
				"\$group" => array(
					"_id" => array(
						"name" => "\$statement.object.definition.name.en-us",
						"courseid" => "\$statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.courseid"
					),
					"count" => array("\$sum" => 1),
				),
			);
			$moodle_sort = array(
				"\$sort" => array("count" => -1),
			);
			$pipeline['moodle'] = array($moodle_match, $moodle_group, $moodle_sort);
		}
		if(isset($this->apimodel->getCourseInfo()['data']['edx']['total_results'])){
			$edxCourseId = array();
			for($i = 0; $i < $this->apimodel->getCourseInfo()['data']['edx']['total_results']; $i++){
				$t = array("statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.courseid" => array("\$eq" => $this->apimodel->getCourseInfo()['data']['edx']['results'][$i]['course_id']));
				array_push($edxCourseId, $t);
			}
			$edx_match = array(
				"\$match" => array(
					"\$or" => $edxCourseId,
					"statement.verb.id" => array(
						"\$eq" => "http://id.tincanapi.com/verb/viewed"
					),
					"statement.object.definition.name.en-us" => array("\$eq" => "a courseware asset"),
				),
			);
			$edx_group = array(
				"\$group" => array(
					"_id" => array(
						"file_id" => "\$statement.object.id",
						"file_name" => "\$statement.object.definition.description.en-us",
						"file_name2" => "\$statement.object.definition.name.en-us"
					),
					"count" => array("\$sum" => 1),
				),
			);
			$edx_sort = array(
				"\$sort" => array("count" => -1),
			);
			$pipeline['edx'] = array($edx_match, $edx_group, $edx_sort);
		}
		$result = $this->datamodel->getData($pipeline);
		$this->returnData['ok'] = $result['ok'];
		$this->returnData['message'] = $result['message'];
		$this->returnData['data'] = $result['data'];
		// TODO: merge data from two platforms
		if(array_key_exists('edx', $result['data'])){

		}
		if(array_key_exists('moodle', $result['data'])){

		}
	}

	//need one more parameter: filename
	//if filename == null, then it will return the overall number of views based on all files
	private function overviewTimeline(){
		$pipeline = array();
		$filename = $this->input->get('filename') == null ? null : str_replace("%20", " ", $this->input->get('filename'));
		if(isset($this->apimodel->getCourseInfo()['data']['moodle']['total_results'])){
			// TODO
		}
		if(isset($this->apimodel->getCourseInfo()['data']['edx']['total_results'])){
			if($filename != null){
				$edx_match = array(
					"\$match" => array(
						"statement.object.definition.name.en-us" => array("\$eq" => $filename),
						"statement.verb.id" => array("\$eq" => "http://id.tincanapi.com/verb/viewed"),
						"statement.timestamp" =>array("\$gte" => $from, "\$lt" => $to),
					),
				);
			}else{
				$edx_match = array(
					"\$match" => array(
						"statement.verb.id" => array("\$eq" => "http://id.tincanapi.com/verb/viewed"),
						"statement.timestamp" =>array("\$gte" => $from, "\$lt" => $to),
					),
				);
			}

			$edx_group = array(
				"\$group" => array(
					"_id" => array(
						"date" => array(
							"\$substr" => array(
								"\$statement.timestamp", 0, 10,
							),
						),
					),
					"value"=>array(
						"\$sum"=>1,
					),
				),
			);

			$edx_sort = array(
				"\$sort" => array("statement.timestamp" => 1),
			);
			$pipeline['edx'] = array($edx_match, $edx_group, $edx_sort);
		}
		$result = $this->datamodel->getData($pipeline);
		$this->returnData['ok'] = $result['ok'];
		$this->returnData['message'] = $result['message'];
		$this->returnData['data'] = $result['data'];
		//TO DO: merge data from two platforms
		if(array_key_exists('edx', $result['data'])){

		}
		if(array_key_exists('moodle', $result['data'])){

		}
	}

	private function getDataFromMoodle(){
		//TO DO
	}

	private function getDataFromEdx(){
		$edx_match = array(
			"\$match" => array(
				"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.courseid" => array(
					"\$eq" => $this->apimodel->getCourseId()
				),
				"statement.verb.id" => array(
					"\$eq" => "http://id.tincanapi.com/verb/viewed"
				),
				"statement.object.definition.name.en-us" => array("\$eq" => "a courseware asset"),
				"statement.timestamp" =>array(
					"\$gte" => $this->apimodel->getFromDate(),
					"\$lt" => $this->apimodel->getToDate(),
				),
			),
		);
		$edx_group = array(
			"\$group" => array(
				"_id" => array(
					"file_id" => "\$statement.object.id",
					"file_name" => "\$statement.object.definition.description.en-us",
					"file_name2" => "\$statement.object.definition.name.en-us"
				),
				"count" => array("\$sum" => 1),
			),
		);
		$edx_sort = array(
			"\$sort" => array("count" => -1),
		);
		$pipeline['edx'] = array($edx_match, $edx_group, $edx_sort);
		$result = $this->datamodel->getData($pipeline);
		$this->returnData['ok'] = $result['ok'];
		$this->returnData['message'] = $result['message'];
		$this->returnData['data'] = $result['data'];
	}

}
