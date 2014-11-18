 <?php

class Application_Model_SurveyMapper
{
	private $_dbTable;
	private $_rawData;
	private $_presenters;
	private $_surveys;
	private $_errors;

	public function __construct($rawData = null, $params = null)
	{
		if(isset($rawData) && isset($params)) {
			$this->_rawData = $rawData;
			$this->_presenters = $this->getPresenters($params);
			if($this->_presenters) {
				$this->_parse();
				$this->_assignSurveysToPresenters();
				$this->_saveSurveys();
			}
			$this->getErrors();
			
		}
	}


	public function getErrors() {
		return $this->_errors;
	}

	private function _saveSurveys()
	{
		$errors = array();
		foreach($this->_presenters as $index => $presenterData) {
			foreach($presenterData['surveys'] as $survey) {
					
				// validate. so far, just check for duplicates
				$error = $this->_validateSurvey($survey, $presenterData);
				if ($error) {
					$errors[] = $error;
					continue;
				}
				
				$formattedSurvey = $this->_format($survey, $presenterData['seminarId']);
				$this->_insert($formattedSurvey);

			}
		}
		$this->_errors = $errors;
		//error_log(print_r($this->_errors, true));
	}

	private function _insert($survey)
	{
		try {
			// begin transaction
			$this->getDbTable()->getDefaultAdapter()->beginTransaction();	

			$surveyTable = new Application_Model_DbTable_Surveys();
			
			$surveyId = $surveyTable->insert($survey);	

			// commit the transaction
			$this->getDbTable()->getDefaultAdapter()->commit();	

		} catch(Exception $e) {
			$this->getDbTable()->getDefaultAdapter()->rollback();
			echo $e->getMessage();
		}
	}

	private function _format($survey, $seminarId)
	{
		$sectionMapper = new Application_Model_SectionMapper();
		$reviewerUserSectionData = $sectionMapper->getUserSectionDataByUnid($survey['reviewer_unid'])->toArray();
		$survey['reviewer_user_section_id'] = $reviewerUserSectionData['id'];

		$survey['seminar_id'] = $seminarId;
		$survey['survey_date'] = date("Y-m-d H:i:s",strtotime($survey['survey_date']));

		unset($survey['reviewer_unid']);
		unset($survey['presenter']);

		return $survey;
	}

	private function _validateSurvey($survey, $presenterData)
	{
		// init user mapper and get presenter data 
		$userMapper = new Application_Model_UserMapper();
		$presenter = $userMapper->find($presenterData['userId']);

		// check for missing unid
		if(empty($survey['reviewer_unid'])) {
			return array(
				'error_type' => 'missing_unid',
				'qualtrics_id' => $survey['qualtrics_id'], 
				'survey_date' => $survey['survey_date'], 
				'presenter_name' => sprintf("%s, %s", $presenter['last_name'], $presenter['first_name']),
				'presenter_unid' => $presenter['unid']
			);
		}

		// get reviewer data. the following conditions have unid
		$reviewer = $userMapper->findUserByUnid($survey['reviewer_unid']);

		// check for invalid user
		if(empty($reviewer['unid'])) {
			return array(
				'error_type' => 'Invalid user',
				'qualtrics_id' => $survey['qualtrics_id'], 
				'survey_date' => $survey['survey_date'], 
				'presenter_name' => sprintf("%s, %s", $presenter['last_name'], $presenter['first_name']),
				'presenter_unid' => $presenter['unid']
			);
		}

		// check for self review
		if($reviewer['unid'] == $presenter['unid']) {
			return array(
					'error_type' => 'presenter reviewed self',
					'qualtrics_id' => $survey['qualtrics_id'], 
					'survey_date' => $survey['survey_date'], 
					'presenter_name' => sprintf("%s, %s", $presenter['last_name'], $presenter['first_name']),
					'presenter_unid' => $presenter['unid']
					);
		}
	
		// check id reviewer for that seminarID exists
		$duplicates = $this->_checkForDuplicates($survey['reviewer_unid'], $presenterData['seminarId']);
		if(!empty($duplicates)) {
			$duplicateData = array(
					'error_type' => 'duplicate_survey',
					'qualtrics_id' => $survey['qualtrics_id'], 
					'survey_date' => $survey['survey_date'], 
					'presenter_name' => sprintf("%s, %s", $presenter['last_name'], $presenter['first_name']),
					'presenter_unid' => $presenter['unid'],
					'duplicates' => array(
						'surveys' => $duplicates,
						'reviewer' => $reviewer
						)
					);

			if(!empty($reviewer)) {
				$duplicateData['reviewer'] = $reviewer;
			}

			return $duplicateData;
		}

	
	}


