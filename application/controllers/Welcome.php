<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$match = array(
			"\$match"=>array(
				"statement.actor.name"=>"admin"
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
		$op = array(
			$match, $group,
		);
		// $op = array(
		// 	"\$match"=>array(
		// 		"statement.actor.name"=>"admin"
		// 	),
		// 	"\$group"=>array(
		// 		"_id"=>array(
		// 			"date"=>array(
		// 				"\$substr"=>array(
		// 					"[ \"\$statement.timestamp\", 0, 9]",
		// 				),
		// 			),
		// 		),
		// 		"sum"=>array(
		// 			"\$sum"=>1,
		// 		),
		// 	),
		// );
		print json_encode($this->mongo_db->aggregate("statements", $op));
		//$this->mongo_db->where_ne('title', 'Testing');
		//$this->mongo_db->where('statement.actor.name', 'admin');
		//$this->mongo_db->limit(10);

		//print json_encode($this->mongo_db->get("statements"));
		//$this->load->view('welcome_message');
	}

	public function mysql(){
		//$this->db->query("INSERT INTO unit(pattern, name) VALUES ('1-1', 'CUHK')");
		$query = $this->db->query('SELECT * FROM `unit`');
		$row = $query->result_array();
		print json_encode($row[0]['name']);
	}
}
