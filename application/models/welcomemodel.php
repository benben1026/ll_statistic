<?php

class WelcomeModel extends CI_Model{
	function fetchData(){
		return $this->mongo_db->select();
	}
}