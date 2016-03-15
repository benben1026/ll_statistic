<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DataModel extends CI_Model{
	function __construct(){
		parent::__construct();
	}


	function getData($domain, $auth, $pipeline){
		$options = array(
			'http' => array(
				'header' => "Content-type: application/json\r\n"
					. "Authorization: Basic " . $auth . "\r\n"
					. "X-Experience-API-Version: 1.0.1",
				'method' => 'GET',
			)
		);
		$context = stream_context_create($options);
		$result = file_get_contents($domain . $pipeline, false, $context);
		if ($result === FALSE) { 
			return false;
		}
		return json_decode($result);
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