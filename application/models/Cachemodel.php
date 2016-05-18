<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CacheModel extends CI_Model{

  private $engagement_list;

  public function __construct(){
    parent::__construct();
    $this->load->database();
    $this->engagement_list = load_engagement_list();
  }

  ////// Statement Functions //////
  public function init_statement_db()
  {
    // initial the statement table, just be called during deployment
    $table_name = "dash_statement";
    foreach ($this->engagement_list as $category => $statement_array) {
      foreach ($statement_array as $statement) {
        $statement_data = array(
          'name' => $statement[0]." ".$statement[1],
          'category' => $category
        );
        $this->db->insert($table_name,$statement_data);
      }
    }
    $this->output['ok'] = true;
    return $this->output;
  }

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
  //If date is not specified, then this function will return all of the statements.
  //If date is specified, then this function will only return the data in that perticular day.
  public function daily_statistic($date = null){
    $this->load->model('datamodel');
    $this->load->model('courseinfomodel');
    $statistic_records = array();

    //Load course list making use of Courseinfo Model
    $courseList = $this->courseinfomodel->getData(array('moodle' => '/metric/course', 'edx' => '/metric/course'));
    if(array_key_exists('results', $courseList['data']['moodle'])){
      foreach($courseList['data']['moodle']['results'] as $value){
        //Get all of the statements
        $output = $this->getDataFromDataModel('moodle', $value['id'], $date);
        //Rearrange the structure of the data.
        foreach($output['data']['moodle']['result'] as $record){
          $statistic_records[] = array(
            "course_id" => $value['id'],
            "course_name" => $value['fullname'],
            "platform" => "moodle",
            "statement" => $record['_id']['Engagement'],
            "date" => $record['_id']['date'],
            "count" => $record['count'],
          );
        }
      }
    }
    if(array_key_exists('results', $courseList['data']['edx'])){
      foreach($courseList['data']['edx']['results'] as $value){
        $output = $this->getDataFromDataModel('edx', $value['course_id'], $date);
        foreach($output['data']['edx']['result'] as $record){
          $statistic_records[] = array(
            "course_id" => $value['course_id'],
            "course_name" => $value['course_name'],
            "platform" => "edx",
            "statement" => $record['_id']['Engagement'],
            "date" => $record['_id']['date'],
            "count" => $record['count'],
          );
        }
      }
    }
    return $statistic_records;

  }

  function getDataFromDataModel($platform, $courseId, $date = null){
    $key = getKey($platform);
    $match = array(
      "\$match" => array(
        "statement.context.extensions.".$key.".courseid" => array("\$eq" => $courseId),
        "statement.context.extensions.".$key.".rolename" => array("\$eq" => "student"),
        "\$or" => $this->getOrArray()
      ),
    );
    if($date != null){
      $match['$match']['statement.timestamp'] = array(
        "\$gte" => $date . "T00:00",
        "\$lte" => $date . "T23:59",
      );
    }
    $sortDate = array(
      "\$sort" => array(
        "statement.timestamp" => -1,
      )
    );
    $group = array(
      "\$group" => array(
        "_id" => array(
          "Engagement" => array("\$concat" => array("\$statement.verb.display.en-us", " ", "\$statement.object.definition.name.en-us")),
          "date" => array("\$substr"=>array("\$statement.timestamp", 0, 10,),),
        ),
        "count" => array("\$sum" => 1)
      )
    );
    $project = array(
      "\$project" => array(
        "_id" => 0,
        "statement.verb.display.en-us" => 1,
        "statement.object.definition.name.en-us" => 1,
        "statement.timestamp" => 1,
      )
    );
    $pipeline[$platform] = array($match, $sortDate, $group);
    $output = $this->datamodel->getData($pipeline);
    return $output;
  }

  function getOrArray() {
    $returnArray = array();

    foreach ($this->engagement_list as $category => $verbStateArray) {
      foreach($verbStateArray as $verbState) {
        $verb = $verbState[0];
        $name = $verbState[1];

        $returnArray[] = array(
          "\$and" => array(
            array("statement.verb.display.en-us" => array("\$eq" => $verb)),
            array("statement.object.definition.name.en-us" => array("\$eq" => $name)),
          )
        );
      }
    }

    return $returnArray;
  }
}
