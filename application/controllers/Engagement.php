<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Engagement extends CI_Controller{
	private $returnData = array(
		"ok" => false,
		"message" => "",
		"data" => null
	);

	function __contruct(){
		parent::__contruct();
	}

	public function detail(){
		$this->load->model('apimodel');
		$this->load->model('datamodel');
		
		// Auth check
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
		$this->getData($platform);
		printJson($this->returnData);
	}
	
	private function getData($platform) {
		
		$key = getKey($platform);

		$match = array(
			"\$match" => array(
				"statement.context.extensions.".$key.".courseid" => array("\$eq" => $this->apimodel->getCourseId()),
				"statement.context.extensions.".$key.".role" => array("\$eq" => "student"),
				"statement.timestamp" =>array(
					"\$gte" => $this->apimodel->getFromDate(),
					"\$lt" => $this->apimodel->getToDate(),
				),
				"\$or" => $this->getOrArray()		
				//"statement.verb.display.en-US" => array("\$not" => "/interacted/"),
			),
		);
		if($this->apimodel->getRole() == 'student'){
			$match['$match']['statement.actor.name'] = array("\$eq" => $this->apimodel->getKeepId());
		}
		
		$sortDate = array(
			"\$sort" => array(
				"statement.timestamp" => -1,
			)
		);
		
		$group = array(
			"\$group" => array(
				"_id" => array(
					"eventname" => array(
						"\$concat" => array("\$statement.verb.display.en-us", " ", "\$statement.object.definition.name.en-us")
					),
					//"verb" => "\$statement.verb.display.en-US",
					//"object" => "\$statement.object.definition.name.en-US",
					"date" => array("\$substr"=>array(
							"\$statement.timestamp", 0, 10,
						),
					),
				),
				"count" => array("\$sum" => 1)
			),
		);		

		$sortEvent = array(
			"\$sort" => array(
				"_id.event" => 1
			)
		);
		$pipeline = array(
			$platform => array($match, $group, $sortEvent)
		); 
		$output = $this->datamodel->getData($pipeline);

		$ykeys = array();
		$dataProcess = array();
		$preDate = "";
		$preEventname = "";
		for($i = 0; $i < count($output['data'][$platform]['result']); $i++){
			if($output['data'][$platform]['result'][$i]['_id']['date'] != $preDate){
				$preDate = $output['data'][$platform]['result'][$i]['_id']['date'];
				$dataProcess[$output['data'][$platform]['result'][$i]['_id']['date']] = array();
			}
			$dataProcess[$output['data'][$platform]['result'][$i]['_id']['date']][$output['data'][$platform]['result'][$i]['_id']['eventname']] = $output['data'][$platform]['result'][$i]['count'];

			//generate ykeys
			$flag = false;
			for($j = 0; $j < count($ykeys); $j++){
				if($ykeys[$j] == $output['data'][$platform]['result'][$i]['_id']['eventname']){
					$flag = true;
					break;
				}
			}
			if(!$flag){
				array_push($ykeys, $output['data'][$platform]['result'][$i]['_id']['eventname']);
			}
		}
		$newData = array();
		foreach($dataProcess as $date => $event){
			$t = array('date' => $date);
			foreach($ykeys as $y){
				$t[$y] = 0;
			}
			foreach($event as $name => $num){
				$t[$name] = $num;
			}
			array_push($newData, $t);
		}

		$output['data']['data'] = $newData;
		$output['data']['ykeys'] = $ykeys;

		$this->returnData['ok'] = true;
		$this->returnData['data'] = $output['data'];
	}
	
	private function getOrArray() {
		$verbs = get_engagement_verbs();
		
		$returnArray = array();
		
		foreach ($verbs as $verb) {
			$returnArray[] = array("statement.verb.display.en-us" => $verb);
		}		
		return $returnArray;
	}
	
	private function getKey($platform) {
		switch ($platform) {
	    case "edx":
	        return "http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log";	        
	    case "moodle":
	        return "http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log";    	    
		}
	}

	// private function getDataFromEdx(){
	// 	$match = array(
	// 		"\$match" => array(
	// 			"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.courseid" => array("\$eq" => $this->apimodel->getCourseId()),
	// 			"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.role" => array("\$eq" => "student"),
	// 			"statement.timestamp" =>array(
	// 				"\$gte" => $this->apimodel->getFromDate(),
	// 				"\$lt" => $this->apimodel->getToDate(),
	// 			),
	// 			"\$nor" => array(
	// 				array("statement.verb.display.en-us" => "interacted with"),
	// 				array("statement.verb.display.en-us" => "enrolled onto"),
	// 				array("statement.verb.display.en-us" => "logged in to"),
	// 			),
	// 			//"statement.verb.display.en-US" => array("\$not" => "/interacted/"),
	// 		),
	// 	);
	// 	if($this->apimodel->getRole() == 'student'){
	// 		$match['$match']['statement.actor.name'] = array("\$eq" => $this->apimodel->getKeepId());
	// 	}
		
	// 	$sortDate = array(
	// 		"\$sort" => array(
	// 			"statement.timestamp" => -1,
	// 		)
	// 	);
	// 	$group = array(
	// 		"\$group" => array(
	// 			"_id" => array(
	// 				"eventname" => array(
	// 					"\$concat" => array("\$statement.verb.display.en-us", " ", "\$statement.object.definition.name.en-us")
	// 				),
	// 				//"verb" => "\$statement.verb.display.en-US",
	// 				//"object" => "\$statement.object.definition.name.en-US",
	// 				"date" => array("\$substr"=>array(
	// 						"\$statement.timestamp", 0, 10,
	// 					),
	// 				),
	// 			),
	// 			"count" => array("\$sum" => 1)
	// 		),
	// 	);
	// 	$sortEvent = array(
	// 		"\$sort" => array(
	// 			"_id.event" => 1
	// 		)
	// 	);
	// 	$pipeline = array(
	// 		"edx" => array($match, $group, $sortEvent)
	// 	); 
	// 	$output = $this->datamodel->getData($pipeline);

	// 	$ykeys = array();
	// 	$dataProcess = array();
	// 	$preDate = "";
	// 	$preEventname = "";
	// 	for($i = 0; $i < count($output['data']['edx']['result']); $i++){
	// 		if($output['data']['edx']['result'][$i]['_id']['date'] != $preDate){
	// 			$preDate = $output['data']['edx']['result'][$i]['_id']['date'];
	// 			$dataProcess[$output['data']['edx']['result'][$i]['_id']['date']] = array();
	// 		}
	// 		$dataProcess[$output['data']['edx']['result'][$i]['_id']['date']][$output['data']['edx']['result'][$i]['_id']['eventname']] = $output['data']['edx']['result'][$i]['count'];

	// 		//generate ykeys
	// 		$flag = false;
	// 		for($j = 0; $j < count($ykeys); $j++){
	// 			if($ykeys[$j] == $output['data']['edx']['result'][$i]['_id']['eventname']){
	// 				$flag = true;
	// 				break;
	// 			}
	// 		}
	// 		if(!$flag){
	// 			array_push($ykeys, $output['data']['edx']['result'][$i]['_id']['eventname']);
	// 		}
	// 	}
	// 	$newData = array();
	// 	foreach($dataProcess as $date => $event){
	// 		$t = array('date' => $date);
	// 		foreach($ykeys as $y){
	// 			$t[$y] = 0;
	// 		}
	// 		foreach($event as $name => $num){
	// 			$t[$name] = $num;
	// 		}
	// 		array_push($newData, $t);
	// 	}

	// 	$output['data']['data'] = $newData;
	// 	$output['data']['ykeys'] = $ykeys;

	// 	$this->returnData['ok'] = true;
	// 	$this->returnData['data'] = $output['data'];

	// }

	// private function getDataFromMoodle(){
	// 	$eventMapping = array(
	// 		'\\mod_resource\\event\\course_module_viewed' => 'View File',
	// 		'\\local_youtube_events\\event\\video_played' => 'Watch Video',
	// 		'\\mod_forum\\event\\discussion_created' => 'Create Post',
	// 		'\\mod_forum\\event\\discussion_viewed' => 'View Post',
	// 		'\\mod_forum\\event\\post_created' => 'Reply Post',
	// 		'\\mod_assign\\event\\submission_status_viewed' => 'View Assignment',
	// 		'\\mod_assign\\event\\assessable_submitted' => 'Submit Assignment',
	// 		'\\mod_quiz\\event\\attempt_started' => 'Attempt Quiz',
	// 	);

	// 	$match = array(
	// 		"\$match" => array(
	// 			"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.courseid" => array("\$eq" => $this->apimodel->getCourseId()),
	// 			"\$or" => array(
	// 				array(
	// 				"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.eventname" => array("\$eq" => "\\mod_resource\\event\\course_module_viewed"),
	// 				),//view file					
	// 				array(
	// 				"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.eventname" => array("\$eq" => "\\local_youtube_events\\event\\video_played"),
	// 				),//play a video
	// 				array(
	// 				"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.eventname" => array("\$eq" => "\\mod_forum\\event\\discussion_created"),
	// 				),//create new post
	// 				array(
	// 				"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.eventname" => array("\$eq" => "\\mod_forum\\event\\discussion_viewed"),
	// 				),//view post
	// 				array(
	// 				"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.eventname" => array("\$eq" => "\\mod_forum\\event\\post_created"),
	// 				),//reply to a post
	// 				array(
	// 				"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.eventname" => array("\$eq" => "\\mod_assign\\event\\submission_status_viewed"),
	// 				),//view an asg
	// 				array(
	// 				"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.eventname" => array("\$eq" => "\\mod_assign\\event\\assessable_submitted"),
	// 				),//submit an asg
	// 				array(
	// 				"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.eventname" => array("\$eq" => "\\mod_quiz\\event\\attempt_started"),
	// 				),//attempt a quiz
	// 			),
				
	// 			"statement.timestamp" =>array(
	// 				"\$gte" => $this->apimodel->getFromDate(),
	// 				"\$lt" => $this->apimodel->getToDate(),
	// 			),
	// 		),
	// 	);
	// 	if($this->apimodel->getRole() == 'student'){
	// 		$match['$match']['statement.actor.name'] = array("\$eq" => $this->apimodel->getKeepId());
	// 	}
		
	// 	$group = array(
	// 		"\$group" => array(
	// 			"_id" => array(
	// 				"eventname" => "\$statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.eventname",
	// 				"date" => array("\$substr"=>array(
	// 						"\$statement.timestamp", 0, 10,
	// 					),
	// 				)
	// 			),
	// 			"numOfEvent" => array("\$sum" => 1),
	// 		)
	// 	);
	// 	$sortEvent = array(
	// 		"\$sort" => array(
	// 			"_id.eventname" => 1
	// 		)
	// 	);
	// 	$pipeline = array(
	// 		"moodle" => array($match, $group, $sortEvent)
	// 	); 
	// 	$result = $this->datamodel->getData($pipeline);

	// 	$dataProcess = array();
	// 	$temp = $result['data']['moodle']['result'];
	// 	for($i = 0; $i < count($temp); $i++){
	// 		if(!isset($dataProcess[$temp[$i]['_id']['date']])){
	// 			$dataProcess[$temp[$i]['_id']['date']] = array();
	// 			foreach($eventMapping as $raw => $event){
	// 				$dataProcess[$temp[$i]['_id']['date']][$event] = 0;
	// 			}
	// 		}
	// 		$dataProcess[$temp[$i]['_id']['date']][$eventMapping[$temp[$i]['_id']['eventname']]] = $temp[$i]['numOfEvent'];
	// 	}

	// 	$newData = array();
	// 	foreach($dataProcess as $date => $event){
	// 		$t = array('date' => $date);
	// 		foreach($event as $name => $num){
	// 			$t[$name] = $num;
	// 		}
	// 		array_push($newData, $t);
	// 	}
	// 	$ykeys = array();
	// 	foreach($eventMapping as $raw => $event){
	// 		array_push($ykeys, $event);
	// 	}
	// 	$result['data']['data'] = $newData;
	// 	$result['data']['ykeys'] = $ykeys;

	// 	$this->returnData['ok'] = true;
	// 	$this->returnData['data'] = $result['data'];
	// }
}