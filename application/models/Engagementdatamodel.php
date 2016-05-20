<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class EngagementDataModel extends DataModel{
    
    private $engagementClassify;
    
    function __construct(){
        parent::__construct();
        $this->engagementClassify = load_engagement_list();
    }    
    
    
    // TODO: it seems to need to get the whole list of course ID
    
    // Get single course ID engagement data
    public function getEngageData($platform, $courseId, $fromDate, $toDate) {
        $pipeline = $this->getPipeline($platform, $courseId, $fromDate, $toDate);
        $output = $this->getData($pipeline);
        
        if (count($output['data'][$platform]['result']) > 0) {
            // return raw data
            return $output['data'][$platform]['result'];
        } else {
            return NULL;
        }
    }
    
    // process the raw data to statistic record
//    public function convertToDBStatisticData($rawData, $courseList) {
        
        // convert each statistic record
        
//    }
    
    // process the raw data to data
    public function convertToDisplayData($rawData) {
        
        $newData = array();
        
		// init for first block
        $lastDate = $this->removeTimeFromDate($rawData[0]['statement']['timestamp']);
        $newData[] = $this->createDataBlock($lastDate);
        
        // loop and do matching
        for($i = 0; $i < count($rawData); $i++){
            
            $currentResult = $rawData[$i]['statement'];				
            $currentVerb = $currentResult['verb']['display']['en-us'];
            $currentName = $currentResult['object']['definition']['name']['en-us'];
            $currentDate = $this->removeTimeFromDate($currentResult['timestamp']);
            
            if ($currentDate != $lastDate) {
                // create new block
                $newData[] = $this->createDataBlock($currentDate);				
                $lastDate = $currentDate;
            }
            
            // update last block
            $len = count($newData);	
            $newData[$len-1][$this->checkCategory($currentVerb, $currentName)] += 1;		
        }
        
        return $newData;        
    }
        
    
    /* Private function */
	private function getPipeline($platform, $courseId, $fromDate = NULL, $toDate = NULL, $shouldGroup = false) {
		
		$key = getKey($platform);
		
		$match = array(
			"\$match" => array(
				"statement.context.extensions.".$key.".courseid" => array("\$eq" => $courseId),
				"statement.context.extensions.".$key.".rolename" => array("\$eq" => "student"),
				"\$or" => $this->getOrArray()
			),
		);
        
        if($fromDate != null){
            $match['$match']['statement.timestamp'] = array(
                "\$gte" => $fromDate . "T00:00",
                "\$lte" => $toDate . "T23:59",
            );
        }
        
		if($this->apimodel->getRole() == 'student'){
			$match['$match']['statement.actor.name'] = array("\$eq" => $this->apimodel->getKeepId());
		}
		
		$project = array(
			"\$project" => array(
				"_id" => 0,
				"statement.verb.display.en-us" => 1,
				"statement.object.definition.name.en-us" => 1,
				"statement.timestamp" => 1,
			)
		);
        
		$sort = array(
			"\$sort" => array(				
				"statement.timestamp" => -1,				
			)
		);
        
        if ($shouldGroup) {
            $group = array(
                  "\$group" => array(
                    "_id" => array(
                        "Engagement" => array("\$concat" => array("\$statement.verb.display.en-us", " ", "\$statement.object.definition.name.en-us")),
                        "date" => array("\$substr"=>array("\$statement.timestamp", 0, 10,),),
                    ),
                    "count" => array("\$sum" => 1)
                )
            );
            
            return array(
			    $platform => array($match, $project, $group, $sort)
		    );	
        }
        
		return array(
			$platform => array($match, $project, $sort)
		);		
	}
    
    private function getOrArray() {
				
		$returnArray = array();
		
		foreach ($this->engagementClassify as $category => $verbStateArray) {
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
