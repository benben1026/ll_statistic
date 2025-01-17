<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class File extends CI_Controller{
	// default variables
	private $platforms = array("moodle","edx");
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

		// auth checking
		if(!$this->apimodel->getAccessGranted()){
			$this->returnData['ok'] = false;
			$this->returnData['message'] = $this->apimodel->getMessage();
			printJson($this->returnData);
			return;
		}

		// date range
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

	private function overviewNum(){
		$pipeline = array();
		foreach ($this->platforms as $platform) {
			$key = getKey($platform);
			if(isset($this->apimodel->getCourseInfo()['data'][$platform]['total_results'])){
				$courseId = array();
				for($i = 0; $i < $this->apimodel->getCourseInfo()['data'][$platform]['total_results']; $i++){
					if($this->apimodel->getCourseInfo()['data'][$platform]['results'][$i]['role_name'] == 'student'){
						continue;
					}
					$t = array("statement.context.extensions.".$key.".courseid" => array("\$eq" => $this->apimodel->getCourseInfo()['data'][$platform]['results'][$i]['course_id']));
					array_push($courseId, $t);
				}
				$match = array(
					"\$match" => array(
						"\$or" => $courseId,
						"statement.verb.id" => array(
							"\$eq" => "http://id.tincanapi.com/verb/viewed"
						),
						"statement.object.definition.name.en-us" => array("\$eq" => "a courseware asset"),
					),
				);
				$group = array(
					"\$group" => array(
						"_id" => array(
							//"name" => "\$statement.object.definition.name.en-us",
							"courseid" => "\$statement.context.extensions.".$key.".courseid",
							"file_id" => "\$statement.object.id",
							"file_name" => "\$statement.object.definition.description.en-us",
							"platform" => $platform
						),
						"count" => array("\$sum" => 1),
					),
				);
				$sort = array(
					"\$sort" => array("count" => -1),
				);
				$pipeline[$platform] = array($match, $group, $sort);
			}
		}
		$result = $this->datamodel->getData($pipeline);
		if(!$result['ok']){
			$this->returnData['ok'] = false;
			$this->returnData['message'] = 'No data';
			return;
		}else{
			$this->returnData['ok'] = true;
		}
		$data = array();
		$edx_flag = false;
		$moodle_flag = false;
		if(array_key_exists('edx', $result['data']) && array_key_exists('result', $result['data']['edx'])){
			$edx_flag = true;
		}
		if(array_key_exists('moodle', $result['data']) && array_key_exists('result', $result['data']['moodle'])){
			$moodle_flag = true;
		}
		if($edx_flag && $moodle_flag){
			for($i = 0, $j = 0; $i < count($result['data']['moodle']['result']) || $j < count($result['data']['edx']['result']); ){
				if($i == count($result['data']['moodle']['result'])){
					array_push($data, $result['data']['edx']['result'][$j]);
					$j ++;
					continue;
				}
				if($j == count($result['data']['edx']['result'])){
					array_push($data, $result['data']['moodle']['result'][$i]);
					$i ++;
					continue;
				}
				if($result['data']['moodle']['result'][$i]['count'] < $result['data']['edx']['result'][$j]['count']){
					array_push($data, $result['data']['edx']['result'][$j]);
					$j ++;
				}else{
					array_push($data, $result['data']['moodle']['result'][$i]);
					$i ++;
				}
			}
		}else if($edx_flag){
			$data = $result['data']['edx']['result'];
		}else if($moodle_flag){
			$data = $result['data']['moodle']['result'];
		}else{
			$this->returnData['ok'] = false;
			$this->returnData['message'] = 'No data';
			return;
		}
		$this->returnData['data'] = $data;
	}

	//need one more parameter: filename
	//if filename == null, then it will return the overall number of views based on all files
	private function overviewTimeline(){
		$pipeline = array();
		$filename = $this->input->get('filename') == null ? null : str_replace("%20", " ", $this->input->get('filename'));
		foreach ($this->$platforms as $platform) {
			$key = getKey($platform);
			if(isset($this->apimodel->getCourseInfo()['data'][$platform]['total_results'])){
				if($filename != null){
					$match = array(
						"\$match" => array(
							"statement.object.definition.description.en-us" => array("\$eq" => $filename),
							"statement.verb.id" => array("\$eq" => "http://id.tincanapi.com/verb/viewed"),
							"statement.timestamp" =>array("\$gte" => $from, "\$lte" => $to),
						),
					);
				}else{
					$match = array(
						"\$match" => array(
							"statement.verb.id" => array("\$eq" => "http://id.tincanapi.com/verb/viewed"),
							"statement.timestamp" =>array("\$gte" => $from, "\$lte" => $to),
						),
					);
				}
				$group = array(
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

				$sort = array(
					"\$sort" => array("statement.timestamp" => 1),
				);
				$pipeline[$platform] = array($match, $group, $sort);
			}
		}
		$result = $this->datamodel->getData($pipeline);
		$this->returnData['ok'] = $result['ok'];
		$this->returnData['message'] = $result['message'];
		$this->returnData['data'] = $result['data'];
		// TODO: merge data from two platforms
		// if(array_key_exists('edx', $result['data'])){
		//
		// }
		// if(array_key_exists('moodle', $result['data'])){
		//
		// }
	}

	// not used for now
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
		$this->getDataFromPlatform($this->apimodel->getPlatform());
		printJson($this->returnData);
	}

	private function getDataFromPlatform($platform){
		$key = getKey($platform);
		$match = array(
			"\$match" => array(
				"statement.context.extensions.".$key.".courseid" => array(
					"\$eq" => $this->apimodel->getCourseId()
				),
				"statement.verb.id" => array(
					"\$eq" => "http://id.tincanapi.com/verb/viewed"
				),
				"statement.object.definition.name.en-us" => array("\$eq" => "a courseware asset"),
				"statement.timestamp" =>array(
					"\$gte" => $this->apimodel->getFromDate(),
					"\$lte" => $this->apimodel->getToDate(),
				),
			),
		);
		$group = array(
			"\$group" => array(
				"_id" => array(
					"file_id" => "\$statement.object.id",
					"file_name" => "\$statement.object.definition.description.en-us"
				),
				"count" => array("\$sum" => 1),
			),
		);
		$sort = array(
			"\$sort" => array("count" => -1),
		);
		$pipeline[$platform] = array($match, $group, $sort);
		$result = $this->datamodel->getData($pipeline);
		$this->returnData['ok'] = $result['ok'];
		$this->returnData['message'] = $result['message'];
		$this->returnData['data'] = $result['data'];
	}
}