	private function _checkForMissingUnid($survey)
	{
		if(empty($survey['reviewer_unid'])) {
			return array(
				'error_type' => 'missing_unid',
				'qualtrics_id' => $survey['qualtrics_id'], 
				'survey_date' => $survey['survey_date'], 
				'presenter' => $survey['presenter']
			);
		} else {
			return false;
		}
	}


	private function _checkForDuplicates($reviewerUnid, $seminarId)
	{

		try {
			// get reviewer user id
			$userMapper = new Application_Model_UserMapper();
			$user = $userMapper->findUserByUnid($reviewerUnid);

			// get review current-year user__section data
			$sectionMapper = new Application_Model_SectionMapper();
			$reviewerUserSectionData = $sectionMapper->getUserSectionData($user['id']);	

			// get duplicate survey metadata 
			$surveyTable = new Application_Model_DbTable_Surveys
();
			$select = $surveyTable
				->select()
				->from($surveyTable, array('id', 'seminar_id', 'reviewer_user_section_id', 'survey_date'))
				->where('seminar_id = ?', $seminarId)
				->where('reviewer_user_section_id = ?', $reviewerUserSectionData['id']);
			$rows = $surveyTable->fetchAll($select);
			if(!empty($rows)) {
				return $rows->toArray();	
			} else {
				return false;
			}
		} catch(Exception $e) {
			throw new Exception('SurveyMapper::_checkDuplicate: ' . $e->getMessage());
		}
	}

	private function _assignSurveysToPresenters()
	{
		foreach($this->_presenters as $index => $presenter) {
			$this->_presenters[$index]['surveys'] = $this->_surveys[$index];
		}
	}

	public function getPresenters($params)
	{
		$prelimPresenters = array();
		for ($i = 1; $i <= 10; $i++) {
			if(empty($params["unid$i"])) {
				continue;
			}
			$prelimPresenters[$i] = array(
				'unid' => $params["unid$i"],
				'seminarDate' => $params["seminarDate$i"] 
			);
		}

		if($this->_validatePresenters($prelimPresenters)) {
			$presenters = array();

			foreach($prelimPresenters as $index => $params) {
				$seminarData = $this->getSeminarData($params['unid'], $params['seminarDate']);

				$presenter['seminarId'] = $seminarData['id'];
				$presenter['unid'] = $params['unid'];
				$presenter['seminarDate'] = $params['seminarDate'];
				$presenter['presenterUserSectionId'] = $seminarData['presenter_user_section_id'];
				$presenter['userId'] = $seminarData['userId'];

				$presenters[$index] = $presenter;
			}
			return $presenters;

		} else {
			return false;	
		}

	}

	private function _validatePresenters($presenters)
	{
		$userMapper = new Application_Model_UserMapper();
		$errors = array();
		foreach($presenters as $presenter) {
			$user = $userMapper->findUserByUnid($presenter['unid']);
			if(empty($user)) {
				$errors[] = array(
					'error_type' => 'invalid_presenter',
					'presenter_unid' => $presenter['unid'], 
					'seminarDate' => $presenter['seminarDate']
				);
			}
		}
		if(!empty($errors)) {
			foreach($errors as $error) {
				$this->_errors[] = $error;
			}
			return false;
		} else {
			return true;
		}
	}

