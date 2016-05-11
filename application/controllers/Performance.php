<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Performance extends CI_Controller{
	private $returnData = array(
		"ok" => false,
		"message" => "",
		"data" => null
	);

	function __contruct(){
		parent::__contruct();
	}

	public function stuPerformance(){
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

		if($this->apimodel->getPlatform() == 'moodle'){
			//TO DO
		}else if($this->apimodel->getPlatform() == 'edx'){
			//get total number of students
			$match = array(
				"\$match" => array(
					"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.courseid" => array("\$eq" => $this->apimodel->getCourseId()),
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
			if(!$output['ok']){
				$this->returnData['ok'] = false;
				$this->returnData['message'] = $output['message'];
				printJson($this->returnData);
				return;
			}
			$totalNumOfStudent = $output['ok'] && $output['data']['edx']['ok'] ? $output['data']['edx']['result'][0]['count'] : 0;
			if($totalNumOfStudent == 0){
				$this->returnData['ok'] = false;
				$this->returnData['message'] = "There is no student in this course";
				printJson($this->returnData);
				return;
			}

			//get the average number of each action
			$match = array(
				"\$match" => array(
					"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.courseid" => array("\$eq" => $this->apimodel->getCourseId()),
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
			if(!$total['ok']){
				$this->returnData['ok'] = false;
				$this->returnData['message'] = $total['message'];
				printJson($this->returnData);
				return;
			}

			//get personal actions
			$match = array(
				"\$match" => array(
					"statement.actor.name" => array("\$eq" => $this->apimodel->getKeepId()),
					"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.courseid" => array("\$eq" => $this->apimodel->getCourseId()),
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
			if(!$personal['ok']){
				$this->returnData['ok'] = false;
				$this->returnData['message'] = $personal['message'];
				printJson($this->returnData);
				return;
			}
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

			$this->returnData['ok'] = true;
			$this->returnData['data'] = array("personal" => $personalData, "average" => $averageData);
		}
		printJson($this->returnData);
	}

	public function stuVitality(){
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
			//TO DO
		}else if($this->apimodel->getPlatform() == 'edx'){
			$match = array(
				"\$match" => array(
					"statement.timestamp" => array(
						"\$gte" => $this->apimodel->getFromDate(),
						"\$lt" => $this->apimodel->getToDate(),
					),
					"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/open_edx_tracking_log.courseid" => array("\$eq" => $this->apimodel->getCourseId()),
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
				$this->returnData['ok'] = false;
				$this->returnData['message'] = $output['message'];
				printJson($this->returnData);
				return;
			}
			$lastStu = "";
			$currentIndex = -1;
			$dataProcess = array();
			for($i = 0; $i < count($output['data']['edx']['result']); $i++){
				if($output['data']['edx']['result'][$i]['_id']['id'] != $lastStu){
					array_push($dataProcess, array(
						'<a href="javascript:void(0);" onclick="openTeaStuView(\'' . $output['data']['edx']['result'][$i]['_id']['id'] . '\', \'' . $output['data']['edx']['result'][$i]['_id']['name'] . '\')">' . $output['data']['edx']['result'][$i]['_id']['name'] . '</a>',0,0,0,0,0,0,0,0,
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
			$this->returnData['ok'] = true;
			$this->returnData['data'] = $dataProcess;
		}
		printJson($this->returnData);
	}
}