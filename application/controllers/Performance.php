<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Performance extends CI_Controller
{
    private $returnData = array(
        'ok' => false,
        'message' => 'default',
        'data' => null,
        // "debug" => "debug"
    );
    private $engagementList;

    public function __contruct()
    {
        parent::__contruct();
    }

    public function stuPerformance()
    {
        $this->engagementList = load_engagement_list();
        $this->load->model('apimodel');
        $this->load->model('datamodel');

        // auth checking
        if (!$this->apimodel->getAccessGranted()) {
            $this->returnData['ok'] = false;
            $this->returnData['message'] = $this->apimodel->getMessage();
            printJson($this->returnData);

            return;
        }

        // date range
        $this->apimodel->setFromDate($this->input->get('from'));
        $this->apimodel->setToDate($this->input->get('to'));

        //!important: please set platform before courseId
        $this->apimodel->setPlatform($this->input->get('platform'));
        $this->apimodel->setCourseId($this->input->get('courseId'));
        // This is loading student views for teacher.
        // Teacher will fetch data as a student role.
        if ($this->input->get('keepId') != null) {
            $this->apimodel->setKeepId($this->input->get('keepId'));
        }
        if (!$this->apimodel->getValidParameter()) {
            $this->returnData['ok'] = false;
            $this->returnData['message'] = $this->apimodel->getMessage();
            printJson($this->returnData);

            return;
        }
        $this->getDataFromPlatform($this->apimodel->getPlatform());
        printJson($this->returnData);
    }

    private function getDataFromPlatform($platform)
    {
        $key = getKey($platform);
        //get total number of students
        $match = array(
            '$match' => array(
                'statement.context.extensions.'.$key.'.courseid' => array('$eq' => $this->apimodel->getCourseId()),
                'statement.context.extensions.'.$key.'.rolename' => array('$eq' => 'student'),
                'statement.verb.id' => array('$eq' => 'http://www.tincanapi.co.uk/verbs/enrolled_onto_learning_plan'),
            ),
        );

        $group = array(
            '$group' => array(
                '_id' => array(
                    'verb' => '$statement.verb.display.en-us',
                ),
                'count' => array('$sum' => 1),
            ),
        );
        $pipeline[$platform] = array($match, $group);
        $output = $this->datamodel->getData($pipeline);

        if (!$output['ok']) {
            $this->returnData['ok'] = false;
            $this->returnData['message'] = $output['message'];
            printJson($this->returnData);

            return;
        }
        $totalNumOfStudent = 0;
//$totalNumOfStudent = 10;
        if (isset($output['data'][$platform]['result'][0])) {
            $totalNumOfStudent = $output['ok'] && $output['data'][$platform]['ok'] ? $output['data'][$platform]['result'][0]['count'] : 0;
        }
        if ($totalNumOfStudent == 0) {
            $this->returnData['ok'] = false;
            $this->returnData['message'] = 'There is no student in this course';
            printJson($this->returnData);

            return;
        }

        //get the average number of each action
        $match = array(
            '$match' => array(
                'statement.context.extensions.'.$key.'.courseid' => array('$eq' => $this->apimodel->getCourseId()),
                'statement.context.extensions.'.$key.'.rolename' => array('$eq' => 'student'),
                '$or' => $this->getOrArray(),
            ),
        );
        $group = array(
            '$group' => array(
                '_id' => array(
                    'verb' => '$statement.verb.display.en-us',
                    'object' => '$statement.object.definition.name.en-us',
                ),
                'count' => array('$sum' => 1),
            ),
        );
        $sort = array(
            '$sort' => array('_id.verb' => 1, '_id.object' => 1),
        );
        $pipeline[$platform] = array($match, $group, $sort);
        $total = $this->datamodel->getData($pipeline);
        if (!$total['ok']) {
            $this->returnData['ok'] = false;
            $this->returnData['message'] = $total['message'];
            printJson($this->returnData);
            return;
        }

        //get personal actions
        $match = array(
            '$match' => array(
                'statement.actor.name' => array('$eq' => $this->apimodel->getKeepId()),
                'statement.context.extensions.'.$key.'.courseid' => array('$eq' => $this->apimodel->getCourseId()),
                'statement.context.extensions.'.$key.'.rolename' => array('$eq' => 'student'),
                '$or' => $this->getOrArray(),
            ),
        );
        $group = array(
            '$group' => array(
                '_id' => array(
                    'verb' => '$statement.verb.display.en-us',
                    'object' => '$statement.object.definition.name.en-us',
                ),
                'count' => array('$sum' => 1),
            ),
        );

        $pipeline[$platform] = array($match, $group, $sort);
        $personal = $this->datamodel->getData($pipeline);
        if (!$personal['ok']) {
            $this->returnData['ok'] = false;
            $this->returnData['message'] = $personal['message'];
            printJson($this->returnData);
            return;
        }
        $averageDataTemp = get_engagement_statement();
        $personalDataTemp = get_engagement_statement();
        foreach($total['data'][$platform]['result'] as $statement){
            foreach($averageDataTemp as $key => $count){
                if($key == $statement['_id']['verb'] . " " . $statement['_id']['object']){
                    $averageDataTemp[$key]['count'] = $statement['count'];
                    break;
                }
            }
        }
        foreach($personal['data'][$platform]['result'] as $statement){
            foreach($personalDataTemp as $key => $count){
                if($key == $statement['_id']['verb'] . " " . $statement['_id']['object']){
                    $personalDataTemp[$key]['count'] = $statement['count'];
                    break;
                }
            }
        }
        $averageData = get_engagement_category();
        $personalData = get_engagement_category();
        foreach($personalDataTemp as $record){
            $personalData[$record['category']] += $record['count'];
        }
        foreach($averageDataTemp as $record){
            $averageData[$record['category']] += $record['count'];
        }
        foreach($averageData as $key => $count){
            $averageData[$key] = number_format($count / $totalNumOfStudent, 3);
        }
        $averageData = array_map('floatval', $averageData);

        $indicator = array();
        $outputAverageData = array();
        $outputPersonalData = array();
        foreach ($averageData as $key => $value) {
            $max = (float)(number_format(max($averageData[$key], $personalData[$key]) * 1.2, 4));
            array_push($indicator, array('name' => $key, 'max' => $max == 0 ? 1 : $max));
            array_push($outputAverageData, $averageData[$key]);
            array_push($outputPersonalData, $personalData[$key]);
        }

        $this->returnData['ok'] = true;
        $this->returnData['data'] = array('indicator' => $indicator, 'personal' => $outputPersonalData, 'average' => $outputAverageData);
    }

    private function getOrArray(){
        $returnArray = array();
        foreach ($this->engagementList as $category => $verbStateArray) {
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

    public function stuVitality()
    {
        $this->load->model('apimodel');
        $this->load->model('datamodel');

        if (!$this->apimodel->getAccessGranted()) {
            $this->returnData['ok'] = false;
            $this->returnData['message'] = $this->apimodel->getMessage();
            printJson($this->returnData);

            return;
        }

        $this->apimodel->setFromDate($this->input->get('from'));
        $this->apimodel->setToDate($this->input->get('to'));

        //!important: please set platform before courseId
        $this->apimodel->setPlatform($this->input->get('platform'));
        $this->apimodel->setCourseId($this->input->get('courseId'));

        if (!$this->apimodel->getValidParameter()) {
            $this->returnData['ok'] = false;
            $this->returnData['message'] = $this->apimodel->getMessage();
            printJson($this->returnData);

            return;
        }

        $this->getVitalityFromPlatform($this->apimodel->getPlatform());
        printJson($this->returnData);
    }

    private function getVitalityFromPlatform($platform)
    {
        $key = getKey($platform);
        // $this->returnData['debug'] = $this->apimodel->getToDate();
        $match = array(
            '$match' => array(
                'statement.timestamp' => array(
                    '$gte' => $this->apimodel->getFromDate(),
                    '$lte' => $this->apimodel->getToDate(),
                ),
                'statement.context.extensions.'.$key.'.courseid' => array('$eq' => $this->apimodel->getCourseId()),
                'statement.context.extensions.'.$key.'.rolename' => array('$eq' => 'student'),
                '$or' => array(
                    //view a courseware
                    array('$and' => array(
                            array('statement.verb.id' => array('$eq' => 'http://id.tincanapi.com/verb/viewed')),
                            array('statement.object.definition.name.en-us' => array('$eq' => 'a courseware page')),
                        ),
                    ),
                    //start playing a video
                    array('statement.verb.display.en-us' => array('$eq' => 'started playing')),
                    //view a thread
                    array('$and' => array(
                            array('statement.verb.id' => array('$eq' => 'http://id.tincanapi.com/verb/viewed')),
                            array('statement.object.definition.name.en-us' => array('$eq' => 'a discussion thread')),
                        ),
                    ),
                    //create a thread
                    array(
                        'statement.verb.display.en-us' => array('$eq' => 'created'),
                    ),
                    //reply a thread
                    array(
                        'statement.verb.display.en-us' => array('$eq' => 'responded to'),
                    ),
                    //vote a thread
                    array(
                        'statement.verb.display.en-us' => array('$eq' => 'up voted'),
                    ),
                    array(
                        'statement.verb.display.en-us' => array('$eq' => 'down voted'),
                    ),
                    //complete a problem
                    array(
                        'statement.verb.display.en-us' => array('$eq' => 'completed'),
                    ),
                ),
            ),
        );
        $group = array(
            '$group' => array(
                '_id' => array(
                    'id' => '$statement.actor.name',
                    'name' => '$statement.actor.account.name',
                    'verb' => '$statement.verb.display.en-us',
                    'object' => '$statement.object.definition.name.en-us',
                ),
                'count' => array('$sum' => 1),
            ),
        );
        $sort = array(
            '$sort' => array(
                '_id.id' => 1,
            ),
        );
        $pipeline[$platform] = array($match, $group, $sort, array('$limit' => 20000));
        $output = $this->datamodel->getData($pipeline);
        if (!$output['ok']) {
            $this->returnData['ok'] = false;
            $this->returnData['message'] = $output['message'];
            printJson($this->returnData);

            return;
        }
        $lastStu = '';
        $currentIndex = -1;
        $dataProcess = array();
        for ($i = 0; $i < count($output['data'][$platform]['result']); ++$i) {
            if ($output['data'][$platform]['result'][$i]['_id']['id'] != $lastStu) {
                array_push($dataProcess, array(
                        '<a href="javascript:void(0);" onclick="openTeaStuView(\''.$output['data'][$platform]['result'][$i]['_id']['id'].'\', \''.$output['data'][$platform]['result'][$i]['_id']['name'].'\')">'.$output['data'][$platform]['result'][$i]['_id']['name'].'</a>', 0, 0, 0, 0, 0, 0, 0, 0,
                    ));
                $lastStu = $output['data'][$platform]['result'][$i]['_id']['id'];
                $currentIndex += 1;
            }
            $coefficient = 0;
            if ($output['data'][$platform]['result'][$i]['_id']['verb'] == 'viewed' && $output['data'][$platform]['result'][$i]['_id']['object'] == 'a courseware page') {
                //$dataProcess[$currentIndex]['Viewed a Courseware'] = $output['data'][$platform]['result'][$i]['count'];
                    $dataProcess[$currentIndex][1] = $output['data'][$platform]['result'][$i]['count'];
                $coefficient = 1;
            } elseif ($output['data'][$platform]['result'][$i]['_id']['verb'] == 'started playing') {
                //$dataProcess[$currentIndex]['Watched a Video'] = $output['data'][$platform]['result'][$i]['count'];
                    $dataProcess[$currentIndex][2] = $output['data'][$platform]['result'][$i]['count'];
                $coefficient = 1;
            } elseif ($output['data'][$platform]['result'][$i]['_id']['verb'] == 'viewed' && $output['data'][$platform]['result'][$i]['_id']['object'] == 'a discussion thread') {
                //$dataProcess[$currentIndex]['Viewed a Thread'] = $output['data'][$platform]['result'][$i]['count'];
                    $dataProcess[$currentIndex][3] = $output['data'][$platform]['result'][$i]['count'];
                $coefficient = 1;
            } elseif ($output['data'][$platform]['result'][$i]['_id']['verb'] == 'created') {
                //$dataProcess[$currentIndex]['Created a Thread'] = $output['data'][$platform]['result'][$i]['count'];
                    $dataProcess[$currentIndex][4] = $output['data'][$platform]['result'][$i]['count'];
                $coefficient = 10;
            } elseif ($output['data'][$platform]['result'][$i]['_id']['verb'] == 'responded to') {
                //$dataProcess[$currentIndex]['Replied to a Thread'] = $output['data'][$platform]['result'][$i]['count'];
                    $dataProcess[$currentIndex][5] = $output['data'][$platform]['result'][$i]['count'];
                $coefficient = 8;
            } elseif ($output['data'][$platform]['result'][$i]['_id']['verb'] == 'up voted' || $output['data'][$platform]['result'][$i]['_id']['verb'] == 'down voted') {
                //$dataProcess[$currentIndex]['Voted a Thread'] += $output['data'][$platform]['result'][$i]['count'];
                    $dataProcess[$currentIndex][6] += $output['data'][$platform]['result'][$i]['count'];
                $coefficient = 5;
            } elseif ($output['data'][$platform]['result'][$i]['_id']['verb'] == 'completed') {
                //$dataProcess[$currentIndex]['Completed a Problem'] = $output['data'][$platform]['result'][$i]['count'];
                    $dataProcess[$currentIndex][7] = $output['data'][$platform]['result'][$i]['count'];
                $coefficient = 5;
            }
                //$dataProcess[$currentIndex]['Total Score'] += $coefficient * $output['data'][$platform]['result'][$i]['count'];
                $dataProcess[$currentIndex][8] += $coefficient * $output['data'][$platform]['result'][$i]['count'];
        }
        $this->returnData['ok'] = true;
        $this->returnData['data'] = $dataProcess;
    }
}