	public function getSeminarData($unid, $seminarDate){
		try {

			$userMapper = new Application_Model_UserMapper();
			$sectionMapper = new Application_Model_SectionMapper();
			$seminarMapper = new Application_Model_SeminarMapper();

			$user = $userMapper->findUserByUnid($unid);

			$userSectionData= $sectionMapper->getUserSectionData($user['id'])->toArray();

			$seminar = $seminarMapper->findByDateAndUserSectionId($seminarDate, $userSectionData['id']);	

			if(empty($seminar)) {
				$newSeminar = $seminarMapper->save(array(
					'seminarDate' => $seminarDate,
					'userSectionId' => $userSectionData['id']
				));

				if (isset($newSeminar)) {
					
					$newSeminarArray = $newSeminar->toArray();
					$seminarData = $newSeminarArray[0];
				}

			} else {
				$seminarData = $seminar->toArray();
			}
			
			if(!empty($seminarData)) {
				$seminarData['userId'] = $user['id'];
				return $seminarData;
			}
		} catch (Exception $e) {
			echo "surveyMapper::getSeminarData" . $e->getMessage();
			print_r(array($unid, $seminarDate));
			//throw new Exception("surveyMapper::getSeminarData" . $e->getMessage());
		}
	}	

	public function getSeminarSurveys($seminarId)
	{
		try {
			$table = $this->getDbTable();
			$select = $table->select()->where('seminar_id = ?', $seminarId);
			$rows = $table->fetchAll($select);
			if(!empty ($rows)) {
				$surveys = $this->getReviewerData($rows->toArray());

				return $surveys;
			} else {
				return false;
			}
		} catch (Exception $e) {
			throw new Exception("surveyMapper::getSeminarSurveys: " . $e->getMessage());
		}	

	}


	public function getSingleSurveyReviewerData($survey)
	{
	//	print_r($survey);
		$userMapper = new Application_Model_UserMapper();
		$userRoleMapper = new Application_Model_UserRoleMapper();
		$survey['reviewer'] = $userMapper->getUserByUserSectionId($survey['reviewer_user_section_id']);
		$surveys['reviewer']['user_section_id'] = $survey['reviewer_user_section_id'];

		$roleData = $userRoleMapper->getUserRoleFromUserId($surveys['reviewer']['id']);
		$surveys['reviewer']['role_id'] = $roleData['role_id'];	
		unset($surveys['reviewer_user_seciton_id']);
		return $survey;
	}


	public function getReviewerData($surveys)
	{
		$userMapper = new Application_Model_UserMapper();
		$userRoleMapper = new Application_Model_UserRoleMapper();
		foreach($surveys as $index =>$survey) {
			$surveys[$index]['reviewer'] = $userMapper->getUserByUserSectionId($survey['reviewer_user_section_id']);
			$surveys[$index]['reviewer']['user_section_id'] = $survey['reviewer_user_section_id'];

			$roleData = $userRoleMapper->getUserRoleFromUserId($surveys[$index]['reviewer']['id']);
			$surveys[$index]['reviewer']['role_id'] = $roleData['role_id'];	
			unset($surveys[$index]['reviewer_user_seciton_id']);
		}
		return $surveys;
	}


	private function _parse()
	{
		$surveyData = array();
		foreach(json_decode($this->_rawData) as $row) {
			//error_log("----------------> " . print_r($row, true));
			$mappedRow = $this->_mapRow($row[0]);
			//error_log(print_r($mappedRow, true));
			$this->_surveys[$mappedRow['presenter']][] = $mappedRow;
		}
	}

