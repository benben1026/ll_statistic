<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once (dirname(__FILE__) . "/CourseInfo.php");

class Learninglocker extends CourseInfo{
	private $domain ;
	private $authentication;
	private $courseInfo;

	function __construct(){
		parent::__construct();
		$this->courseInfo = $this->getUserCourseList("all");
		$this->load->model('datamodel');
	}

	public function getEngagement($keepId, $from = "2015-01-01", $to = "2020-12-31"){
		//$from = DateTime::createFromFormat('Y-m-d', $from);
		//$to = DateTime::createFromFormat('Y-m-d', $to);
		$from = $from . "T00:00";
		$to = $to . "T00:00";
		$match = array(
			"\$match" => array(
				"statement.actor.name" => array(
					//"\$eq" => "stud01"
					"\$eq" => $keepId
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
		$sort = array(
			"\$sort" => array(
				"statement.timestamp" => -1,
			)
		);
		$group = array(
			"\$group"=>array(
				"_id"=>array(
					"date"=>array(
						"\$substr"=>array(
							"\$statement.timestamp", 0, 10,
						),
					),
				),
				"value"=>array(
					"\$sum"=>1,
				),
			),
		);
		$project = array(
			"\$project" => array(
				"_id" => 0,
			)
		);
		$limit = array(
			"\$limit" => 5,
		);
		$pipeline = array(
			"moodle" => array($match, $group, $sort),
			"edx" => array($match, $group, $sort)
		); 
		$output = $this->datamodel->getData($pipeline, "all");
		
		// foreach($this->authentication as $key => $value){
		// 	//$temp = $this->datamodel->getData($this->domain, $value, "?pipeline=" . json_encode($pipeline) . "");
		// 	$temp = $this->datamodel->getInternalData($this->domain, $value, "?pipeline=" . json_encode($pipeline) . "");
		// 	//$temp = $this->datamodel->getData($pipeline);
		// 	//$temp = json_decode($temp);
		// 	//$temp = get_object_vars($temp);
		// 	$temp['LRS'] = $key;
		// 	$result = array();
		// 	$element = array();
		// 	foreach($temp['result'] as $k => $v){
		// 		//$v = get_object_vars($v);
		// 		//$v['_id'] = get_object_vars($v['_id']);
		// 		$element = array(
		// 			"date" => $v['_id']['date'],
		// 			"value" => $v['value'],
		// 		);
		// 		array_push($result, $element);
		// 	}
		// 	$temp['result'] = $result;
		// 	if($temp !== FALSE){
		// 		array_push($output, $temp);
		// 	}
		// }
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($output));
		
	}

	//$courseId should be an array consists of a list of course Id
	public function getFileViewing($platform = "all"){
		$courseId_json = $this->input->post("courseId");
		$courseId = json_decode($courseId_json);
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
					"\$eq" => "http://id.tincanapi.com/verb/viewed"
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

	public function getFileViewingAccToTime($filename, $from = "2015-01-01", $to = "2017-01-01"){
		$filename = str_replace("%20", " ", $filename);;
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
}