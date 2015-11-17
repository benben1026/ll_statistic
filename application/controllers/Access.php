<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Access extends Acc_Controller{
	private $error_info;
	private $global_right;
	private $spec_right;

	public function index(){
		print json_encode($this->user['id']);
	}

	public function home(){
		$data['title'] = 'Keep Home';
		$this->load->view('template/header', $data);
		$this->load->view('home');
		//$this->load->view('template/footer');
	}

	public function getPersonalStat($name){
		if($name == "_self"){
			$name = $this->user['name'];
		}
		$match = array(
			"\$match"=>array(
				"statement.actor.name"=>$name
			),
		);
		$groupByVerb = array(
			"\$group"=>array(
				"_id"=>array(
					"verb"=>"\$statement.verb.id",
					"date"=>array(
						"\$substr"=>array(
							"\$statement.timestamp", 0, 9,
						),
					),
				),
				"dateCount"=>array(
					"\$sum"=>1,
				),
			),
		);
		$sort2 = array(
			"\$sort"=>array("statement.timestamp"=>-1),
		);
		$group2 = array(
			"\$group"=>array(
				"_id"=>"\$_id.verb",
				"date"=>array(
					"\$push"=>array(
						"date"=>"\$_id.date",
						"count"=>"\$dateCount",
					),
				),
				"verbCount"=>array("\$sum"=>"\$dateCount"),
			),
		);
		$sort = array(
			"\$sort"=>array("verbCount" => -1),
		);
		$groupByDate = array(
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
		$limit = array(
			"\$limit"=>5,
		);
		$op = array($match, $sort2, $groupByVerb, $group2, $sort);
		//$op = array($match, $sort2, $limit);
		$result = $this->mongo_db->aggregate("statements", $op);
		print json_encode($result);
		// if($result['ok'] == 1)
		// 	print json_encode($result['result']);
		// else
		// 	print '[]';
	}

	public function getData($course_id, $action, $resource){
		$output;
		$res = $this->checkRight($course_id, $action);
		if($res && $this->global_right == 1){
			$outut['status'] = true;
			$output['scope'] = 'all';
		}else if($res){
			$outut['status'] = true;
			$output['scope'] = 'partial';
			$output['data'] = $this->spec_right;
		}else{
			$output['status'] = false;
			$output['error_info'] = $this->error_info;
		}
		print json_encode($output);

	}

	private function checkRight($course_id, $action){
		if($this->user['id'] == null){
			$this->error_info = "Please Login";
			return false;
		}
		print $this->user['id'];
		$action = trim(strtolower($action));
		if($action != "read" && $action != "write" && $action != "admin"){
			$this->error_info = "Invalid Parameter";
			return false;
		}

		$this->load->model("accessmodel");
		$this->global_right = $this->accessmodel->checkGlobalRight($this->user['id'], $course_id, $action);
		if($this->global_right == 1)
			return true;
		$this->spec_right = $this->accessmodel->checkSpecRights($this->user['id'], $course_id, $action);
			return true;
	}

	// public function checkRight(){
	// 	if($this->user['id'] == null){
	// 		print 'please login';
	// 		return;
	// 	}
	// 	$postData = $this->input->post();
	// 	$this->load->model("accessmodel");
	// 	if(!isset($postData['accesstoken'])){
	// 		print 'Invalid Parameter';
	// 		return;
	// 	}
	// 	$res = $this->accessmodel->checkRights($postData['accesstoken'], $this->user['id']);
	// 	//print json_encode($res);
	// 	if($res){
	// 		print 'OK';
	// 	}else{
	// 		print 'Access Denied';
	// 	}
	// } 
		// }
}