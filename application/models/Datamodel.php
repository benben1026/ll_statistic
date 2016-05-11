<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DataModel extends CI_Model{
	private $pipeline_url = "http://lrs.keep.edu.hk/public/api/v1/statements/aggregate?pipeline=";
	private $auth = array(
		// Production
		// "moodle" => "ZGVmYTRiMjM5ODFhY2Q5YmRkNGU0OGM4N2I3NGU1NDhmYmNiNjEwYzplMzAwOWJkM2RiNTZiMTNmMjg5ZGQ2YTM2M2Y4MWQ3OGY4OTdkMzVm",
		// "edx" => "Y2M5ZTQ0YmUwMmE2NTg4MTRmNDJlMmZmNDI1ODBkYjE3ZGVmMjMyMDo3ZGQyMzk5MjhmNmZkMzIyNjFjODgzODM1MmI0YjA4ZDQ5NzQ1Mjk3",

		// Staging
		"moodle" => "MGU3NmEyZGQxZjc4NmI5NzEzMjVkMmQ4YWUzY2FmMTI1NjczZDgyNTpjMTUxZDg2NWNlNGY1NjFiNWIyZDFlNTI5MmM3OTgyZDhiMDc3ZGVj",
		"edx" => "ZmNlOWY0MWMwMWJmOTllOTg4YjFkNGZlYzhiZjlkNjYyZmFjODIzOTo1MzU5NzUxNzdlNTlhYWEzZmQ0MGIyNWFmMzI0YWZhNjAxMzdiNzcy",
	);
	private $output = array(
		"ok" => false,
		"message" => "",
		"data" => array(),
	);

	function __construct(){
		parent::__construct();
	}


	// function getData($domain, $auth, $pipeline){
	// 	$options = array(
	// 		'http' => array(
	// 			'header' => "Content-type: application/json\r\n"
	// 				. "Authorization: Basic " . $auth . "\r\n"
	// 				. "X-Experience-API-Version: 1.0.1",
	// 			'method' => 'GET',
	// 		)
	// 	);
	// 	$context = stream_context_create($options);
	// 	$result = file_get_contents($domain . $pipeline, false, $context);
	// 	if ($result === FALSE) {
	// 		return false;
	// 	}
	// 	return json_decode($result);
	// }

	//pipeline should be in the following format,
	//array("moodle" => array(), "edx" => array())
	function getData($pipeline_array){
		if(empty($pipeline_array)){
			$this->output['message'] = "Invalid Pipeline";
			return $this->output;
		}
		$this->output['ok'] = true;
		foreach($pipeline_array as $lrs => $pipeline_raw){
			if(!isset($this->auth[$lrs])){
				$this->output['data'][$lrs] = null;
				continue;
			}
			$pipeline = urlencode(json_encode($pipeline_raw, JSON_UNESCAPED_SLASHES));
			$this->output['data'][$lrs] = $this->sendRequest($this->auth[$lrs], $pipeline);
		}
		return $this->output;


		// $pipeline = urlencode(json_encode($pipeline_raw, JSON_UNESCAPED_SLASHES));
		// $this->output['ok'] = true;
		// if($platform == "all"){
		// 	foreach($this->auth as $lrs => $key){
		// 		$result = $this->sendRequest($key, $pipeline);
		// 		if(!$result){
		// 			$this->output['data'][$lrs] = null;
		// 		}else{
		// 			$this->output['data'][$lrs] = $result;
		// 		}
		// 	}
		// }else if(!isset($this->auth[$platform])){
		// 	$this->output['data'][$platform] = null;
		// }else{
		// 	$result = $this->sendRequest($this->auth[$platform], $pipeline);
		// 	if(!$result){
		// 		$this->output['data'][$platform] = null;
		// 	}else{
		// 		$this->output['data'][$platform] = $result;
		// 	}
		// }
		// return $this->output;
	}

	function sendRequest($key, $pipeline){
		// Staging Proxy
		$proxy = "192.168.1.149:8000";

		$header = array();
		$header[] = 'Authorization: Basic ' . $key;
		$header[] = 'X-Experience-API-Version: 1.0.1';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		// Staging Proxy
		curl_setopt($ch, CURLOPT_PROXY, $proxy);
		curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
		curl_setopt($ch, CURLOPT_URL, $this->pipeline_url . $pipeline);
		$result = curl_exec($ch);
		if(curl_error($ch)){
			curl_close($ch);
			return false;
		}else{
			curl_close($ch);
			return json_decode($result, TRUE);
		}
	}

	function getInternalData($domain, $auth, $pipeline){
		$proxy = "192.168.1.149:8000";

		$header = array();
		$header[] = 'Authorization: Basic ' . $auth;
		$header[] = 'X-Experience-API-Version: 1.0.1';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_PROXY, $proxy);
		curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
		curl_setopt($ch, CURLOPT_URL, $domain . $pipeline);
		$result = curl_exec($ch);
		curl_close($ch);
		return json_decode($result, TRUE);
	}
	// function getDataAccToEventname($name, $reg){
	// 	$output = array();
	// 	$mongo = $this->mongo_db->getMongoInstance();
	// 	$cursor = $mongo->statements->aggregate( '{"$match":  { $and: [ { "statement.actor.name": "stud00" }, { "statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.eventname": { $regex: /user_loggedin/i } } ] }}, {"$group":{"_id": { date: { $substr: [ "$statement.timestamp", 0, 9] } }, "sum":{"$sum":1}}}'  ).toArray();
	// 	$res = $cursor->getNext();
	// 	while($res){
	// 		$output.push($res);
	// 		$res = $cursor->getNext();
	// 	}
	// 	return $output;
	// }

	// not used for now
	function getDataAccToEventname($name, $reg, $from, $to){
		$match = array(
			"\$match"=>array(
				"\$and"=>array(
					array(
						"statement.actor.name"=>$name,
					),
					array(
						"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.eventname"=>array(
							"\$regex"=>new MongoRegex($reg)
						)
					),
					array(
						"timestamp"=>array(
							"\$gte"=>new MongoDate(strtotime($from)),
							"\$lt"=>new MongoDate(strtotime($to)),
							//"\$gte"=>new MongoDate(strtotime("2015-10-24 00:00:00")),
							//"\$lt"=>new MongoDate(strtotime("2015-11-27 00:00:00")),
							//"\$gte"=>new MongoDate(strtotime("10/24/15")),
							//"\$lt"=>new MongoDate(strtotime("11/27/15")),
						),
					),
					// array(
					// 	"\$lt"=>
					// ),
				)
			),
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
				"sum"=>array(
					"\$sum"=>1,
				),
			),
		);
		$op = array($match, $group);
		return $this->mongo_db->aggregate("statements", $op);
	}

	// not used for now
	function getInfoAccToCourseId($courseId, $reg, $from, $to){
		$match = array(
			"\$match"=>array(
				"\$and"=>array(
					array(
						"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.courseid"=>$courseId
					),
					array(
						"statement.context.extensions.http://lrs&46;learninglocker&46;net/define/extensions/moodle_logstore_standard_log.eventname"=>array(
							"\$regex"=>new MongoRegex($reg)
						)
					),
					array(
						"timestamp"=>array(
							"\$gte"=>new MongoDate(strtotime($from)),
							"\$lt"=>new MongoDate(strtotime($to))
						),
					),
				),
			),
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
				"sum"=>array(
					"\$sum"=>1,
				),
			),
		);
		$op = array($match, $group);
		return $this->mongo_db->aggregate("statements", $op);
	}
}
