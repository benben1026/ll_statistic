<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Forum extends CI_Controller{
	private $returnData = array(
		"ok" => false,
		"message" => "",
		"data" => null
	);

	function __contruct(){
		parent::__contruct();
	}

	public function overview($type){
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
			$printJson($this->returnData);
			return;
		}
		$pipeline = array();
		if(isset($this->apimodel->getCourseInfo()['data']['moodle']['total_results'])){
			$moodleCourseId = array();
			for($i = 0; $i < $this->apimodel->getCourseInfo()['data']['moodle']['total_results']; $i++){
				$t = array("statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.courseid" => array("\$eq" => $this->apimodel->getCourseInfo()['data']['moodle']['results'][$i]['course_id']));
				array_push($moodleCourseId, $t);
			}		
			$moodle_match = $this->getOverviewMatchArray($moodleCourseId);
			
			$moodle_group = $this->getOverviewGroupArray("moodle");
			
			$moodle_sort = array(
				"\$sort" => array("count" => -1)
			);
			$pipeline['moodle'] = array($moodle_match, $moodle_group, $moodle_sort);
		}
		if(isset($this->apimodel->getCourseInfo()['data']['edx']['total_results'])){
			$edxCourseId = array();
			for($i = 0; $i < $this->apimodel->getCourseInfo()['data']['edx']['total_results']; $i++){
				$t = array("statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.courseid" => array("\$eq" => $this->apimodel->getCourseInfo()['data']['edx']['results'][$i]['course_id']));
				array_push($edxCourseId, $t);
			}
			$edx_match = $this->getOverviewMatchArray($edxCourseId);
			
			
			//TO DO:update the pipeline to group comments of the same post together
			//object id: https://edx.keep.edu.hk/courses/course-v1:cuhk+csci2100a+2015_2/discussion/forum/course/threads/571361123d97140a7c0000cc#response_5713b3ee3d97140a7f0000e6
			//how to ignore the response id after #
			$edx_group = $this->getOverviewGroupArray('edx');
			$edx_sort = array(
				"\$sort" => array("count" => -1),
			);
			$pipeline['edx'] = array($edx_match, $edx_group, $edx_sort);
		}
		$result = $this->datamodel->getData($pipeline);
//echo json_encode($pipeline);
		//merge data from moodle and edx
		$data = array();
		if(array_key_exists('edx', $result['data'])){
			for($i = 0; $i < count($result['data']['edx']['result']); $i++){
				$forum_name = array_key_exists("forum_name", $result['data']['edx']['result'][$i]['_id']) ? $result['data']['edx']['result'][$i]['_id']['forum_name'] : "Forum";
				$temp = array(
					$i + 1,
					"<a target=\"_blank\" href=\"" . $result['data']['edx']['result'][$i]['_id']['forum_id'] . "\">" . $forum_name . "</a>",										
					$this->apimodel->getCourseNameByCourseId($result['data']['edx']['result'][$i]['_id']['course_id'], "edx"),
					'KEEP edX',
					$result['data']['edx']['result'][$i]['count'],
				);
				array_push($data, $temp);
			}
		}
		if(array_key_exists('moodle', $result['data'])){
			for($i = 0; $i < count($result['data']['edx']['result']); $i++){
				$forum_name = array_key_exists("forum_name", $result['data']['edx']['result'][$i]['_id']) ? $result['data']['edx']['result'][$i]['_id']['forum_name'] : "Forum";
				$temp = array(
					$i + 1,
					"<a target=\"_blank\" href=\"" . $result['data']['edx']['result'][$i]['_id']['forum_id'] . "\">" . $forum_name . "</a>",										
					$this->apimodel->getCourseNameByCourseId($result['data']['edx']['result'][$i]['_id']['course_id'], "moodle"),
					'KEEP edX',
					$result['data']['edx']['result'][$i]['count'],
				);
				array_push($data, $temp);
			}
		}
		
		$this->returnData['ok'] = true;
		$this->returnData['data'] = $data;
		printJson($this->returnData);
	}

	public function detail($type){
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

		$platform = $this->apimodel->getPlatform();
		$result;
		// TODO: Could be further shorten the code
		switch ($type) {
	    case "view":
	        $result = $this->getView($platform);
			break;	        
	    case "reply":
	        $result = $this->getReply($platform);
			break;  	    
		case "active":
	        $result = $this->getActive($platform);
			break;			
		}
		$data = array();
		
		// format the return data
		for($i = 0; $i < count($result['data'][$this->apimodel->getPlatform()]['result']); $i++){
			$forum_name = array_key_exists("forum_name", $result['data'][$this->apimodel->getPlatform()]['result'][$i]['_id']) ? $result['data'][$this->apimodel->getPlatform()]['result'][$i]['_id']['forum_name'] : "Forum";
			//$forum_name = "Forum";
			$temp = array(
				$i + 1,
				"<a target=\"_blank\" href=\"" . $result['data'][$this->apimodel->getPlatform()]['result'][$i]['_id']['forum_id'] . "\">" . $forum_name . "</a>",
				$result['data'][$this->apimodel->getPlatform()]['result'][$i]['count'],
			);
			array_push($data, $temp);
		}
		$this->returnData['data'] = $data;
		$this->returnData['ok'] = true;
		
		printJson($this->returnData);		
	}

	// - Private Function

	private function getView($platform){
		$key = $this->getKey($platform);
		$match = array(
			"\$match" => array(
				"statement.context.extensions.".$key.".courseid" => $this->apimodel->getCourseId(),
				"statement.verb.id" => array("\$eq" => "http://id.tincanapi.com/verb/viewed"),
				"statement.object.definition.name.en-us" => array("\$eq" => "a discussion thread"),
				"statement.timestamp" =>array(
					"\$gte" => $this->apimodel->getFromDate(),
					"\$lt" => $this->apimodel->getToDate(),
				),
			),
		);
		$group = array(
			"\$group" => array(
				"_id" => array(
					"forum_id" => "\$statement.object.id",
					"forum_name" => "\$statement.object.definition.description.en-us",
				),
				"count" => array("\$sum" => 1),
			),
		);
		$sort = array(
			"\$sort" => array("count" => -1),
		);
		$pipeline[$platform] = array($match, $group, $sort);
		return $this->datamodel->getData($pipeline);
	}

	private function getReply($platform){
		$key = $this->getKey($platform);
		$match = array(
			"\$match" => array(
				"statement.context.extensions.".$key.".courseid" => $this->apimodel->getCourseId(),
				"statement.verb.id" => array("\$eq" => "http://adlnet.gov/expapi/verbs/responded"),
				"\$or" => array(
					"statement.object.definition.name.en-us" => array("\$eq" => "a discussion thread"),
					"statement.object.definition.name.en-us" => array("\$eq" => "a discussion response"),
				),
				"statement.timestamp" =>array(
					"\$gte" => $this->apimodel->getFromDate(),
					"\$lt" => $this->apimodel->getToDate(),
				),
			),
		);
		$group = array(
			"\$group" => array(
				"_id" => array(
					"forum_id" => "\$statement.object.id",
					"forum_name" => "\$statement.object.definition.description.en-us",
				),
				"count" => array("\$sum" => 1),
			),
		);
		$sort = array(
			"\$sort" => array("count" => -1),
		);
		$pipeline[$platform] = array($match, $group, $sort);
		return $this->datamodel->getData($pipeline);
	}

	private function getActive($platform){
		$key = $this->getKey($platform);
		$edx_match = array(
			"\$match" => array(
				"\$or" => array(
					array("statement.verb.display.en-us" => array("\$eq" => "created")),
					array("statement.verb.display.en-us" => array("\$eq" => "responded to")),
					array("statement.verb.display.en-us" => array("\$eq" => "updated")),
				),
				"statement.context.extensions.".$key.".courseid" => $this->apimodel->getCourseId(),
				"statement.object.definition.description.en-us" => array("\$eq" => "a discussion thread"),
				"statement.timestamp" =>array(
					"\$gte" => $this->apimodel->getFromDate(),
					"\$lt" => $this->apimodel->getToDate(),
				),
			),
		);
		$edx_group = array(
			"\$group" => array(
				"_id" => array(
					"forum_id" => "\$statement.object.id",
					"forum_name" => "\$statement.object.definition.description.en-us",
				),
				"count" => array(
					"\$max" => array("\$substr"=>array("\$statement.timestamp", 0, 10))
				)
			)
		);
		$edx_sort = array(
			"\$sort" => array(
				"count" => -1,
			)
		);
		
		$pipeline[$platform] = array($match, $group, $sort);
		return $this->datamodel->getData($pipeline);
	}
	
	private function getOverviewGroupArray($platform) {
		
		$key = $this->getKey($platform);
				
		return array(
			"\$group" => array(
				"_id" => array(
					"forum_id" => "\$statement.object.id",
					"forum_name" => "\$statement.object.definition.description.en-us",
					"course_id" => "\$statement.context.extensions.".$key.".courseid",
				),
				"count" => array("\$sum" => 1),
			),
		);
	}
	
	private function getOverviewMatchArray($courseId) {
		return array(
				"\$match" => array(
					"\$or" => $courseId,
					"statement.verb.id" => array("\$eq" => $type == "view" ? "http://id.tincanapi.com/verb/viewed" : "http://adlnet.gov/expapi/verbs/responded"),
					"\$or" => array(
						"statement.object.definition.name.en-us" => array("\$eq" => "a discussion thread"),
						"statement.object.definition.name.en-us" => array("\$eq" => "a discussion response"),
					),					
					"statement.timestamp" =>array(
						"\$gte" => $this->apimodel->getFromDate(),
						"\$lt" => $this->apimodel->getToDate(),
					),
				),
			);
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