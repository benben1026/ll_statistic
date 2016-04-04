<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Access extends Acc_Controller{
	private $error_info;
	private $global_right;
	private $spec_right;

	public function index(){
		if($this->session->userdata('samlUserData')['login'][0] != null){
			redirect('/access/overview');
			return;
		}
		$data['title'] = "Welcome";
		$data['firstname'] = $this->session->userdata('samlUserData')['firstname'][0];
		$this->load->view('template/header', $data);
		$this->load->view('landing');
		$this->load->view('template/footer');
		// print json_encode($this->session->all_userdata());
		// print "<br/>";
		// print "email:" . $this->session->userdata('samlUserData')['login'][0] . "<br/>";
		// print "keepid:" . $this->session->userdata('samlUserData')['keepid'][0] . "<br/>";
		// print "full name:" . $this->session->userdata('samlUserData')['fullname'][0] . "<br/>";
	}

	public function overview(){
		$data['title'] = "Overview";
		$data['firstname'] = $this->session->userdata('samlUserData')['firstname'][0];
		$this->load->view('template/header', $data);
		$this->load->view('overview');
		//$this->load->view('overview');
		// $this->load->view('charts/engagement');
		// $this->load->view('charts/courseOverview');
		$this->load->view('template/footer');
	}

	public function testLogin(){
		if($this->session->userdata('samlUserData')['login'][0] != null){
			print 'You are login with ' . $this->session->userdata('samlUserData')['login'][0];
		}else{
			print 'You are not login';
		}
	}

	/********************** VIEW ************************/

	public function home(){
		if($this->user['id'] == null){
			$t['target'] = base_url() . "index.php/login";
			$this->load->view('jump', $t);
			return;
		}
		$data['title'] = 'Keep Home';
		$data['username'] = $this->user['name'];
		$this->load->view('template/header', $data);
		$this->load->view('home');
		$this->load->view('template/footer');
	}

	public function course(){
		if($this->user['id'] == null){
			$t['target'] = base_url() . "index.php/login";
			$this->load->view('jump', $t);
			return;
		}
		$data['title'] = 'Keep Home';
		$data['username'] = $this->user['name'];
		$this->load->view('template/header', $data);
		$this->load->view('course');
		$this->load->view('template/footer');
	}

	public function admin(){
		if($this->user['id'] == null){
			$t['target'] = base_url() . "index.php/login";
			$this->load->view('jump', $t);
			return;
		}
		$data['title'] = 'Admin';
		$data['username'] = $this->user['name'];
		$this->load->view('template/header', $data);
		$this->load->view('admin');
		$this->load->view('template/footer');
	}

	public function logout(){
		$this->session->sess_destroy();
		$t['target'] = base_url() . "index.php/login";
		$this->load->view('jump', $t);
	}
	/********************** VIEW END ***********************/


	/*********************************API**********************************/
	public function getLecMat($from_y, $from_m, $from_d, $to_y, $to_m, $to_d){
		$name = $this->user['name'];
		//print $name;
		if(!$name){
			return;
		}
		$from = $this->constructDate($from_y, $from_m, $from_d);
		$to = $this->constructDate($to_y, $to_m, $to_d);
		if(!$from || !$to){
			print 'Invalid Date';
			return;
		}
		$reg = "/(mod_book)|(mod_resource)|(mod_lesson)|(mod_url)|(mod_resource)|(mod_wiki)|(local_youtube_events)/i";
		$this->load->model("datamodel");

		$output = $this->datamodel->getDataAccToEventname($name, $reg, $from, $to);
		$output['label'] = "Lecture Material";
		print json_encode($output);
	}

	public function getAssessment($from_y, $from_m, $from_d, $to_y, $to_m, $to_d){
		$name = $this->user['name'];
		if(!$name){
			return;
		}
		$from = $this->constructDate($from_y, $from_m, $from_d);
		$to = $this->constructDate($to_y, $to_m, $to_d);
		if(!$from || !$to){
			print 'Invalid Date';
			return;
		}
		//print "from = " . $from . " to = " . $to . "<br/>";
		//print "Mongo Date = " . new MongoDate(strtotime($from)) . "<br/>";
		//print "Mongo Date2 = " . new MongoDate(strtotime("2015-10-26 00:00:00")) . "<br/>";
		//print "Mongo Date3 = " . new MongoDate(strtotime("2015-10-26T00:00:00.000Z")) . "<br/>";
		//print "Mongo Date3 = " . new MongoDate(strtotime("2015-10-03T00:00:00.000Z")) . "<br/>";
		$reg = "/(mod_quiz)|(mod_assign)/i";
		$this->load->model("datamodel");
		$output = $this->datamodel->getDataAccToEventname($name, $reg, $from, $to);
		$output['label'] = "Asscssment";
		print json_encode($output);
	}

	public function getLogin($from_y, $from_m, $from_d, $to_y, $to_m, $to_d){
		$name = $this->user['name'];
		if(!$name){
			return;
		}
		$from = $this->constructDate($from_y, $from_m, $from_d);
		$to = $this->constructDate($to_y, $to_m, $to_d);
		if(!$from || !$to){
			print 'Invalid Date';
			return;
		}
		$reg = "/user_loggedin/i";
		$this->load->model("datamodel");
		$output = $this->datamodel->getDataAccToEventname($name, $reg, $from, $to);
		$output['label'] = "Login";
		print json_encode($output);
	}

	public function getForum($from_y, $from_m, $from_d, $to_y, $to_m, $to_d){
		$name = $this->user['name'];
		if(!$name){
			return;
		}
		$from = $this->constructDate($from_y, $from_m, $from_d);
		$to = $this->constructDate($to_y, $to_m, $to_d);
		if(!$from || !$to){
			print 'Invalid Date';
			return;
		}
		$reg = "/mod_forum/i";
		$this->load->model("datamodel");
		$output = $this->datamodel->getDataAccToEventname($name, $reg, $from, $to);
		$output['label'] = "Forum";
		print json_encode($output);
	}
	
	public function getPersonalStat($from_y, $from_m, $from_d, $to_y, $to_m, $to_d){
		$output['data'] = array();
		$name = $this->user['name'];
		if(!$name){
			return;
		}
		$from = $this->constructDate($from_y, $from_m, $from_d);
		$to = $this->constructDate($to_y, $to_m, $to_d);
		if(!$from || !$to){
			print 'Invalid Date';
			return;
		}
		$this->load->model("datamodel");
		$forum = $this->datamodel->getDataAccToEventname($name, '/mod_forum/i', $from, $to);
		$login = $this->datamodel->getDataAccToEventname($name, '/user_loggedin/i', $from, $to);
		$assessment = $this->datamodel->getDataAccToEventname($name, '/(mod_quiz)|(mod_assign)/i', $from, $to);
		$lecmat = $this->datamodel->getDataAccToEventname($name, '/(mod_book)|(mod_resource)|(mod_lesson)|(mod_url)|(mod_resource)|(mod_wiki)|(local_youtube_events)/i', $from, $to);
		if($forum['ok'] == 1){
			$output['data']['Forum'] = $forum['result'];
			//array_push($output['data'], array('Forum'=>$forum['result']));
		}
		if($login['ok'] == 1){
			$output['data']['Login'] = $login['result'];
			//array_push($output['data'], array('Login'=>$login['result']));
		}
		if($assessment['ok'] == 1){
			$output['data']['Assessment'] = $assessment['result'];
			//array_push($output['data'], array('Assessment'=>$assessment['result']));
		}
		if($lecmat['ok'] == 1){
			$output['data']['Lecture Material'] = $lecmat['result'];
			//array_push($output['data'], array('Lecture Material'=>$lecmat['result']));
		}
		$output['ok'] = 1;
		print json_encode($output);
	}

	public function getCourseInfo($courseId, $from_y, $from_m, $from_d, $to_y, $to_m, $to_d){
		if($this->user['id'] == null){
			$output['ok'] = 0;
			$output['error'] = "Please Login";
			print json_encode($output);
			return;
		}
		$output['data'] = array();
		$from = $this->constructDate($from_y, $from_m, $from_d);
		$to = $this->constructDate($to_y, $to_m, $to_d);
		if(!$from || !$to){
			$output['ok'] = 0;
			$output['error'] = "Invalid Date";
			print json_encode($output);
			return;
		}
		$res = $this->getPriv($courseId, 'read');
		$global_priv = false;
		if($res['scope'] == 'partial'){
			$global_priv = false;
		}else if($res['scope'] == 'all'){
			$global_priv = true;
		}
		$this->load->model("datamodel");
		if($global_priv || $res['data']['forum']){
			$forum = $this->datamodel->getInfoAccToCourseId($courseId, '/mod_forum/i', $from, $to);
			if($forum['ok'] == 1){
				$output['data']['Forum'] = $forum['result'];
			}
		}

		if($global_priv || $res['data']['login']){
			$login = $this->datamodel->getInfoAccToCourseId($courseId, '/user_loggedin/i', $from, $to);
			if($login['ok'] == 1){
				$output['data']['Login'] = $login['result'];
			}
		}

		if($global_priv || $res['data']['assessment']){
			$assessment = $this->datamodel->getInfoAccToCourseId($courseId, '/(mod_quiz)|(mod_assign)/i', $from, $to);
			if($assessment['ok'] == 1){
				$output['data']['Assessment'] = $assessment['result'];
			}
		}

		if($global_priv || $res['data']['file']){
			$lecmat = $this->datamodel->getInfoAccToCourseId($courseId, '/(mod_book)|(mod_resource)|(mod_lesson)|(mod_url)|(mod_resource)|(mod_wiki)|(local_youtube_events)/i', $from, $to);
			if($lecmat['ok'] == 1){
				$output['data']['Lecture Material'] = $lecmat['result'];
			}
		}
		if(empty($output['data'])){
			$output['ok'] = 0;
			$output['error'] = "You don't have sufficient privilege to access this data";
		}else{
			$output['ok'] = 1;
		}
		print json_encode($output);
	}

	public function getPriv($course_id, $action){
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
		return $output;

	}

	private function checkRight($course_id, $action){
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

	private function constructDate($y, $m, $d){
		$date = $y . '-' . $m . '-' . $d;
		$d = DateTime::createFromFormat('Y-m-d', $date);
		//$d = DateTime::createFromFormat('Y-m-d', $date);
		//print $date . '<br/>';
		//print $d->format('Y-m-d') . '<br/>';
		//print $d->format('Y-m-d H:i:s') . '<br/>';
		//print strtotime("now") . "<br/>";
		//print strtotime("2015-11-27 12:25:00") . "<br/>";
    	// if(!($d && $d->format('Y-m-d') == $date)){
    	// 	return false;
    	// }
		//return $d->format('Y-m-d') . 'T00:00:00.000Z';
		return $d->format('Y-m-d H:i:s');
	}

	/***************************ADMIN***************************/
	//login is required
	public function getRoleList(){
		if($this->user['id'] == null){
			$t['target'] = base_url() . "index.php/login";
			$this->load->view('jump', $t);
			return;
		}
		$this->load->model("accessmodel");
		$roles = $this->accessmodel->getRoles($this->user['id']);
		print json_encode($roles);
	}

	//no privilege check, only login is required
	public function getAllUnits($role_id){
		if($this->user['id'] == null){
			$t['target'] = base_url() . "index.php/login";
			$this->load->view('jump', $t);
			return;
		}
		$this->load->model("accessmodel");
		$units = $this->accessmodel->getAllUnits($this->user['id'], $role_id);
		print json_encode($units);
	}

	//will check if this user has admin privilege in this unit
	public function getAllUsers($role_id, $unit_id){
		if($this->user['id'] == null){
			$t['target'] = base_url() . "index.php/login";
			$this->load->view('jump', $t);
			return;
		}
		$this->load->model("accessmodel");
		print json_encode($this->accessmodel->getAllUsers($this->user['id'], $role_id, $unit_id));	

	}

	public function updateGlobalPrivilege(){
		//post data should consist of role_id, target_role_id, privilege
		$postData = $this->input->post();
		if(!isset($postData['role_id'], $postData['target_role_id'], $postData['privilege'])){
			print json_encode(array('result' => false, 'error' => 'Invalid Parameters'));
			return;
		}
		//print '1 ';
		if($this->user['id'] == null){
			$t['target'] = base_url() . "index.php/login";
			$this->load->view('jump', $t);
			return;
		}
		//print '2 ';
		$this->load->model("accessmodel");
		$res = $this->accessmodel->updateGlobalPriv($this->user['id'], $postData['role_id'], $postData['target_role_id'], json_decode($postData['privilege']));
		print json_encode($res);
	}
	/*************************END ADMIN*************************/
}