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
			$this->_parse();
			$this->_assignSurveysToPresenters();
			$this->_saveSurveys();
		}
	}

	private function _saveSurveys()
	{
		foreach($this->_presenters as $index => $presenterData) {
			foreach($presenterData['surveys'] as $survey) {

				// validate. so far, just check for duplicates
				$errors = $this->_validateSurvey($survey, $presenterData['seminarId']);			
				if ($errors) {
					$this->_errors[] = $errors;	
					continue;
				}
				
				$formattedSurvey = $this->_format($survey, $presenterData['seminarId']);
				$this->_insert($formattedSurvey);

			}
		}
	}

	private function _insert($survey)
	{
		try {
				//Zend_Debug::dump($survey);

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

	private function _validateSurvey($survey, $seminarId)
	{
		$errors = array();
		// check id reviewer for that seminarID exists
		$this->_errors['duplicates'][] = $this->_checkForDuplicates($survey['reviewer_unid'], $seminarId);
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
		//print_r($params);
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
			throw new Exception("surveyMapper::getSeminarData" . $e->getMessage());
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
			$mappedRow = $this->_mapRow($row); 
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
				case($i == 15):
					$mappedRow['presenter'] = $row[$i];
					break;

				// grade stats
				case($i == 11):
					$mappedRow['grade_sum'] = $row[$i];
					break;
				case($i == 12):
					$mappedRow['grade_weighted_average'] = $row[$i];
					break;
				case($i == 13):
					$mappedRow['grade_standard_deviation'] = $row[$i];
					break;

				// presentation style
				case($i == 16):
					$mappedRow['ps_pace'] = $row[$i];
					break;
				case($i == 17):
					$mappedRow['ps_eyecontact'] = $row[$i];
					break;
				case($i == 18):
					$mappedRow['ps_professionalism'] = $row[$i];
					break;
				case($i == 19):
					$mappedRow['ps_materials'] = $row[$i];
					break;
				case($i == 20):
					$mappedRow['ps_comments'] = $row[$i];
					break;

				// instructional material
				case($i == 21):
					$mappedRow['im_handouts'] = $row[$i];
					break;
				case($i == 22):
					$mappedRow['im_grammar'] = $row[$i];
					break;
				case($i == 23):
					$mappedRow['im_charts'] = $row[$i];
					break;
				case($i == 24):
					$mappedRow['im_cites'] = $row[$i];
					break;
				case($i == 25):
					$mappedRow['im_comments'] = $row[$i];
					break;

				// overall presentation	
				case($i == 26):
					$mappedRow['op_introduction'] = $row[$i];
					break;
				case($i == 27):
					$mappedRow['op_purpose'] = $row[$i];
					break;
				case($i == 28):
					$mappedRow['op_objectives'] = $row[$i];
					break;
				case($i == 29):
					$mappedRow['op_background'] = $row[$i];
					break;
				case($i == 30):
					$mappedRow['op_organization'] = $row[$i];
					break;
				case($i == 31):
					$mappedRow['op_comments'] = $row[$i];
					break;

				// clinical data
				case($i == 32):
					$mappedRow['cd_objectives'] = $row[$i];
					break;
				case($i == 33):
					$mappedRow['cd_outcome'] = $row[$i];
					break;
				case($i == 34):
					$mappedRow['cd_analysis'] = $row[$i];
					break;
				case($i == 35):
					$mappedRow['cd_samplesize'] = $row[$i];
					break;
				case($i == 36):
					$mappedRow['cd_withdrawals'] = $row[$i];
					break;
				case($i == 37):
					$mappedRow['cd_details'] = $row[$i];
					break;
				case($i == 38):
					$mappedRow['cd_comments'] = $row[$i];
					break;

				// conclusions
				case($i == 39):
					$mappedRow['cc_data'] = $row[$i];
					break;
				case($i == 40):
					$mappedRow['cc_importance'] = $row[$i];
					break;
				case($i == 41):
					$mappedRow['cc_recommendations'] = $row[$i];
					break;
				case($i == 42):
					$mappedRow['cc_role'] = $row[$i];
					break;
				case($i == 43):
					$mappedRow['cc_comments'] = $row[$i];
					break;

				// q&a
				case($i == 44):
					$mappedRow['qa_answers'] = $row[$i];
					break;
				case($i == 45):
					$mappedRow['qa_interaction'] = $row[$i];
					break;
				case($i == 46):
					$mappedRow['qa_comments'] = $row[$i];
					break;

				// overal knowledge
				case($i == 47):
					$mappedRow['ok_demonstrated'] = $row[$i];
					break;
				case($i == 48):
					$mappedRow['ok_difference'] = $row[$i];
					break;
				case($i == 49):
					$mappedRow['ok_deep'] = $row[$i];
					break;
				case($i == 50):
					$mappedRow['ok_discussion'] = $row[$i];
					break;
				case($i == 51):
					$mappedRow['ok_think'] = $row[$i];
					break;
				case($i == 52):
					$mappedRow['ok_comments'] = $row[$i];
					break;

				// comments
				case($i == 54):
					$mappedRow['comments_like'] = $row[$i];
					break;
				case($i == 55):
					$mappedRow['comments_improve'] = $row[$i];
					break;
				case($i == 56):
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


}
