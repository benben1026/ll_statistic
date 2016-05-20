<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Engagement extends CI_Controller{
	private $returnData = array(
		"ok" => false,
		"message" => "",
		"data" => null
	);
	
	private $engagementClassify;

	function __contruct(){
		parent::__contruct();
	}

	public function detail(){
		$this->load->model('apimodel');
		$this->load->model('datamodel');
		$this->load->model('engagementdatamodel');
		$this->load->model('cachemodel');
		
		$this->engagementClassify = load_engagement_list();
		
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
		// This is loading student views for teacher.
		// Teacher will fetch data as a student role.
		if($this->input->get('keepId') != null){
			$this->apimodel->setKeepId($this->input->get('keepId'));
		}
		if(!$this->apimodel->getValidParameter()){
			$this->returnData['ok'] = false;
			$this->returnData['message'] = $this->apimodel->getMessage();
			printJson($this->returnData);
			return;
		}							
		
		$this->getData();
		printJson($this->returnData);
	}
	
	public function cron_job() {
		var_dump("Date: ".$this->input->get('date'));
		$this->load->model('cachemodel');				
		$this->cachemodel->createStatisticRecords($this->input->get('date'));
	}
	
	private function getData() {		
		
		// Get platform
		$platform = $this->apimodel->getPlatform();
		
		// Get course ID
		$courseId = $this->apimodel->getCourseId();
		
		// Get from Date
		$fromDate = $this->apimodel->getFromDate();
		
		// Get To date
		$toDate = $this->apimodel->getToDate();		
		
		
		/*------ Cache mechanism ------- */
		
		// Get the lastUpdateDate
		$lastUpdateDate = $this->cachemodel->getLastUpdateDate();
		
		// If the ToDate < lastUpdateDate
		$format = "yyyy-mm-dd";
		$fromDateCompare = DateTime::createFromFormat($format, $fromDate);
		$toDateCompare  = DateTime::createFromFormat($format, $toDate);
		$lastUpdateDateCompare  = DateTime::createFromFormat($format, $lastUpdateDate);
		
		// Build the Y-coordinate keys
		$ykeys = array();
		foreach ($this->engagementClassify as $category => $verbStateArray) {
			$ykeys[] = $category;
		}
		
		$oldData = array();
		$newData = array();
		
		if ($toDateCompare < $lastUpdateDateCompare) {
			// Get data from Cache only
			$cacheOutputData = $this->cachemodel->readCacheStatisticRecord($platform, $courseId, $fromDate, $toDate);
			$oldData = $cacheOutputData['data'];			
						
		} else if ($fromDateCompare < $lastUpdateDateCompare) {
			// Else if fromDate < lastUpdate
			// Get cache data fromDate - lastUpdate
			$cacheOutputData = $this->cachemodel->readCacheStatisticRecord($fromDate, $toDate);
			
			// Get LRS data from (lastUpdate + 1) - toDate
			$lastUpdateDate->modify('+ 1 day');
									 
			$rawData = $this->engagementdatamodel->getEngageData($platform, $courseId, $lastUpdateDate, $toDate);
		
			// Process the data for display
			$newData = $this->engagementdatamodel->convertToDisplayData($rawData);
		} else {
			// else just get the data from LRS
			
			// Get LRS data from (lastUpdate + 1) - toDate			 
			$rawData = $this->engagementdatamodel->getEngageData($platform, $courseId, $fromDate, $toDate);
		
			// Process the data for display
			$newData = $this->engagementdatamodel->convertToDisplayData($rawData);
			
		}
				
		array_merge($newData, $oldData);	

		$this->returnData['ok'] = true;
		$this->returnData['data'] = array('data' => $newData, 'ykeys' => $ykeys);		
		
	}
	
	private function processOutputData($platform, $output, &$newData) {
		
		if (count($output['data'][$platform]['result']) > 0) {
					
			// init for first block
			$lastDate = $this->removeTimeFromDate($output['data'][$platform]['result'][0]['statement']['timestamp']);
			$newData[] = $this->createDataBlock($lastDate);
			
			// loop and do matching
			for($i = 0; $i < count($output['data'][$platform]['result']); $i++){
				
				$currentResult = $output['data'][$platform]['result'][$i]['statement'];				
				$currentVerb = $currentResult['verb']['display']['en-us'];
				$currentName = $currentResult['object']['definition']['name']['en-us'];
				$currentDate = $this->removeTimeFromDate($currentResult['timestamp']);
				
				if ($currentDate != $lastDate) {
					// create new block
					$newData[] = $this->createDataBlock($currentDate);				
					$lastDate = $currentDate;
				}
				
				// update last block
				$len = count($newData);	
				$newData[$len-1][$this->checkCategory($currentVerb, $currentName)] += 1;		
			}
		}		
	}
	
	private function getPipeline($platform, $fromDate, $toDate) {
		
		$key = getKey($platform);
		
		$match = array(
			"\$match" => array(
				"statement.context.extensions.".$key.".courseid" => array("\$eq" => $this->apimodel->getCourseId()),
				"statement.context.extensions.".$key.".rolename" => array("\$eq" => "student"),
				"statement.timestamp" =>array(
					"\$gte" => $fromDate,
					"\$lte" => $toDate,
				),
				"\$or" => $this->getOrArray()
			),
		);
		if($this->apimodel->getRole() == 'student'){
			$match['$match']['statement.actor.name'] = array("\$eq" => $this->apimodel->getKeepId());
		}
		
		$project = array(
			"\$project" => array(
				"_id" => 0,
				"statement.verb.display.en-us" => 1,
				"statement.object.definition.name.en-us" => 1,
				"statement.timestamp" => 1,
			)
		);
		
		$sort = array(
			"\$sort" => array(				
				"statement.timestamp" => -1,				
			)
		);
		
		return array(
			$platform => array($match, $project, $sort)
		);		
	}
	
	private function getOrArray() {
				
		$returnArray = array();
		
		foreach ($this->engagementClassify as $category => $verbStateArray) {
			foreach($verbStateArray as $verbState) {
				$verb = $verbState[0];
				$name = $verbState[1];
				
				$returnArray[] = array(
					"\$and" => array(
						array("statement.verb.display.en-us" => array("\$eq" => $verb)),
						array("statement.object.definition.name.en-us" => array("\$eq" => $name)),
					)	
				);
			}			
		}		
		
		return $returnArray;
	}
	
	private function checkCategory($inVerb, $inStatement) {
		
		foreach ($this->engagementClassify as $category => $verbStateArray) {
			foreach($verbStateArray as $verbState) {
				$verb = $verbState[0];
				$statement = $verbState[1];

				if ($verb === $inVerb && $statement === $inStatement) {
					return $category;
				}
			}
		}		
	}
	
	private function createDataBlock($inDate) {

		$returnArray = array("date" => $inDate);
		
		foreach ($this->engagementClassify as $key => $value) {
			$returnArray[$key] = 0;
		}
		
		return $returnArray;
	}

	private function removeTimeFromDate($inDate) {
		return substr($inDate, 0, 10);
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