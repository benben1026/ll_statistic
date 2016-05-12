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
	
	public function test(){
		$match = array(
			"\$match" => array(
				"statement.verb.display.en-US" => array("\$eq" => "evaluated"),

				// "statement.timestamp" =>array(
				// 		"\$gte" => "2016-03-20T00:00",
				// 		"\$lt" => "2016-05-04T00:00",
				// 	),
			),
		);
		$project = array(
			"\$project" => array(
				"statement.verb" => 1,
				"statement.object" => 1
			)
		);
		$pipeline = array("edx" => array($match, $project));
		$output = $this->datamodel->getData($pipeline);
		echo json_encode($output);
	}

	/***************************** API Prepared for Overview Page ***********************************/

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
					"statement.verb.id" => array("\$eq" => "http://id.tincanapi.com/verb/viewed"),
					"statement.object.id" => array("\$regex" => "/discussion/forum/course/threads/.", "\$options" => "i"),
				),
			);
			$edx_group = array(
				"\$group" => array(
					"_id" => array(
						//----------------TO DO-------------------
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
		$output = $this->datamodel->getData($pipeline);
		$result = array();

		//merge data from moodle and edx
		if(array_key_exists('edx', $output['data'])){
			for($i = 0; $i < count($output['data']['edx']['result']); $i++){
				$forum_name = array_key_exists("forum_name", $output['data']['edx']['result'][$i]['_id']) ? $output['data']['edx']['result'][$i]['_id']['forum_name'] : "Forum";
				$temp = array(
					$i + 1,
					"<a target=\"_blank\" href=\"" . $output['data']['edx']['result'][$i]['_id']['forum_id'] . "\">" . $forum_name . "</a>",
					$output['data']['edx']['result'][$i]['_id']['course_name'],
					'KEEP edX',
					$output['data']['edx']['result'][$i]['count'],
				);
				array_push($result, $temp);
			}
		}
		if(array_key_exists('moodle', $output['data'])){
			//TO DO
		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode(array('data' => $result)));
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
		$output = $this->datamodel->getData($pipeline);

		$result = array();
		if(array_key_exists('edx', $output['data'])){
			for($i = 0; $i < count($output['data']['edx']['result']); $i++){
				$forum_name = array_key_exists("forum_name", $output['data']['edx']['result'][$i]['_id']) ? $output['data']['edx']['result'][$i]['_id']['forum_name'] : "Forum";
				$temp = array(
					$i + 1,
					"<a target=\"_blank\" href=\"" . $output['data']['edx']['result'][$i]['_id']['forum_id'] . "\">" . $forum_name . "</a>",
					array_key_exists('course_name', $output['data']['edx']['result'][$i]['_id']) ? $output['data']['edx']['result'][$i]['_id']['course_name'] : 'Not Available',
					'KEEP edX',
					$output['data']['edx']['result'][$i]['count'],
				);
				array_push($result, $temp);
			}
		}
		if(array_key_exists('moodle', $output['data'])){
			//TO DO
		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode(array('data' => $result)));
		
	}

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
					"statement.verb.id" => array(
						"\$eq" => "http://id.tincanapi.com/verb/viewed"
					),
					"statement.object.definition.name.en-US" => array("\$eq" => "a courseware asset"),					
				),
			);
			$edx_group = array(
				"\$group" => array(
					"_id" => array(
						"file_id" => "\$statement.object.id",
						"file_name" => "\$statement.object.definition.description.en-US",
						"file_name2" => "\$statement.object.definition.name.en-US"
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

		//TO DO: merge data from edx and moodle
		if(array_key_exists('edx', $output['data'])){

		}
		if(array_key_exists('moodle', $output['data'])){

		}

		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($output));
	}

	public function getFileViewingAccToTime($filename, $from = "2015-01-01", $to = "2017-01-01"){
		$filename = str_replace("%20", " ", $filename);
		$from = $from . "T00:00";
		$to = $to . "T00:00";
		$pipeline = array();

		//statement prepared for moodle
		if(($platform == "all" || $platform == "moodle") && isset($this->courseInfo['data']['moodle']['total_results'])){
			//TO DO
		}

		//statement prepared for edx
		if(($platform == "all" || $platform == "edx") && isset($this->courseInfo['data']['edx']['total_results'])){
			$edx_match = array(
				"\$match" => array(
					"statement.object.definition.name.en" => array(
						"\$eq" => $filename
					),
					"statement.verb.id" => array(
						"\$eq" => "http://id.tincanapi.com/verb/viewed"
					),
					"statement.timestamp" =>array(
						"\$gte" => $from,
						"\$lte" => $to,
					),
				),
			);

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
			//TO DO rearrange data to fit the frontend library
		}
		$output = $this->datamodel->getData($pipeline);
		//TO DO: merge data from edx and moodle
		if(array_key_exists('edx', $output['data'])){

		}
		if(array_key_exists('moodle', $output['data'])){

		}
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($output));
	}

	public function getVideoWatching($platform = "all"){
		//TO DO: we cannot get the video name in statement
	}


