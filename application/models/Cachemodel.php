<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DataModel extends CI_Model{

  private engagement_list;

  public function __construct(){
    parent::__construct();
    $this->load->database();
    $this->engagement_list = load_engagement_list();
  }

  ////// Statement Functions //////
  public function insert_statement_db($new_statement)
  {
    # insert a new statement type into database
  }

  public function delete_statement_db($del_statement)
  {
    # delete a statement type
  }

  public function refresh_statement_db()
  {
    # check the difference between config file and db and update db
  }

  ////// Engagement functions //////
  public function get_engagements($engagement_array,$from_date,$to_date)
  {
    # get engagements
  }

  ////// Daily job //////
  public function daily_statistic($value='')
  {
    # code...
  }
}