	private function _mapRow($row)
	{
		$mappedRow = array();
		for ($i = 0; $i < count($row); $i++) {
			//$row[$i] = addslashes($row[$i]);
			switch(true) {
				// metadata
				case($i == 0):
					$mappedRow['qualtrics_id'] = $row[$i];
					break;
				case($i == 3):
					$mappedRow['reviewer_unid'] = $row[$i];
					break;
				case($i == 8):
					$mappedRow['survey_date'] = $row[$i];
					break;
				case($i == 14):
					$mappedRow['presenter'] = $row[$i];
					break;

				// grade stats
				case($i == 10):
					$mappedRow['grade_sum'] = $row[$i];
					break;
				case($i == 11):
					$mappedRow['grade_weighted_average'] = $row[$i];
					break;
				case($i == 12):
					$mappedRow['grade_standard_deviation'] = $row[$i];
					break;

				// presentation style
				case($i == 15):
					$mappedRow['ps_pace'] = $row[$i];
					break;
				case($i == 16):
					$mappedRow['ps_eyecontact'] = $row[$i];
					break;
				case($i == 17):
					$mappedRow['ps_professionalism'] = $row[$i];
					break;
				case($i == 18):
					$mappedRow['ps_materials'] = $row[$i];
					break;
				case($i == 19):
					$mappedRow['ps_comments'] = $row[$i];
					break;

				// instructional material
				case($i == 20):
					$mappedRow['im_handouts'] = $row[$i];
					break;
				case($i == 21):
					$mappedRow['im_grammar'] = $row[$i];
					break;
				case($i == 22):
					$mappedRow['im_charts'] = $row[$i];
					break;
				case($i == 23):
					$mappedRow['im_cites'] = $row[$i];
					break;
				case($i == 24):
					$mappedRow['im_comments'] = $row[$i];
					break;

				// overall presentation	
				case($i == 25):
					$mappedRow['op_introduction'] = $row[$i];
					break;
				case($i == 26):
					$mappedRow['op_purpose'] = $row[$i];
					break;
				case($i == 27):
					$mappedRow['op_objectives'] = $row[$i];
					break;
				case($i == 28):
					$mappedRow['op_background'] = $row[$i];
					break;
				case($i == 29):
					$mappedRow['op_organization'] = $row[$i];
					break;
				case($i == 30):
					$mappedRow['op_comments'] = $row[$i];
					break;

				// clinical data
				case($i == 31):
					$mappedRow['cd_objectives'] = $row[$i];
					break;
				case($i == 32):
					$mappedRow['cd_outcome'] = $row[$i];
					break;
				case($i == 33):
					$mappedRow['cd_analysis'] = $row[$i];
					break;
				case($i == 34):
					$mappedRow['cd_samplesize'] = $row[$i];
					break;
				case($i == 35):
					$mappedRow['cd_withdrawals'] = $row[$i];
					break;
				case($i == 36):
					$mappedRow['cd_details'] = $row[$i];
					break;
				case($i == 37):
					$mappedRow['cd_comments'] = $row[$i];
					break;

				// conclusions
				case($i == 38):
					$mappedRow['cc_data'] = $row[$i];
					break;
				case($i == 39):
					$mappedRow['cc_importance'] = $row[$i];
					break;
				case($i == 40):
					$mappedRow['cc_recommendations'] = $row[$i];
					break;
				case($i == 41):
					$mappedRow['cc_role'] = $row[$i];
					break;
				case($i == 42):
					$mappedRow['cc_comments'] = $row[$i];
					break;

				// q&a
				case($i == 43):
					$mappedRow['qa_answers'] = $row[$i];
					break;
				case($i == 44):
					$mappedRow['qa_interaction'] = $row[$i];
					break;
				case($i == 45):
					$mappedRow['qa_comments'] = $row[$i];
					break;

				// overal knowledge
				case($i == 46):
					$mappedRow['ok_demonstrated'] = $row[$i];
					break;
				case($i == 47):
					$mappedRow['ok_difference'] = $row[$i];
					break;
				case($i == 48):
					$mappedRow['ok_deep'] = $row[$i];
					break;
				case($i == 49):
					$mappedRow['ok_discussion'] = $row[$i];
					break;
				case($i == 50):
					$mappedRow['ok_think'] = $row[$i];
					break;
				case($i == 51):
					$mappedRow['ok_comments'] = $row[$i];
					break;

				// comments
				case($i == 53):
					$mappedRow['comments_like'] = $row[$i];
					break;
				case($i == 54):
					$mappedRow['comments_improve'] = $row[$i];
					break;
				case($i == 55):
					$mappedRow['comments_overall'] = $row[$i];
					break;
			}
		}
		return $mappedRow;
	}

