<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class CacheModel extends CI_Model
{
    private $engagement_list;
    private $engage_table = 'dash_engage';  // table to store the daily records
    private $statement_table = 'dash_statement';    // table to store the statement types
    private $output = array(
        'ok' => FALSE,
        'message' => '',
        'data' => NULL,
    );  // output format

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->engagement_list = load_engagement_list();
    }

    public function check_tables()
    {
        $response = array(
            'ok' => FALSE,
            'message' => ''
        );
        if ($this->db->table_exists($this->statement_table) == FALSE) {
            $response['message'] = "table `{$this->statement_table}` does not exist.";
        } elseif ($this->db->table_exists($this->engage_table) == FALSE) {
            $response['message'] = "table `{$this->engage_table}` does not exist.";
        } else {
            $response['ok'] = TRUE;
        }
        return $response;
    }

    /**
    * Return the last update date in such format "2016-05-09".
    *
    * @param
    *
    * @return
    */
    public function getLastUpdateDate()
    {
        $table_check = $this->check_tables();
        if ($table_check['ok'] == FALSE) {
            return $table_check;
        }

        $this->db->trans_start();
        $this->db->order_by('statistic_date', 'DESC');
        $query = $this->db->get($this->engage_table);
        $row = $query->unbuffered_row();
        $this->db->trans_complete();

        if ($this->db->trans_status() == FALSE) {
            $this->output['ok'] = FALSE;
            $this->output['message'] = 'Database error.';
        } elseif ($row == NULL) {
            $this->output['ok'] = FALSE;
            $this->output['message'] = 'Table is empty.';
        } else {
            $this->output['ok'] = TRUE;
            $this->output['message'] = 'success';
            $this->output['data'] = $row->statistic_date;
        }
        return $this->output;
    }

    /**
    * Create statistic records dialy, cronjob used only.
    *
    * @param
    *
    * @return
    */
    public function createStatisticRecords($date = null)
    {
        $table_check = $this->check_tables();
        if ($table_check['ok'] == FALSE) {
            return $table_check;
        }

        $refresh_response = $this->refresh_statement_db();
        if (!$refresh_response['ok']) {
            $this->output['ok'] = false;
            $this->output['message'] = 'error when refresh statement database';

            return $this->output;
        } elseif ($refresh_response['needReload']) {
            $date = null;
        }
        $statistic_records = $this->daily_statistic($date);

        $this->db->trans_start();
        $insert_data = array();
        $update_data = array();
        foreach ($statistic_records as $record) {
            $insert_engagement = array(
                'course_id' => $record['course_id'],
                'platform' => $record['platform'],
                'statistic_date' => $record['date'],
            );

            //get the statement id
            $query = $this->db->get_where($this->statement_table, array('name' => $record['statement']));
            $statement_row = $query->row_array();
            $insert_engagement['statement_id'] = $statement_row['statement_id'];

            // check if exist already
            $query = $this->db->get_where($this->engage_table, $insert_engagement);
            if (count($query->result_array()) > 0) {
                if ($query->row_array()['statement_count'] != $record['count']) {
                    $insert_engagement['engagement_id'] = $query->row_array()['engagement_id'];
                    $insert_engagement['statement_count'] = $record['count'];
                    $update_data[] = $insert_engagement;
                }
            } else {
                $insert_engagement['statement_count'] = $record['count'];
                $insert_data[] = $insert_engagement;
            }
        }
        $this->db->trans_complete();

        // update exist engagements
        $this->db->trans_start();
        if (count($update_data) > 0) {
            $update_rows = $this->db->update_batch($this->engage_table, $update_data, 'engagement_id');
            $this->output['update_rows'] = $update_rows;
        }
        if (count($insert_data) > 0) {
            $this->db->insert_batch($this->engage_table, $insert_data);
            $this->output['insert_rows'] = count($insert_data);
        }
        $this->db->trans_complete();
        $this->output['ok'] = $this->db->trans_status();

        return $this->output;
    }

    /**
    * Return  grouped category statistic record with specific date range.
    *
    * @param daterange determined by start and end date, e.g.'2016-01-01'
    *
    * @return output data
    */
    public function readCacheStatisticRecord($platform, $course_id, $from_date, $to_date)
    {
        $table_check = $this->check_tables();
        if ($table_check['ok'] == FALSE) {
            return $table_check;
        }

        $this->db->trans_start();
        $this->db->select('category,statistic_date');
        $this->db->select_sum('statement_count');
        $this->db->from($this->engage_table);
        $this->db->join($this->statement_table, "{$this->statement_table}.statement_id = {$this->engage_table}.statement_id");
        $this->db->where(array('platform' => $platform, 'course_id' => $course_id));
        $this->db->where(array('statistic_date >=' => $from_date, 'statistic_date <=' => $to_date));
        $this->db->group_by(array('statistic_date', 'category'));
        // $sql = $this->db->get_compiled_select();
        $query = $this->db->get();
        $this->db->trans_complete();

        if ($this->db->trans_status() == FALSE) {
            $this->output['ok'] = FALSE;
            $this->output['message'] = 'something wrong when select in database.';
        } elseif (count($query->result_array()) == 0) {
            $this->output['ok'] = FALSE;
            $this->output['message'] = 'empty result';
        } else {
            // change query results to output format
            $tmp_array = array();
            foreach ($query->result() as $row) {
                $tmp_array[$row->statistic_date][$row->category] = $row->statement_count;
            }
            $output_data = array();
            foreach ($tmp_array as $date => $categories) {  // assign zero to categories not exist
                $one_date_stats = array('date' => $date);
                foreach ($this->engagement_list as $category => $statement_array) {
                    if (array_key_exists($category, $categories)) {
                        $one_date_stats[$category] = $categories[$category];
                    } else {
                        $one_date_stats[$category] = 0;
                    }
                }
                $output_data[] = $one_date_stats;
            }

            $this->output['ok'] = TRUE;
            $this->output['message'] = 'success';
            $this->output['platform'] = $platform;
            $this->output['course_id'] = $course_id;
            $this->output['data'] = $output_data;
        }
        return $this->output;
    }

    private function refresh_statement_db()
    {
        $response = array('ok' => false, 'needReload' => false);

        $this->db->trans_start();
        $query = $this->db->get($this->statement_table);
        $current_array = $query->result_array();
        if (count($current_array) < 1) { // table is empty
            $this->init_statement_db();
            $this->db->trans_complete();
            $response['ok'] = $this->db->trans_status();
            $response['needReload'] = true;

            return $response;
        }

        $insert_array = array();
        $update_array = array();
        $delete_array = array();
        foreach ($current_array as $current_statement) { // default delete=current
            $delete_array[] = array(
                'statement_id' => $current_statement['statement_id'],
                'name' => $current_statement['name'],
                'category' => $current_statement['category'],
            );
        }
        //fill out the arrays
        foreach ($this->engagement_list as $category => $statement_array) {
            foreach ($statement_array as $statement) {
                $statement_data = array(
                    'name' => $statement[0].' '.$statement[1],
                    'category' => $category,
                );
                $index = 0;
                foreach ($current_array as $current_statement) {
                    if ($current_statement['name'] == $statement_data['name']) {
                        $tmp_data = array(
                            'statement_id' => $current_statement['statement_id'],
                            'name' => $current_statement['name'],
                            'category' => $current_statement['category'],
                        );
                        $key = array_search($tmp_data, $delete_array);
                        array_splice($delete_array, $key, 1);
                        if ($current_statement['category'] != $statement_data['category']) {
                            $statement_data['statement_id'] = $current_statement['statement_id'];
                            $update_array[] = $statement_data;
                        }
                        break;
                    }
                    $index += 1;
                }
                if ($index == count($current_array)) { // new statement
                    $insert_array[] = $statement_data;
                }
            }
        }

        if (count($insert_array) > 0) {
            $response['needReload'] = true;
            $this->insert_statements($insert_array);
        }
        if (count($update_array) > 0) {
            // return $update_array;
            $this->update_statements($update_array);
        }
        if (count($delete_array) > 0) {
            $this->delete_statements($delete_array);
        }
        $this->db->trans_complete();
        $response['ok'] = $this->db->trans_status();

        return $response;
    }

    private function init_statement_db()
    {
        // initial the statement table, just be called during deployment
        foreach ($this->engagement_list as $category => $statement_array) {
            foreach ($statement_array as $statement) {
                $statement_data = array(
                    'name' => $statement[0].' '.$statement[1],
                    'category' => $category,
                );
                $this->db->insert($this->statement_table, $statement_data);
            }
        }
    }

    private function insert_statements($insert_array)
    {
        $this->db->insert_batch($this->statement_table, $insert_array);
    }

    private function update_statements($update_array)
    {
        $this->db->update_batch($this->statement_table, $update_array, 'statement_id');
    }

    private function delete_statements($delete_array)
    {
        foreach ($delete_array as $delete_statement) {
            // delete the related engaments
            $this->db->where('statement_id', $delete_statement['statement_id']);
            $this->db->delete($this->engage_table);
            // then delete the statement
            $this->db->delete($this->statement_table, $delete_statement);
        }
    }

    ////// Daily job //////
    //If date is not specified, then this function will return all of the statements.
    //If date is specified, then this function will only return the data in that perticular day.
    private function daily_statistic($date = null)
    {
        $this->load->model('datamodel');
        $this->load->model('courseinfomodel');
        $statistic_records = array();

        //Load course list making use of Courseinfo Model
        $courseList = $this->courseinfomodel->getData(array('moodle' => '/metric/course', 'edx' => '/metric/course'));
        if (array_key_exists('results', $courseList['data']['moodle'])) {
            foreach ($courseList['data']['moodle']['results'] as $value) {
                //Get all of the statements
                $output = $this->getDataFromDataModel('moodle', $value['id'], $date);
                //Rearrange the structure of the data.
                foreach ($output['data']['moodle']['result'] as $record) {
                    $statistic_records[] = array(
                        'course_id' => $value['id'],
                        'course_name' => $value['fullname'],
                        'platform' => 'moodle',
                        'statement' => $record['_id']['Engagement'],
                        'date' => $record['_id']['date'],
                        'count' => $record['count'],
                    );
                }
            }
        }
        if (array_key_exists('results', $courseList['data']['edx'])) {
            foreach ($courseList['data']['edx']['results'] as $value) {
                $output = $this->getDataFromDataModel('edx', $value['course_id'], $date);
                foreach ($output['data']['edx']['result'] as $record) {
                    $statistic_records[] = array(
                        'course_id' => $value['course_id'],
                        'course_name' => $value['course_name'],
                        'platform' => 'edx',
                        'statement' => $record['_id']['Engagement'],
                        'date' => $record['_id']['date'],
                        'count' => $record['count'],
                    );
                }
            }
        }

        return $statistic_records;
    }

    private function getDataFromDataModel($platform, $courseId, $date = null)
    {
        $key = getKey($platform);
        $match = array(
            '$match' => array(
                'statement.context.extensions.'.$key.'.courseid' => array('$eq' => $courseId),
                'statement.context.extensions.'.$key.'.rolename' => array('$eq' => 'student'),
                '$or' => $this->getOrArray(),
            ),
        );

        if ($date != null) {
            $match['$match']['statement.timestamp'] = array(
                '$gte' => $date.'T00:00',
                '$lte' => $date.'T23:59',
            );
        }
        
        $sortDate = array(
            '$sort' => array(
                'statement.timestamp' => -1,
            ),
        );
        
        $group = array(
        '$group' => array(
          '_id' => array(
            'Engagement' => array('$concat' => array('$statement.verb.display.en-us', ' ', '$statement.object.definition.name.en-us')),
            'date' => array('$substr' => array('$statement.timestamp', 0, 10)),
          ),
          'count' => array('$sum' => 1),
        ),
      );

    //   $project = array(
    //     '$project' => array(
    //       '_id' => 0,
    //       'statement.verb.display.en-us' => 1,
    //       'statement.object.definition.name.en-us' => 1,
    //       'statement.timestamp' => 1,
    //     ),
    //   );
      $pipeline[$platform] = array($match, $sortDate, $group);
      $output = $this->datamodel->getData($pipeline);
      return $output;
  }

    private function getOrArray()
    {
        $returnArray = array();
        foreach ($this->engagement_list as $category => $verbStateArray) {
            foreach ($verbStateArray as $verbState) {
                $verb = $verbState[0];
                $name = $verbState[1];
                $returnArray[] = array(
                    '$and' => array(
                        array('statement.verb.display.en-us' => array('$eq' => $verb)),
                        array('statement.object.definition.name.en-us' => array('$eq' => $name)),
                    ),
                );
            }
        }

        return $returnArray;
    }
}
