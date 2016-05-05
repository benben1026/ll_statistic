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
			$this->printJson();
			return;
		}
		$this->apimodel->setFromDate($this->input->get('from'));
		$this->apimodel->setToDate($this->input->get('to'));
		if(!$this->apimodel->getValidParameter()){
			$this->returnData['ok'] = false;
			$this->returnData['message'] = $this->apimodel->getMessage();
			$this->printJson();
			return;
		}
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
					"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.eventname" => array(
						"\$eq" => $type == "view" ? "\\mod_forum\\event\\discussion_viewed" : "\\mod_forum\\event\\post_created"
					),
					"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.rolename" => array("\$eq" => "student"),
					"statement.timestamp" =>array(
						"\$gte" => $this->apimodel->getFromDate(),
						"\$lt" => $this->apimodel->getToDate(),
					),
				),
			);
			$moodle_group = array(
				"\$group" => array(
					"_id" => array(
						"forum_id" => "\$statement.object.id",
						"forum_name" => "\$statement.object.definition.name.en"
					),
					"count" => array("\$sum" => 1),
				)
			);
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
			$edx_match = array(
				"\$match" => array(
					"\$or" => $edxCourseId,
					"statement.verb.id" => array("\$eq" => $type == "view" ? "http://id.tincanapi.com/verb/viewed" : "http://adlnet.gov/expapi/verbs/responded"),
					"statement.object.id" => array("\$regex" => $type == "view" ? "/discussion/forum/course/threads/." : "/threads/", "\$options" => "i"),
					"statement.timestamp" =>array(
						"\$gte" => $this->apimodel->getFromDate(),
						"\$lt" => $this->apimodel->getToDate(),
					),
				),
			);
			//TO DO:update the pipeline to group comments of the same post together
			//object id: https://edx.keep.edu.hk/courses/course-v1:cuhk+csci2100a+2015_2/discussion/forum/course/threads/571361123d97140a7c0000cc#response_5713b3ee3d97140a7f0000e6
			//how to ignore the response id after #
			$edx_group = array(
				"\$group" => array(
					"_id" => array(
						"forum_id" => "\$statement.object.id",
						"forum_name" => "\$statement.object.definition.description.en-US",
						"course_name" => "\$statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.coursename",
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
//echo json_encode($pipeline);
		//merge data from moodle and edx
		$data = array();
		if(array_key_exists('edx', $result['data'])){
			for($i = 0; $i < count($result['data']['edx']['result']); $i++){
				$forum_name = array_key_exists("forum_name", $result['data']['edx']['result'][$i]['_id']) ? $result['data']['edx']['result'][$i]['_id']['forum_name'] : "Forum";
				$temp = array(
					$i + 1,
					"<a target=\"_blank\" href=\"" . $result['data']['edx']['result'][$i]['_id']['forum_id'] . "\">" . $forum_name . "</a>",
					$result['data']['edx']['result'][$i]['_id']['course_name'],
					'KEEP edX',
					$result['data']['edx']['result'][$i]['count'],
				);
				array_push($data, $temp);
			}
		}
		if(array_key_exists('moodle', $result['data'])){
			//TO DO
		}
		$this->returnData['ok'] = true;
		$this->returnData['data'] = $data;
		$this->printJson();
	}

	public function detail($type){
		$this->load->model('apimodel');
		$this->load->model('datamodel');
		if(!$this->apimodel->getAccessGranted()){
			$this->returnData['ok'] = false;
			$this->returnData['message'] = $this->apimodel->getMessage();
			$this->printJson();
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
			$this->printJson();
			return;
		}


		if($this->apimodel->getPlatform() == 'moodle'){
			if($type == 'view'){
				$this->getViewFromMoodle();
			}else if($type == 'reply'){
				$this->getReplyFromMoodle();
			}else if($type == 'active'){
				$this->getActiveFromMoodle();
			}
		}else if($this->apimodel->getPlatform() == 'edx'){
			$result;
			if($type == 'view'){
				$result = $this->getViewFromEdx();
			}else if($type == 'reply'){
				$result = $this->getReplyFromEdx();
			}else if($type == 'active'){
				$result = $this->getActiveFromEdx();
			}
			$data = array();
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
		}
		$this->printJson();
	}

	private function getViewFromMoodle(){
		//TO DO
	}

	private function getReplyFromMoodle(){
		//TO DO
	}

	private function getActiveFromMoodle(){
		//TO DO
	}

	private function getViewFromEdx(){
		$edx_match = array(
			"\$match" => array(
				"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.courseid" => $this->apimodel->getCourseId(),
				"statement.verb.id" => array("\$eq" => "http://id.tincanapi.com/verb/viewed"),
				"statement.object.id" => array("\$regex" => "/discussion/forum/course/threads/." , "\$options" => "i"),
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
					"forum_name" => "\$statement.object.definition.description.en-US",
				),
				"count" => array("\$sum" => 1),
			),
		);
		$edx_sort = array(
			"\$sort" => array("count" => -1),
		);
		$pipeline['edx'] = array($edx_match, $edx_group, $edx_sort);
		return $this->datamodel->getData($pipeline);
	}

	private function getReplyFromEdx(){
		$edx_match = array(
			"\$match" => array(
				"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.courseid" => $this->apimodel->getCourseId(),
				"statement.verb.id" => array("\$eq" => "http://adlnet.gov/expapi/verbs/responded"),
				"statement.object.id" => array("\$regex" => "/discussion/", "\$options" => "i"),
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
					"forum_name" => "\$statement.object.definition.description.en-US",
				),
				"count" => array("\$sum" => 1),
			),
		);
		$edx_sort = array(
			"\$sort" => array("count" => -1),
		);
		$pipeline['edx'] = array($edx_match, $edx_group, $edx_sort);
		return $this->datamodel->getData($pipeline);
	}

	private function getActiveFromEdx(){
		$edx_match = array(
			"\$match" => array(
				"\$or" => array(
					array("statement.verb.display.en-US" => array("\$eq" => "created")),
					array("statement.verb.display.en-US" => array("\$eq" => "responded to")),
					array("statement.verb.display.en-US" => array("\$eq" => "updated")),
				),
				"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.courseid" => $this->apimodel->getCourseId(),
				"statement.object.id" => array("\$regex" => "/discussion/", "\$options" => "i"),
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
					"forum_name" => "\$statement.object.definition.description.en-US",
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
		
		$pipeline['edx'] = array($edx_match, $edx_group, $edx_sort);
		return $this->datamodel->getData($pipeline);
	}

	private function printJson(){
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($this->returnData));
	}
}