	public function setDbTable($dbTable)
	{
		if (is_string($dbTable)) {
			$dbTable = new $dbTable();
		}
		if (!$dbTable instanceof Zend_Db_Table_Abstract) {
			throw new Exception('Invalid table data gateway provided');
		}
		$this->_dbTable = $dbTable;
		return $this;
	}

	public function getDbTable()
	{
		if (null === $this->_dbTable) {
			$this->setDbTable('Application_Model_DbTable_Surveys');
		}
		return $this->_dbTable;
	}




	public function averageOne($survey)
	{
		$averages = $this->_averageArray();

		foreach($survey as $key => $value) {
			if((strpos($key, 'ps_') === 0) && !(strpos($key, 'comments') > 0)){
				$averages['ps']['values'][] = $value;
				$averages['ps']['total'] += $value;
				$averages['ps']['qValues'][$key][] = $value;
			}

			if((strpos($key, 'im_') === 0) && !(strpos($key, 'comments') > 0)){
				$averages['im']['values'][] = $value;
				$averages['im']['total'] += $value;
				$averages['im']['qValues'][$key][] = $value;
			}	

			if((strpos($key, 'op_') === 0) && !(strpos($key, 'comments') > 0)){
				$averages['op']['values'][] = $value;
				$averages['op']['total'] += $value;
				$averages['op']['qValues'][$key][] = $value;
			}

			if((strpos($key, 'cd_') === 0) && !(strpos($key, 'comments') > 0)){
				$averages['cd']['values'][] = $value;
				$averages['cd']['total'] += $value;
				$averages['cd']['qValues'][$key][] = $value;
			}


			if((strpos($key, 'cc_') === 0) && !(strpos($key, 'comments') > 0)){
				$averages['cc']['values'][] = $value;
				$averages['cc']['total'] += $value;
				$averages['cc']['qValues'][$key][] = $value;
			}


			if((strpos($key, 'qa_') === 0) && !(strpos($key, 'comments') > 0)){
				$averages['qa']['values'][] = $value;
				$averages['qa']['total'] += $value;
				$averages['qa']['qValues'][$key][] = $value;
			}


			if((strpos($key, 'ok_') === 0) && !(strpos($key, 'comments') > 0)){
				$averages['ok']['values'][] = $value;
				$averages['ok']['total'] += $value;
				$averages['ok']['qValues'][$key][] = $value;
			}

		}

		$averages = $this->_calculateAverages($averages);

		return $averages;
	}


