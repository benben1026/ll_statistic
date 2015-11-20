<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DataModel extends CI_Model{
	function __construct(){
		parent::__construct();
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
							"\$lt"=>new MongoDate(strtotime($to))
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
							"\$statement.timestamp", 0, 9,
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