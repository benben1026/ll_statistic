<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once (dirname(__FILE__) . "/CourseInfo.php");

class Learninglocker extends CourseInfo {
	private $courseInfo;

	function __construct(){
		parent::__construct();
		$this->courseInfo = $this->getUserCourseList("all");
		$this->load->model('datamodel');
	}
	
	public function getForumViewingStu($platform = "all"){
		$pipeline = array();

		//statement prepared for moodle
		if(($platform == "all" || $platform == "moodle") && isset($this->courseInfo['data']['moodle']['total_results'])){
			$moodleCourseId = array();
			for($i = 0; $i < $this->courseInfo['data']['moodle']['total_results']; $i++){
				$t = array("statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.courseid" => array("\$eq" => $this->courseInfo['data']['moodle']['results'][$i]['course_id']));
				array_push($moodleCourseId, $t);
			}		
			$moodle_match = array(
				"\$match" => array(
					"\$or" => $moodleCourseId,
					"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.eventname" => array(
						"\$eq" => "\\mod_forum\\event\\discussion_viewed"
					),

					"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.rolename" => array("\$eq" => "student"
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
		
		//statement prepared for edx
		if(($platform == "all" || $platform == "edx") && isset($this->courseInfo['data']['edx']['total_results'])){
			$edxCourseId = array();
			for($i = 0; $i < $this->courseInfo['data']['edx']['total_results']; $i++){
				$t = array("statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.courseid" => array("\$eq" => $this->courseInfo['data']['edx']['results'][$i]['course_id']));
				array_push($edxCourseId, $t);
			}
			$edx_match = array(
				"\$match" => array(
					"\$or" => $edxCourseId,
					//-----------------TO DO--------------------
					"statement.verb.id" => array("\$eq" => "http://id.tincanapi.com/verb/viewed"),
					"statement.object.id" => array("\$regex" => "/discussion/forum/", "\$options" => "i"),
				),
			);
			$edx_group = array(
				"\$group" => array(
					"_id" => array(
						//----------------TO DO-------------------
						"forum_id" => "\$statement.object.id",
						"forum_name" => "\$statement.object.definition.description.en-US",
						//"forum_name" => "\$statement.object.id",
					),
					"count" => array("\$sum" => 1),
				),
			);
			$edx_sort = array(
				"\$sort" => array("count" => -1),
			);
			$pipeline['edx'] = array($edx_match, $edx_group, $edx_sort);
		}
		$output = $this->datamodel->getData($pipeline);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($output));
	}

	public function getForumReplyStu($platform = "all"){
		$pipeline = array();

		//statement prepared for moodle
		if(($platform == "all" || $platform == "moodle") && isset($this->courseInfo['data']['moodle']['total_results'])){
			$moodleCourseId = array();
			for($i = 0; $i < $this->courseInfo['data']['moodle']['total_results']; $i++){
				$t = array("statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.courseid" => array("\$eq" => $this->courseInfo['data']['moodle']['results'][$i]['course_id']));
				array_push($moodleCourseId, $t);
			}		
			$moodle_match = array(
				"\$match" => array(
					"\$or" => $moodleCourseId,
					"statement.verb.id" => array("\$eq" => "http://adlnet.gov/expapi/verbs/responded"),
					"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.eventname" => array("\$eq" => "\\mod_forum\\event\\post_created"),

					"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.rolename" => array("\$eq" => "student"),
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
		
		//statement prepared for edx
		if(($platform == "all" || $platform == "edx") && isset($this->courseInfo['data']['edx']['total_results'])){
			$edxCourseId = array();
			for($i = 0; $i < $this->courseInfo['data']['edx']['total_results']; $i++){
				$t = array("statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.courseid" => array("\$eq" => $this->courseInfo['data']['edx']['results'][$i]['course_id']));
				array_push($edxCourseId, $t);
			}
			$edx_match = array(
				"\$match" => array(
					"\$or" => $edxCourseId,
					//-----------------TO DO--------------------
					"statement.verb.id" => array("\$eq" => "http://adlnet.gov/expapi/verbs/responded"),
					"statement.object.id" => array("\$regex" => "/discussion/threads/", "\$options" => "i"),
				),
			);
			$edx_group = array(
				"\$group" => array(
					"_id" => array(
						//----------------TO DO-------------------
						"forum_id" => "\$statement.object.id",
						"forum_name" => "\$statement.object.definition.description.en-US",
						//"forum_name" => "\$statement.object.id",
					),
					"count" => array("\$sum" => 1),
				),
			);
			$edx_sort = array(
				"\$sort" => array("count" => -1),
			);
			$pipeline['edx'] = array($edx_match, $edx_group, $edx_sort);
		}
		$output = $this->datamodel->getData($pipeline);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($output));
	}

	//$courseId should be an array consists of a list of course Id
	public function getFileViewing($platform = "all"){
		$pipeline = array();

		//statement prepared for moodle
		if(($platform == "all" || $platform == "moodle") && isset($this->courseInfo['data']['moodle']['total_results'])){
			$moodleCourseId = array();
			for($i = 0; $i < $this->courseInfo['data']['moodle']['total_results']; $i++){
				$t = array("statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.courseid" => array("\$eq" => $this->courseInfo['data']['moodle']['results'][$i]['course_id']));
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
						"name" => "\$statement.object.definition.name.en",
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
		
		//statement prepared for edx
		if(($platform == "all" || $platform == "edx") && isset($this->courseInfo['data']['edx']['total_results'])){
			$edxCourseId = array();
			for($i = 0; $i < $this->courseInfo['data']['edx']['total_results']; $i++){
				$t = array("statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.courseid" => array("\$eq" => $this->courseInfo['data']['edx']['results'][$i]['course_id']));
				array_push($edxCourseId, $t);
			}
			$edx_match = array(
				"\$match" => array(
					"\$or" => $edxCourseId,
					// "statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.courseid" => array(
					// 	"\$eq" => "course-v1:cuhk+csci2100a+2015_2"
					// ),
					"statement.verb.id" => array(
						"\$eq" => "http://id.tincanapi.com/verb/viewed"
					),
				),
			);
			$edx_group = array(
				"\$group" => array(
					"_id" => array(
						"name" => "\$statement.object.definition.name.en-US",
						"courseid" => "\$statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.courseid"
					),
					"count" => array("\$sum" => 1),
				),
			);
			$edx_sort = array(
				"\$sort" => array("count" => -1),
			);
			$pipeline['edx'] = array($edx_match, $edx_group, $edx_sort);
		}
//echo json_encode($pipeline);
		$output = $this->datamodel->getData($pipeline);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($output));
	}

	public function getFileViewingAccToTime($filename, $from = "2015-01-01", $to = "2017-01-01"){
		$filename = str_replace("%20", " ", $filename);
		$from = $from . "T00:00";
		$to = $to . "T00:00";
		$match = array(
			"\$match" => array(
				"statement.object.definition.name.en" => array(
					"\$eq" => $filename
				),
				"statement.verb.id" => array(
					"\$eq" => "http://id.tincanapi.com/verb/viewed"
				),
				"statement.timestamp" =>array(
					"\$gte" => $from,
					"\$lt" => $to,
				),
			),
		);

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
		$pipeline = array(
			"moodle" => array($match, $group),
			"edx" => array($match, $group),
		);
		$output = $this->datamodel->getData($pipeline);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($output));
	}

	public function getVideoWatching($platform = "all"){
		$courseId_json = $this->input->post("courseId");
		$courseId = json_decode($courseId_json);
		//$courseId = array("59", "65");
		$temp = array();
		for($i = 0; $i < count($courseId); $i++){
			$t = array("statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.courseid" => array("\$eq" => $courseId[$i]),
				);
			array_push($temp, $t);
		}
		$match = array(
			"\$match" => array(
				"\$or" => $temp,

				"statement.verb.id" => array(
					"\$eq" => "http://activitystrea.ms/schema/1.0/start"
				),
			),
		);
		$group = array(
			"\$group" => array(
				"_id" => array(
					"name" => "\$statement.object.definition.name.en",
					"courseid" => "\$statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.courseid"
				),
				"count" => array("\$sum" => 1),
			),
		);
		$sort = array(
			"\$sort" => array("count" => -1),
		);

		$limit = array(
			"\$limit" => 5,
		);

		$pipeline = array(
			"moodle" => array($match, $group, $sort),
			"edx" => array($match, $group, $sort)
		);
		$output = $this->datamodel->getData($pipeline);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($output));
	}


/********************** API Prepared for Course Detail Page ****************************/
	public function getAsgList(){
		$courseId = $this->input->get('courseId');
		$platform = $this->input->get('platform');
		//this id should be return from the login information -- TO MODIFY
		$keepId = "563a82e2-96ed-11e4-bf37-080027087aa9";
		// if(!$this->checkCourseAcc($courseId, $platform)){
		// 	$output = array('ok' => false, 'message' => 'You cannot access this course');
		// 	$this->output->set_content_type('application/json');
		// 	$this->output->set_output(json_encode($output));
		// 	return;
		// }

		$match = array(
			"\$match" => array(
				"statement.actor.name" => array("\$eq" => $keepId),
				"statement.verb.id" => array("\$eq" => "http://adlnet.gov/expapi/verbs/completed"),
				"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.component" => array("\$eq" => "mod_assign"),
				"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.courseid" => array("\$eq" => $courseId),
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
					"asg_name" => "\$statement.object.definition.name.en"
				)
			),
		);

		$pipeline = array(
			"moodle" => array($match, $sort, $group),
		);
		$output = $this->datamodel->getData($pipeline);
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($output));
	}

	public function getAsgDisStu(){
		$courseId = $this->input->get('courseId');
		$platform = $this->input->get('platform');
		$asg = $this->input->get('asg');
		$asg = str_replace("%20", " ", $asg);
		// -- TO MODIFY
		// if(!$this->checkCourseAcc($courseId, $platform)){
		// 	$output = array('ok' => false, 'message' => 'You cannot access this course');
		// 	$this->output->set_content_type('application/json');
		// 	$this->output->set_output(json_encode($output));
		// 	return;
		// }

		$match = array(
			"\$match" => array(
				"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.eventname" => array("\$eq" => "\\core\\event\\user_graded"),
				"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.courseid" => array("\$eq" => $courseId),
				"statement.verb.id" => array("\$eq" => "http://www.tincanapi.co.uk/verbs/evaluated"),
				"statement.context.contextActivities.grouping" => array(
					"\$elemMatch" => array("definition.name.en" => array("\$eq" => $asg))
				),
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

		$pipeline = array(
			"moodle" => array($match, $sort, $project),
		);
		$output = $this->datamodel->getData($pipeline);

		//find out users' score --TO MODIFY

		//instructor can make multiple assessments to one assignment,
		//so we need to delete the redundance record
		$newData = array();
		$lastId = 0;
		for($i = 0; $i < count($output['data']['moodle']['result']); $i++){
			if($lastId != $output['data']['moodle']['result'][$i]['statement']['object']['name']){
				array_push($newData, $output['data']['moodle']['result'][$i]);
				$lastId = $output['data']['moodle']['result'][$i]['statement']['object']['name'];
			}
		}
		$output['data'] = $newData;
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($output));

	}

	public function getEngagement($from = "2015-01-01", $to = "2020-12-31"){
		//this id should be return from the login information -- TO MODIFY
		//$keepId = "563a82e2-96ed-11e4-bf37-080027087aa9";
		$keepId = "fb4d945c-1964-4055-a833-8d843eea3c76";

		$courseId = str_replace(" ", "+", $this->input->get('courseId'));
		$platform = $this->input->get('platform');

		$from = $from . "T00:00";
		$to = $to . "T00:00";

		if($platform == 'moodle'){
			$eventMapping = array(
				'\\mod_resource\\event\\course_module_viewed' => 'View File',
				'\\local_youtube_events\\event\\video_played' => 'Watch Video',
				'\\mod_forum\\event\\discussion_created' => 'Create Post',
				'\\mod_forum\\event\\discussion_viewed' => 'View Post',
				'\\mod_forum\\event\\post_created' => 'Reply Post',
				'\\mod_assign\\event\\submission_status_viewed' => 'View Assignment',
				'\\mod_assign\\event\\assessable_submitted' => 'Submit Assignment',
				'\\mod_quiz\\event\\attempt_started' => 'Attempt Quiz',
			);

			$match = array(
				"\$match" => array(
					"statement.actor.name" => array("\$eq" => $keepId),
					"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.courseid" => array("\$eq" => $courseId),
					"\$or" => array(
						array(
						"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.eventname" => array("\$eq" => "\\mod_resource\\event\\course_module_viewed"),
						),//view file					
						array(
						"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.eventname" => array("\$eq" => "\\local_youtube_events\\event\\video_played"),
						),//play a video
						array(
						"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.eventname" => array("\$eq" => "\\mod_forum\\event\\discussion_created"),
						),//create new post
						array(
						"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.eventname" => array("\$eq" => "\\mod_forum\\event\\discussion_viewed"),
						),//view post
						array(
						"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.eventname" => array("\$eq" => "\\mod_forum\\event\\post_created"),
						),//reply to a post
						array(
						"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.eventname" => array("\$eq" => "\\mod_assign\\event\\submission_status_viewed"),
						),//view an asg
						array(
						"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.eventname" => array("\$eq" => "\\mod_assign\\event\\assessable_submitted"),
						),//submit an asg
						array(
						"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.eventname" => array("\$eq" => "\\mod_quiz\\event\\attempt_started"),
						),//attempt a quiz
					),
					
					"statement.timestamp" =>array(
						"\$gte" => $from,
						"\$lt" => $to,
					),
				),
			);
			
			$group = array(
				"\$group" => array(
					"_id" => array(
						"eventname" => "\$statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.eventname",
						"date" => array("\$substr"=>array(
								"\$statement.timestamp", 0, 10,
							),
						)
					),
					"numOfEvent" => array("\$sum" => 1),
				)
			);
			$sortEvent = array(
				"\$sort" => array(
					"_id.eventname" => 1
				)
			);
			$pipeline = array(
				"moodle" => array($match, $group, $sortEvent)
			); 
			$output = $this->datamodel->getData($pipeline);

			$dataProcess = array();
			$temp = $output['data']['moodle']['result'];
			for($i = 0; $i < count($temp); $i++){
				if(!isset($dataProcess[$temp[$i]['_id']['date']])){
					$dataProcess[$temp[$i]['_id']['date']] = array();
					foreach($eventMapping as $raw => $event){
						$dataProcess[$temp[$i]['_id']['date']][$event] = 0;
					}
				}
				$dataProcess[$temp[$i]['_id']['date']][$eventMapping[$temp[$i]['_id']['eventname']]] = $temp[$i]['numOfEvent'];
			}

			$newData = array();
			foreach($dataProcess as $date => $event){
				$t = array('date' => $date);
				foreach($event as $name => $num){
					$t[$name] = $num;
				}
				array_push($newData, $t);
			}
			$ykeys = array();
			foreach($eventMapping as $raw => $event){
				array_push($ykeys, $event);
			}
			//$output['dataProcess'] = $dataProcess;
			$output['data']['data'] = $newData;
			$output['data']['ykeys'] = $ykeys;

			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode($output));
		}else if($platform == 'edx'){
			$match = array(
				"\$match" => array(
					"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.courseid" => array("\$eq" => $courseId),
					"statement.actor.name" => array("\$eq" => $keepId),
					"statement.timestamp" =>array(
						"\$gte" => $from,
						"\$lt" => $to,
					),
					"\$nor" => array(
						array("statement.verb.display.en-US" => "interacted with"),
						array("statement.verb.display.en-US" => "enrolled onto"),
						array("statement.verb.display.en-US" => "logged in to"),
					),
					//"statement.verb.display.en-US" => array("\$not" => "/interacted/"),
				),
			);
			$sortDate = array(
				"\$sort" => array(
					"statement.timestamp" => -1,
				)
			);
			$group = array(
				"\$group" => array(
					"_id" => array(
						"eventname" => array(
							"\$concat" => array("\$statement.verb.display.en-US", " ", "\$statement.object.definition.name.en-US")
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
				"edx" => array($match, $group, $sortEvent)
			); 
			$output = $this->datamodel->getData($pipeline);

			$ykeys = array();
			$dataProcess = array();
			$preDate = "";
			$preEventname = "";
			for($i = 0; $i < count($output['data']['edx']['result']); $i++){
				if($output['data']['edx']['result'][$i]['_id']['date'] != $preDate){
					$preDate = $output['data']['edx']['result'][$i]['_id']['date'];
					$dataProcess[$output['data']['edx']['result'][$i]['_id']['date']] = array();
				}
				$dataProcess[$output['data']['edx']['result'][$i]['_id']['date']][$output['data']['edx']['result'][$i]['_id']['eventname']] = $output['data']['edx']['result'][$i]['count'];

				//generate ykeys
				$flag = false;
				for($j = 0; $j < count($ykeys); $j++){
					if($ykeys[$j] == $output['data']['edx']['result'][$i]['_id']['eventname']){
						$flag = true;
						break;
					}
				}
				if(!$flag){
					array_push($ykeys, $output['data']['edx']['result'][$i]['_id']['eventname']);
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
			//$output['data']['edx']['result'] = [];

			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode($output));
		}
	}
















	private function checkCourseAcc($courseId, $platform){
		if(isset($this->courseInfo['data'][$platform])){
			foreach($this->courseInfo['data'][$platform]['results'] as $course){
				if($course['course_id'] == $courseId){
					return true;
				}
			}
		}
		return false;
	}
}