	public function averageAll($surveys, $role_id)
	{
		$averages = $this->_averageArray();
		foreach($surveys as $survey) {

			if(isset($role_id) && ($survey['reviewer']['role_id'] != $role_id)) {
				continue;
			}	

			foreach($survey as $key => $value) {
				if((strpos($key, 'ps_') === 0) && !(strpos($key, 'comments') > 0)){
					$averages['ps']['values'][] = $value;
					$averages['ps']['total'] += $value;
					$averages['ps']['qValues'][$key]['values'][] = $value;
					$averages['ps']['qValues'][$key]['counts'] = array_count_values($averages['ps']['qValues'][$key]['values']);
				}

				if((strpos($key, 'im_') === 0) && !(strpos($key, 'comments') > 0)){
					$averages['im']['values'][] = $value;
					$averages['im']['total'] += $value;
					$averages['im']['qValues'][$key]['values'][] = $value;
					$averages['im']['qValues'][$key]['counts'] = array_count_values($averages['im']['qValues'][$key]['values']);
				}	

				if((strpos($key, 'op_') === 0) && !(strpos($key, 'comments') > 0)){
					$averages['op']['values'][] = $value;
					$averages['op']['total'] += $value;
					$averages['op']['qValues'][$key]['values'][] = $value;
					$averages['op']['qValues'][$key]['counts'] = array_count_values($averages['op']['qValues'][$key]['values']);
				}

				if((strpos($key, 'cd_') === 0) && !(strpos($key, 'comments') > 0)){
					$averages['cd']['values'][] = $value;
					$averages['cd']['total'] += $value;
					$averages['cd']['qValues'][$key]['values'][] = $value;
					if($value != 0) {
						$averages['cd']['qValues'][$key]['counts'] = array_count_values($averages['cd']['qValues'][$key]['values']);
					} 
				}


				if((strpos($key, 'cc_') === 0) && !(strpos($key, 'comments') > 0)){
					$averages['cc']['values'][] = $value;
					$averages['cc']['total'] += $value;
					$averages['cc']['qValues'][$key]['values'][] = $value;
					$averages['cc']['qValues'][$key]['counts'] = array_count_values($averages['cc']['qValues'][$key]['values']);
				}


				if((strpos($key, 'qa_') === 0) && !(strpos($key, 'comments') > 0)){
					$averages['qa']['values'][] = $value;
					$averages['qa']['total'] += $value;
					$averages['qa']['qValues'][$key]['values'][] = $value;
					$averages['qa']['qValues'][$key]['counts'] = array_count_values($averages['qa']['qValues'][$key]['values']);
				}


				if((strpos($key, 'ok_') === 0) && !(strpos($key, 'comments') > 0)){
					$averages['ok']['values'][] = $value;
					$averages['ok']['total'] += $value;
					$averages['ok']['qValues'][$key]['values'][] = $value;
					$averages['ok']['qValues'][$key]['counts'] = array_count_values($averages['ok']['qValues'][$key]['values']);
				}

			}
		}
		$averages = $this->_calculateAverages($averages);
		return $averages;
	}

	private function _countQuestionValues($averages)
	{

	}

	private function _calculateAverages($averages)
	{
		foreach($averages as $field => $data){
			// adjust count to exclude NA(0) values where NA = true.
			$values = $data['values'];
			foreach($values as $index => $value) {
				if($value == 0) {
					unset($values[$index]);
				}
			}
			// only calculate if count > 0
			if(count($values) > 0) {
				//$averages[$field]['average'] = round(array_sum($data['values'])/count($data['values']), 2);
				$averages[$field]['average'] = round(array_sum($values)/count($values), 2);
			} else {
				$averages[$field]['average'] = 0;
			}
		}
		return $averages;
	}


	private function _averageArray()
	{
		$averageArray = array(

				'ps' => array(
					'total' => 0,
					'values' => array(),
					'weight' => 0.05,
					'qValues' => array(),
					'NA' => false

					),
				'im' => array(
					'total' => 0,
					'values' => array(),
					'weight' => 0.1,
					'qValues' => array(),
					'NA' => true
					),
				'op' => array(
					'total' => 0,
					'values' => array(),
					'weight' => 0.1,
					'qValues' => array(),
					'NA' => false
					),
				'cd' => array(
						'total' => 0,
						'values' => array(),
						'weight' => 0.2,
						'qValues' => array(),
						'NA' => true
						),
				'cc' => array(
						'total' => 0,
						'values' => array(),
						'weight' => 0.2,
						'qValues' => array(),
						'NA' => false
						),
				'qa' => array(
						'total' => 0,
						'values' => array(),
						'weight' => 0.15,
						'qValues' => array(),
						'NA' => false
						),
				'ok' => array(
						'total' => 0,
						'values' => array(),
						'weight' => 0.2,
						'qValues' => array(),
						'NA' => false
						)

					);

		return $averageArray;
	}



}