/********************** API Prepared for Course Detail Page ****************************/
	public function getAsgList(){
		//only moodle has assignment
		$courseId = $this->input->get('courseId');
		$platform = $this->input->get('platform');
		$keepId = $this->session->userdata('samlUserData')['keepid'][0];
		if($keepId == null || !$this->checkCourseAcc($courseId, $platform)){
			$output = array('ok' => false, 'message' => 'You cannot access this course');
			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode($output));
			return;
		}

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
		//only moodle has assignment
		$courseId = $this->input->get('courseId');
		$platform = $this->input->get('platform');
		$asg = $this->input->get('asg');
		$asg = str_replace("%20", " ", $asg);
		// -- TO DO
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
		//TO DO: check privilege
		$keepId = $this->session->userdata('samlUserData')['keepid'][0];

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
						"\$lte" => $to,
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
						"\$lte" => $to,
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

	public function getCourseForum(){
		$courseId = str_replace(" ", "+", $this->input->get('courseId'));
		$platform = $this->input->get('platform');
		//$type can be 'view', 'reply'
		$type = $this->input->get('type');

		if($platform == 'moodle'){

		}else if($platform == 'edx'){
			if($type != "active"){
				$edx_match = array(
					"\$match" => array(
						"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.courseid" => $courseId,
						"statement.verb.id" => array("\$eq" => $type == "view" ? "http://id.tincanapi.com/verb/viewed" : "http://adlnet.gov/expapi/verbs/responded"),
						"statement.object.id" => array("\$regex" => $type == "view" ? "/discussion/forum/course/threads/." : "/discussion/", "\$options" => "i"),
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
				
			}else{
				$edx_match = array(
					"\$match" => array(
						"\$or" => array(
							array("statement.verb.display.en-US" => array("\$eq" => "created")),
							array("statement.verb.display.en-US" => array("\$eq" => "responded to")),
							array("statement.verb.display.en-US" => array("\$eq" => "updated")),
						),
						"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.courseid" => $courseId,
						"statement.object.id" => array("\$regex" => "/discussion/", "\$options" => "i"),
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
				// $output = $this->datamodel->getData($pipeline);
				// $this->output->set_content_type('application/json');
				// $this->output->set_output(json_encode($output));
			}
			$output = $this->datamodel->getData($pipeline);
			$result = array();
			for($i = 0; $i < count($output['data'][$platform]['result']); $i++){
				$forum_name = array_key_exists("forum_name", $output['data'][$platform]['result'][$i]['_id']) ? $output['data'][$platform]['result'][$i]['_id']['forum_name'] : "Forum";
				//$forum_name = "Forum";
				$temp = array(
					$i + 1,
					"<a target=\"_blank\" href=\"" . $output['data'][$platform]['result'][$i]['_id']['forum_id'] . "\">" . $forum_name . "</a>",
					$output['data'][$platform]['result'][$i]['count'],
				);
				array_push($result, $temp);
			}
			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode(array("data" => $result)));
			
		}
		
	}

	public function getCourseFileView(){
		$courseId = str_replace(" ", "+", $this->input->get('courseId'));
		$platform = $this->input->get('platform');
		$pipeline = array();

		if($platform == 'moodle'){

		}else if($platform == 'edx'){
			$edx_match = array(
				"\$match" => array(
					"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.courseid" => array(
						"\$eq" => $courseId
					),
					"statement.verb.id" => array(
						"\$eq" => "http://id.tincanapi.com/verb/viewed"
					),
					"\$or" => array(
						array("statement.object.id" => array("\$regex" => "asset", "\$options" => "i")),
						//array("statement.object.id" => array("\$regex" => "/courseware/", "\$options" => "i")),
					),
					
				),
			);
			$edx_group = array(
				"\$group" => array(
					"_id" => array(
						"file_id" => "\$statement.object.id",
						"file_name" => "\$statement.object.definition.description.en-US",
						"file_name2" => "\$statement.object.definition.name.en-US"
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

	public function getCourseEngagement($from = "2015-01-01", $to = "2020-12-31"){
		$keepId = $this->session->userdata('samlUserData')['keepid'][0];

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
					"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.role" => array("\$eq" => "student"),
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
						"\$lte" => $to,
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
					"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.role" => array("\$eq" => "student"),
					"statement.timestamp" =>array(
						"\$gte" => $from,
						"\$lte" => $to,
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

	public function getCourseAddDrop($from = "2015-01-01", $to = "2020-12-31"){
		$keepId = $this->session->userdata('samlUserData')['keepid'][0];

		$courseId = str_replace(" ", "+", $this->input->get('courseId'));
		$platform = $this->input->get('platform');

		$from = $from . "T00:00";
		$to = $to . "T00:00";

		if($platform == 'moodle'){

		}else if($platform == 'edx'){
			$edx_match = array(
				"\$match" => array(
					"statement.timestamp" => array(
							"\$gte" => $from,
							"\$lte" => $to,
						),
					"\$or" => array(
						array("statement.verb.id" => array("\$eq" => "http://www.tincanapi.co.uk/verbs/enrolled_onto_learning_plan")),
						array("statement.verb.id" => array("\$eq" => "http://activitystrea.ms/schema/1.0/leave")),
					),
					"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.courseid" => array("\$eq" => $courseId),
					"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.role" => array("\$eq" => "student"),
				)
			);
			$edx_group = array(
				"\$group" => array(
					"_id" => array(
						"event" => "\$statement.verb.display.en-US",
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
			$pipeline['edx'] = array($edx_match, $edx_group, $sortDate);
			$output = $this->datamodel->getData($pipeline);
			$dataProcess = array();
			$preDate = "";
			$lastIndex = 0;
			for($i = 0; $i < count($output['data']['edx']['result']); $i++){
				if($output['data']['edx']['result'][$i]['_id']['date'] != $preDate){
					$temp = array(
						"date" => $output['data']['edx']['result'][$i]['_id']['date'],
						"Enroll" => 0,
						"Drop" => 0
					);
					array_push($dataProcess, $temp);
				}
				if($output['data']['edx']['result'][$i]['_id']['event'] == "enrolled onto"){
					$dataProcess[count($dataProcess) - 1]['Enroll'] = $output['data']['edx']['result'][$i]['count'];
				}else{
					$dataProcess[count($dataProcess) - 1]['Drop'] = $output['data']['edx']['result'][$i]['count'];
				}
			}
			$output['data'] = $dataProcess;
			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode($output));
		}
	}

	public function getStudentCourseVitality($from = "2015-01-01", $to = "2020-12-31"){
		$keepId = $this->session->userdata('samlUserData')['keepid'][0];

		$courseId = str_replace(" ", "+", $this->input->get('courseId'));
		$platform = $this->input->get('platform');

		$from = $from . "T00:00";
		$to = $to . "T00:00";

		if($platform == 'moodle'){

		}else if($platform == 'edx'){
			$match = array(
				"\$match" => array(
					"statement.timestamp" => array(
						"\$gte" => $from,
						"\$lte" => $to,
					),
					"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.courseid" => array("\$eq" => $courseId),
					"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.role" => array("\$eq" => "student"),
					"\$or" => array(
						//view a courseware
						array("\$and" => array(
											array("statement.verb.id" => array("\$eq" => "http://id.tincanapi.com/verb/viewed")),
											array("statement.object.definition.name.en-US" => array("\$eq" => "a courseware page"))
										)
						),
						//start playing a video
						array("statement.verb.display.en-US" => array("\$eq" => "started playing")),
						//view a thread
						array("\$and" => array(
											array("statement.verb.id" => array("\$eq" => "http://id.tincanapi.com/verb/viewed")),
											array("statement.object.definition.name.en-US" => array("\$eq" => "a discussion thread"))
										)
						),
						//create a thread
						array(
							"statement.verb.display.en-US" => array("\$eq" => "created"),
						),
						//reply a thread
						array(
							"statement.verb.display.en-US" => array("\$eq" => "responded to"),
						),
						//vote a thread
						array(
							"statement.verb.display.en-US" => array("\$eq" => "up voted"),
						),
						array(
							"statement.verb.display.en-US" => array("\$eq" => "down voted"),
						),
						//complete a problem
						array(
							"statement.verb.display.en-US" => array("\$eq" => "completed"),
						),
					)
				),
			);
			$group = array(
				"\$group" => array(
					"_id" => array(
						"id" => "\$statement.actor.name",
						"name" => "\$statement.actor.account.name",
						"verb" => "\$statement.verb.display.en-US",
						"object" => "\$statement.object.definition.name.en-US"
					),
					"count" => array("\$sum" => 1)
				),
			);
			$sort = array(
				"\$sort" => array(
					"_id.id" => 1
				)
			);
			$pipeline['edx'] = array($match, $group, $sort, array("\$limit" => 20000));
			$output = $this->datamodel->getData($pipeline);
			if(!$output['ok']){
				echo false;
				return;
			}
			$lastStu = "";
			$currentIndex = -1;
			$dataProcess = array();
			for($i = 0; $i < count($output['data']['edx']['result']); $i++){
				if($output['data']['edx']['result'][$i]['_id']['id'] != $lastStu){
					array_push($dataProcess, array(
						$output['data']['edx']['result'][$i]['_id']['name'],0,0,0,0,0,0,0,0,
					));
					$lastStu = $output['data']['edx']['result'][$i]['_id']['id'];
					$currentIndex += 1;
				}
				$coefficient = 0;
				if($output['data']['edx']['result'][$i]['_id']['verb'] == 'viewed' && $output['data']['edx']['result'][$i]['_id']['object'] == 'a courseware page'){
					//$dataProcess[$currentIndex]['Viewed a Courseware'] = $output['data']['edx']['result'][$i]['count'];
					$dataProcess[$currentIndex][1] = $output['data']['edx']['result'][$i]['count'];
					$coefficient = 1;
				}else if($output['data']['edx']['result'][$i]['_id']['verb'] == 'started playing'){
					//$dataProcess[$currentIndex]['Watched a Video'] = $output['data']['edx']['result'][$i]['count'];
					$dataProcess[$currentIndex][2] = $output['data']['edx']['result'][$i]['count'];
					$coefficient = 1;
				}else if($output['data']['edx']['result'][$i]['_id']['verb'] == 'viewed' && $output['data']['edx']['result'][$i]['_id']['object'] == 'a discussion thread'){
					//$dataProcess[$currentIndex]['Viewed a Thread'] = $output['data']['edx']['result'][$i]['count'];
					$dataProcess[$currentIndex][3] = $output['data']['edx']['result'][$i]['count'];
					$coefficient = 1;
				}else if($output['data']['edx']['result'][$i]['_id']['verb'] == 'created'){
					//$dataProcess[$currentIndex]['Created a Thread'] = $output['data']['edx']['result'][$i]['count'];
					$dataProcess[$currentIndex][4] = $output['data']['edx']['result'][$i]['count'];
					$coefficient = 10;
				}else if($output['data']['edx']['result'][$i]['_id']['verb'] == 'responded to'){
					//$dataProcess[$currentIndex]['Replied to a Thread'] = $output['data']['edx']['result'][$i]['count'];
					$dataProcess[$currentIndex][5] = $output['data']['edx']['result'][$i]['count'];
					$coefficient = 8;
				}else if($output['data']['edx']['result'][$i]['_id']['verb'] == 'up voted' || $output['data']['edx']['result'][$i]['_id']['verb'] == 'down voted'){
					//$dataProcess[$currentIndex]['Voted a Thread'] += $output['data']['edx']['result'][$i]['count'];
					$dataProcess[$currentIndex][6] += $output['data']['edx']['result'][$i]['count'];
					$coefficient = 5;
				}else if($output['data']['edx']['result'][$i]['_id']['verb'] == 'completed'){
					//$dataProcess[$currentIndex]['Completed a Problem'] = $output['data']['edx']['result'][$i]['count'];
					$dataProcess[$currentIndex][7] = $output['data']['edx']['result'][$i]['count'];
					$coefficient = 5;
				}
				//$dataProcess[$currentIndex]['Total Score'] += $coefficient * $output['data']['edx']['result'][$i]['count'];
				$dataProcess[$currentIndex][8] += $coefficient * $output['data']['edx']['result'][$i]['count'];
			}


			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode(array('data' => $dataProcess)));
		}
	}

	public function getCourseEnrollStudent($from = "2015-01-01", $to = "2020-12-31"){
		$keepId = $this->session->userdata('samlUserData')['keepid'][0];

		$courseId = str_replace(" ", "+", $this->input->get('courseId'));
		$platform = $this->input->get('platform');

		$from = $from . "T00:00";
		$to = $to . "T00:00";

		if($platform == 'moodle'){

		}else if($platform == 'edx'){
			$match = array(
				"\$match" => array(
					"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.courseid" => array("\$eq" => $courseId),
					"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.role" => array("\$eq" => "student"),
					"\$or" => array(
						array("statement.verb.id" => array("\$eq" => "http://www.tincanapi.co.uk/verbs/enrolled_onto_learning_plan")),
						array("statement.verb.id" => array("\$eq" => "http://activitystrea.ms/schema/1.0/leave")),
					),
					"statement.timestamp" => array(
						"\$gte" => $from,
						"\$lte" => $to,
					),
				)
			);
			$group = array(
				"\$group" => array(
					"_id" => array(
						"verb" => "\$statement.verb.display.en-US"
					),
					"count" => array("\$sum" => 1),
				)
			);
			$pipeline['edx'] = array($match, $group);
			$output = $this->datamodel->getData($pipeline);
			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode($output));
		}
	}

	public function getPersonalPerformance(){
		$keepId = $this->session->userdata('samlUserData')['keepid'][0];

		$courseId = str_replace(" ", "+", $this->input->get('courseId'));
		$platform = $this->input->get('platform');
		if($platform == 'moodle'){

		}else if($platform == 'edx'){
			//get total number of students
			$match = array(
				"\$match" => array(
					"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.courseid" => array("\$eq" => $courseId),
					"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.role" => array("\$eq" => "student"),
					"statement.verb.id" => array("\$eq" => "http://www.tincanapi.co.uk/verbs/enrolled_onto_learning_plan")
				)
			);
			$group = array(
				"\$group" => array(
					"_id" => array(
						"verb" => "\$statement.verb.display.en-US"
					),
					"count" => array("\$sum" => 1),
				)
			);
			$pipeline['edx'] = array($match, $group);
			$output = $this->datamodel->getData($pipeline);
			$totalNumOfStudent = $output['ok'] && $output['data']['edx']['ok'] ? $output['data']['edx']['result'][0]['count'] : 0;
			if($totalNumOfStudent == 0){
				$this->output->set_content_type('application/json');
				$this->output->set_output(json_encode(array("ok" => false)));
				return;
			}

			//get the average number of each action
			$match = array(
				"\$match" => array(
					"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.courseid" => array("\$eq" => $courseId),
					"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.role" => array("\$eq" => "student"),
					"\$or" => array(
						//view a courseware
						array("\$and" => array(
											array("statement.verb.id" => array("\$eq" => "http://id.tincanapi.com/verb/viewed")),
											array("statement.object.definition.name.en-US" => array("\$eq" => "a courseware page"))
										)
						),
						//start playing a video
						array("statement.verb.display.en-US" => array("\$eq" => "started playing")),
						//view a thread
						array("\$and" => array(
											array("statement.verb.id" => array("\$eq" => "http://id.tincanapi.com/verb/viewed")),
											array("statement.object.definition.name.en-US" => array("\$eq" => "a discussion thread"))
										)
						),
						//create a thread
						array(
							"statement.verb.display.en-US" => array("\$eq" => "created"),
						),
						//reply a thread
						array(
							"statement.verb.display.en-US" => array("\$eq" => "responded to"),
						),
						//vote a thread
						array(
							"statement.verb.display.en-US" => array("\$eq" => "up voted"),
						),
						array(
							"statement.verb.display.en-US" => array("\$eq" => "down voted"),
						),
						//complete a problem
						array(
							"statement.verb.display.en-US" => array("\$eq" => "completed"),
						),
					)
				),
			);
			$group = array(
				"\$group" => array(
					"_id" => array(
						"verb" => "\$statement.verb.display.en-US",
						"object" => "\$statement.object.definition.name.en-US"
					),
					"count" => array("\$sum" => 1)
				),
			);
			$sort = array(
				"\$sort" => array("_id.verb" => 1, "_id.object" => 1),
			);
			$pipeline['edx'] = array($match, $group, $sort);
			$total = $this->datamodel->getData($pipeline);

			//get personal actions
			$match = array(
				"\$match" => array(
					"statement.actor.name" => array("\$eq" => $keepId),
					"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.courseid" => array("\$eq" => $courseId),
					"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.role" => array("\$eq" => "student"),
					"\$or" => array(
						//view a courseware
						array("\$and" => array(
											array("statement.verb.id" => array("\$eq" => "http://id.tincanapi.com/verb/viewed")),
											array("statement.object.definition.name.en-US" => array("\$eq" => "a courseware page"))
										)
						),
						//start playing a video
						array("statement.verb.display.en-US" => array("\$eq" => "started playing")),
						//view a thread
						array("\$and" => array(
											array("statement.verb.id" => array("\$eq" => "http://id.tincanapi.com/verb/viewed")),
											array("statement.object.definition.name.en-US" => array("\$eq" => "a discussion thread"))
										)
						),
						//create a thread
						array(
							"statement.verb.display.en-US" => array("\$eq" => "created"),
						),
						//reply a thread
						array(
							"statement.verb.display.en-US" => array("\$eq" => "responded to"),
						),
						//vote a thread
						array(
							"statement.verb.display.en-US" => array("\$eq" => "up voted"),
						),
						array(
							"statement.verb.display.en-US" => array("\$eq" => "down voted"),
						),
						//complete a problem
						array(
							"statement.verb.display.en-US" => array("\$eq" => "completed"),
						),
					)
				),
			);
			$group = array(
				"\$group" => array(
					"_id" => array(
						"verb" => "\$statement.verb.display.en-US",
						"object" => "\$statement.object.definition.name.en-US"
					),
					"count" => array("\$sum" => 1)
				),
			);
			
			$pipeline['edx'] = array($match, $group, $sort);
			$personal = $this->datamodel->getData($pipeline);
			$averageData = array(0, 0, 0, 0, 0, 0, 0);
			$personalData = array(0, 0, 0, 0, 0, 0, 0);

			//find personal
			for($i = 0; $i < count($personal['data']['edx']['result']); $i++){				
				if($personal['data']['edx']['result'][$i]['_id']['verb'] == 'viewed' && $personal['data']['edx']['result'][$i]['_id']['object'] == 'a courseware page'){
					$personalData[0] = $personal['data']['edx']['result'][$i]['count'];
				}else if($personal['data']['edx']['result'][$i]['_id']['verb'] == 'started playing'){
					$personalData[1] = $personal['data']['edx']['result'][$i]['count'];
				}else if($personal['data']['edx']['result'][$i]['_id']['verb'] == 'viewed' && $personal['data']['edx']['result'][$i]['_id']['object'] == 'a discussion thread'){
					$personalData[2] = $personal['data']['edx']['result'][$i]['count'];
				}else if($personal['data']['edx']['result'][$i]['_id']['verb'] == 'created'){
					$personalData[3] = $personal['data']['edx']['result'][$i]['count'];
				}else if($personal['data']['edx']['result'][$i]['_id']['verb'] == 'responded to'){
					$personalData[4] = $personal['data']['edx']['result'][$i]['count'];
				}else if($personal['data']['edx']['result'][$i]['_id']['verb'] == 'up voted' || $output['data']['edx']['result'][$i]['_id']['verb'] == 'down voted'){
					$personalData[5] += $personal['data']['edx']['result'][$i]['count'];
				}else if($personal['data']['edx']['result'][$i]['_id']['verb'] == 'completed'){
					$personalData[6] = $personal['data']['edx']['result'][$i]['count'];
				}
			}

			//find average
			for($i = 0; $i < count($total['data']['edx']['result']); $i++){
				if($total['data']['edx']['result'][$i]['_id']['verb'] == 'viewed' && $total['data']['edx']['result'][$i]['_id']['object'] == 'a courseware page'){
					$averageData[0] = number_format($total['data']['edx']['result'][$i]['count'] / $totalNumOfStudent, 3);
				}else if($total['data']['edx']['result'][$i]['_id']['verb'] == 'started playing'){
					$averageData[1] = number_format($total['data']['edx']['result'][$i]['count'] / $totalNumOfStudent, 3);
				}else if($total['data']['edx']['result'][$i]['_id']['verb'] == 'viewed' && $total['data']['edx']['result'][$i]['_id']['object'] == 'a discussion thread'){
					$averageData[2] = number_format($total['data']['edx']['result'][$i]['count'] / $totalNumOfStudent, 3);
				}else if($total['data']['edx']['result'][$i]['_id']['verb'] == 'created'){
					$averageData[3] = number_format($total['data']['edx']['result'][$i]['count'] / $totalNumOfStudent, 3);
				}else if($total['data']['edx']['result'][$i]['_id']['verb'] == 'responded to'){
					$averageData[4] = number_format($total['data']['edx']['result'][$i]['count'] / $totalNumOfStudent, 3);
				}else if($total['data']['edx']['result'][$i]['_id']['verb'] == 'up voted' || $total['data']['edx']['result'][$i]['_id']['verb'] == 'down voted'){
					if($averageData[5] == 0){
						$averageData[5] = $total['data']['edx']['result'][$i]['count'] / $totalNumOfStudent;
					}else{
						$averageData[5] = ($averageData[5] * $totalNumOfStudent + $total['data']['edx']['result'][$i]['count']) / $totalNumOfStudent;
					}
					
				}else if($total['data']['edx']['result'][$i]['_id']['verb'] == 'completed'){
					$averageData[6] = number_format($total['data']['edx']['result'][$i]['count'] / $totalNumOfStudent, 3);
				}
			}
			$averageData[5] = number_format($averageData[5], 3);
			$averageData = array_map('floatval', $averageData);

			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode(array("personal" => $personalData, "average" => $averageData)));
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