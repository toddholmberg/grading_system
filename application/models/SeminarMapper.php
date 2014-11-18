<?php

class Application_Model_SeminarMapper
{
	const PREP_SCORE_MAX = 35;
	const PROF_SCORE_MAX = 100;
	const PRECISION = 2;
	protected $_dbTable;

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
			$this->setDbTable('Application_Model_DbTable_Seminars');
		}
		return $this->_dbTable;
	}

	public function save($data)
	{
		$seminarData = array(
				'id' => isset($data['id']) ? $data['id'] : null,
				'date'   => $data['seminarDate'],
				'presenter_user_section_id' => $data['userSectionId']
				);

		if (empty($seminarData['id'])) {
			// insert seminar
			try {
				$newSeminar = $this->insert($seminarData);
				return $newSeminar;
			} catch(Exception $e) {
				return array('error' => array('message'=> $e->getMessage()));
			}
		} else { 
			// update update	
			throw new Exception('Seminars are not yet editable');
		}
	}

	public function insert($seminarData)
	{
		try {
			// drop 'id' field from seminarData array
			unset($seminarData['id']);

			// begin transaction
			$this->getDbTable()->getDefaultAdapter()->beginTransaction();	

			// insert new user
			$seminar = new Application_Model_DbTable_Seminars();
			$seminarId = $seminar->insert($seminarData);

			$this->getDbTable()->getDefaultAdapter()->commit();

			$newSeminar = $seminar->find($seminarId);
					
			return $newSeminar;
	
		} catch(Exception $e) {
			$this->getDbTable()->getDefaultAdapter()->rollback();
			throw new Exception($e->getMessage());
		}
	}

	public function find($seminarId)
	{
		try {
			$result = $this->getDbTable()->find($seminarId);
			if (0 == count($result)) {
				throw new Exception("Seminar with id $seminarId does not exist.");
			}
			$seminar = $result->current();
			return $seminar;
		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		}
	}	

	public function findById($seminarId)
	{
		// get current academic year
		try {
			$db = Zend_Db_Table::getDefaultAdapter();

			$sql = 'select s.id as seminar_id, s.date as seminar_date, s.presenter_user_section_id, us.user_id as presenter_user_id, user.unid as presenter_unid, user.last_name as presenter_last_name, user.first_name as presenter_first_name from seminar s left join user__section us on s.presenter_user_section_id = us.id left join user on us.user_id = user.id where s.id = :seminarId';

			$sth = $db->prepare($sql);

			$sth->bindParam(':seminarId', $seminarId);

			if($sth->execute()) {

				$result = $sth->fetch(PDO::FETCH_ASSOC);
				return $result;

			} else {
				throw new Exception($sth->errorInfo());
			}
		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		}
	}



	public function findByDateAndUserSectionId($date, $userSectionId)
	{
		try{
			$table = $this->getDbTable();
			$select = $table->select()
				->where('date = ?', $date)
				->where('presenter_user_section_id = ?', $userSectionId);
			$row = $table->fetchRow($select);

			if (!empty($row)) {
				return $row;
			} else {
				return false;
			}
		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		}	
	}

	public function fetchAll()
	{
		$resultSet = $this->getDbTable()->fetchAll();
		return $resultSet->toArray();
	}

	public function findCurrentSeminars($pYearId = null, $sectionId = null)
	{
		// get current academic year
		try {
			$academicYears = new Application_Model_DbTable_AcademicYears();
			$currentAcademicYear = $academicYears->getCurrentAcademicYear();
			$db = Zend_Db_Table::getDefaultAdapter();
			$sql = 'select s.id as seminar_id, s.date as seminar_date, s.presenter_user_section_id, us.user_id as presenter_user_id, user.unid as presenter_unid, user.last_name as presenter_last_name, user.first_name as presenter_first_name from seminar s left join user__section us on s.presenter_user_section_id = us.id left join academic_year__p_year__section aps on aps.id = us.section_id left join user on us.user_id = user.id where aps.academic_year_id = :academic_year_id';
			if(isset($pYearId) && isset($sectionId)) {
				$sql .= ' and aps.p_year_id = :pyear and aps.section_id = :section';
			}

			$sth = $db->prepare($sql);
			$sth->bindParam(':academic_year_id', $currentAcademicYear['id']);
			if(isset($pYearId) && isset($sectionId)) {
				$sth->bindParam(':pyear', $pYearId);
				$sth->bindParam(':section', $sectionId);
			}
			if($sth->execute()) {
				$result = $sth->fetchAll(PDO::FETCH_ASSOC);
				return $result;
			} else {
				throw new Exception($sth->errorInfo());
			}
		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		}
	}


	public function saveScores($data)
	{
		/*
		Array
			(
				[prep_3_86] => 6
				[prof_3_86] => 7
			)
		*/

		// build score array from $data
		$scoreArray = array();
		foreach($data as $key => $value) {
			$keyData = explode('_', $key);
			$scoreArray['seminar_id'] = $keyData[1];
			switch($keyData[0]) {
				case 'prep':
					$scoreArray[$keyData[0]] = $value;
					break;
				case 'prof':
					$scoreArray[$keyData[0]] = $value;
					break;			
			}
			$scoreArray['grader_user_id'] = $keyData[2];
		}
		$scoreArray['presenter_user_section_id'] = $data['presenter_user_section_id'];


		if($scoreArray['prep'] > self::PREP_SCORE_MAX) {
			$scoreArray['prep'] = self::PREP_SCORE_MAX;
		}
		if($scoreArray['prof'] > self::PROF_SCORE_MAX) {
			$scoreArray['prof'] = self::PROF_SCORE_MAX;
		}

		$scoresTable = new Application_Model_DbTable_Scores();
		$select = $scoresTable->select()
			->where('seminar_id = ?', $scoreArray['seminar_id'])
			->where('grader_user_id = ?', $scoreArray['grader_user_id']);
			
		$row = $scoresTable->fetchRow($select);
		if(empty($row)) {
			$newRow = $scoresTable->createRow();
			$newRow->seminar_id= $scoreArray['seminar_id'];
			$newRow->prep = $scoreArray['prep'];
			$newRow->prof = $scoreArray['prof'];
			$newRow->grader_user_id = $scoreArray['grader_user_id'];
			$newRow->save();
			$scoreData = $newRow;
		} else {
			$row->prep = $scoreArray['prep'];
			$row->prof = $scoreArray['prof'];
			$row->save();
			$scoreData = $row;
		}

		$averageScoreArray = $this->getFacultyScoreAverages($scoreArray['seminar_id']);
		$averageScoreArray['grader_user_id'] = $scoreArray['grader_user_id'];
		$averageScoreArray['presenter_user_section_id'] = $scoreArray['presenter_user_section_id'];

		$attendanceScore = $this->calculateAttendanceScore($scoreArray['presenter_user_section_id'], $scoreArray['seminar_id'], $scoreArray['grader_user_id']);

		$averageScoreArray['finalScore'] = $this->getFinalScore($scoreArray['seminar_id'], $scoreArray['presenter_user_section_id'], $scoreData->prep, $scoreData->prof, $attendanceScore);

		return $averageScoreArray;	
	}

	public function getFinalScore($seminarId, $presenterUserSectionId, $prepAvg, $profAvg, $attendanceScore)
	{
		$surveyMapper = new Application_Model_SurveyMapper();
		$surveys = $surveyMapper->getSeminarSurveys($seminarId);

		$facultyAverages = $surveyMapper->averageAll($surveys, 3);
		$studentAverages = $surveyMapper->averageAll($surveys, 2);

		// debug: set attendance to 100 for final score validation
		//$attendancescore = round((12 * 100)/12, 2);
		return $this->finalScore($facultyAverages, $studentAverages, $attendanceScore,$prepAvg,$profAvg);
	}

	public function getGraderUserIdByPresenterUserSectionId($presenterUserSectionId)
	{
			$sectionMapper = new Application_Model_SectionMapper();
			$graders = $sectionMapper->getUserSectionGraders($presenterUserSectionId);
			$graderUserId = $graders[0]['user_id'];
			return $graderUserId;
	}


	public function calculateAttendanceScore($userSectionId, $seminarId, $graderUserId = null)
	{
		if(empty($graderUserId)) {
			$graderUserId = $this->getGraderUserIdByPresenterUserSectionId($userSectionId);
		}	

		// get presenter section config
		$userMapper = new Application_Model_UserMapper();
		$presenterData = $userMapper->getUserByUserSectionId($userSectionId);
		$sectionMapper = new Application_Model_SectionMapper();
		$presenterSectionMap = $sectionMapper->getUserCurrentSectionMapId($presenterData['id']);
		$sectionConfig = $sectionMapper->getSectionConfig($presenterSectionMap['id']);

		/**
		 * if attended > attendance required in section_config table
		 * then attended will equal the required value
		 */
		$attendanceRequired = $sectionConfig->attendance_count;
		$attended = ($this->attended($userSectionId) > $attendanceRequired) ? $attendanceRequired : $this->attended($userSectionId);

		// init scores table
		$scoresTable = new Application_Model_DbTable_Scores();
		
		// get save attendance value
		$savedSelect = $scoresTable->select()
			->from($scoresTable, array('attendance'))
			->where('seminar_id = ?', $seminarId);
		$savedScore = $scoresTable->fetchRow($savedSelect)->attendance;

		if($attended > $savedScore) {
			// save new score to scoring table
			$scoreRowId = $scoresTable->update(
					array('attendance' => $attended),
					$scoresTable->getAdapter()->quoteInto('seminar_id = ?', $seminarId)
					);	
		}		

		// return score for output
		$attendanceScore = round((($attended/$attendanceRequired) * 100), 2);
		return $attendanceScore;
	
	}

	public function getAttendanceScore($seminarId, $userSectionId)
	{
		// get presenter section config
		$userMapper = new Application_Model_UserMapper();
		$presenterData = $userMapper->getUserByUserSectionId($userSectionId);
		$sectionMapper = new Application_Model_SectionMapper();
		$presenterSectionMap = $sectionMapper->getUserCurrentSectionMapId($presenterData['id']);
		$sectionConfig = $sectionMapper->getSectionConfig($presenterSectionMap['id']);

		$attendanceRequired = $sectionConfig->attendance_count;

		$scoresTable = new Application_Model_DbTable_Scores();
		$row = $scoresTable->fetchRow(
				$scoresTable->select()->where('seminar_id = ?', $seminarId)
				);
		if(!empty($row)) {

			// return score for output
			$attendanceScore = round((($row->attendance/$attendanceRequired) * 100), 2);
			return $attendanceScore;
		} else {
			return 0;
		}
	}

/*
	public function attendanceScore($userSectionId)
	{
		// get presenter section config
		$userMapper = new Application_Model_UserMapper();
		$presenterData = $userMapper->getUserByUserSectionId($userSectionId);
		$sectionMapper = new Application_Model_SectionMapper();
		$presenterSectionMap = $sectionMapper->getUserCurrentSectionMapId($presenterData['id']);
		$sectionConfig = $sectionMapper->getSectionConfig($presenterSectionMap['id']);

		 //* if attended > attendance required in section_config table
		 //* then attended will equal the required value
		$attendanceRequired = $sectionConfig->attendance_count;
		$attended = ($this->attended($userSectionId) > $attendanceRequired) ? $attendanceRequired : $this->attended($userSectionId);

		return round((($attended/$attendanceRequired) * 100), 2);
	
	}
*/

	public function getFacultyScoreAverages($seminarId)
	{
		$table = new Application_Model_DbTable_Scores();
		$rows = $table->fetchAll(
			$table->select()->where('seminar_id = ?', $seminarId)
		);
		$rowArray = $rows->toArray();
		
		$averages = $this->averageScores($rowArray);

		$averageScoreArray = array(
			'seminar_id' => $seminarId,
			'prepAvg' => $averages['prepAvg'],
			'profAvg' => $averages['profAvg'],
		);

		return $averageScoreArray;
	}

	public function averageScores($rowArray)
	{

		$prep = 0;
		$prof = 0;
		foreach($rowArray as $row) {
			$prep += $row['prep'];
			$prof += $row['prof'];	
		}

		// Allows for any number of graders.
		// As of 12/2012, it changed from two graders
		// per section to one.
		$count = !empty($rowArray) ? count($rowArray) : 1;
		$prepAvg = $prep/$count;
		$profAvg = $prof/$count;
		
		$averages = array(
			'prepAvg' => $prepAvg,
			'profAvg' => $profAvg
		);

		return $averages;
	}

	public function getSeminarScores($seminarId)
	{
		$table = new Application_Model_DbTable_Scores();
		$rows = $table->fetchAll(
				$table->select()->where('seminar_id = ?', $seminarId)
				);
		
		if(!empty($rows)) {
			$rowArray = $rows->toArray();

			$graderScores = array();
			foreach($rowArray as $row) {
				$graderScores[$row['grader_user_id']] = array(
					'grader_user_id' => $row['grader_user_id'],
					'prep' => $row['prep'],
					'prof' => $row['prof']
				);
			}

			$averages = $this->averageScores($rowArray);
			$scoreArray = array(
				'seminar_id' => $seminarId,
				'prepAvg' => $averages['prepAvg'],
				'profAvg' => $averages['profAvg'],
				'graderScores' => $graderScores
			);
			return $scoreArray;
		} else {
			return false;
		}
	}


	public function attendanceDates($presenterUserSectionId)
	{
		try{
			$db = Zend_Db_Table::getDefaultAdapter();
			$sql = 'SELECT seminar.date, user.last_name, user.first_name FROM survey LEFT JOIN seminar ON survey.seminar_id = seminar.id LEFT JOIN user__section us ON us.id = seminar.presenter_user_section_id LEFT JOIN user ON us.user_id = user.id WHERE survey.reviewer_user_section_id = :presenterUserSectionId';
			$sth = $db->prepare($sql);
			$sth->bindParam(':presenterUserSectionId', $presenterUserSectionId);
			if($sth->execute()) {
				$results = $sth->fetchAll(PDO::FETCH_ASSOC);
				return $results;
			} else {
				echo Zend_Debug::dump($this->errorInfo());
			}	
		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}

	public function attended($presenterUserSectionId)
	{
		try {
			// find all surveys where reviewer_user_section_id = $presenterUserSectionId
			$db = Zend_Db_Table::getDefaultAdapter();
			$sql = "SELECT count(*) as survey_count FROM survey WHERE reviewer_user_section_id = $presenterUserSectionId";
			$sth = $db->prepare($sql);
			if($sth->execute()) {
				$result = $sth->fetch(PDO::FETCH_ASSOC);	
				return $result['survey_count'];
				//return $sth->rowCount();
			} else {
				echo Zend_Debug::dump($this->errorInfo());
			}
		} catch(Exception $e) {
			echo $e->errorMessage();
		}
		
	}

	public function finalScore($facultyAverages, $studentAverages, $attendanceScore, $facPrepAvg, $facProfAvg)
	{
		$presRawScore = $this->_presRawScore($facultyAverages, $studentAverages);
		$presScore = $this->_presScore($presRawScore);
		$prepScore = $this->_prepScore($facPrepAvg);	
		$number = round(((0.5 * $presScore) + (0.35 * $prepScore) + (0.05 * $facProfAvg) + (0.1 * $attendanceScore)), self::PRECISION);

		//error_log("$number = round((0.5 * $presScore) + (0.35 * $prepScore) + (0.05 * $facProfAvg) + (0.1 * $attendanceScore), " . self::PRECISION . ")");

		$letter = $this->letterGrade($number);
		
		return array('number' => $number, 'letter' => $letter);
	}	

	private function _prepScore($rawScore)
	{
		return ($rawScore/self::PREP_SCORE_MAX) * 100;
	}

	private function _presRawScore($facultyAverages, $studentAverages)
	{
		$facWeightedAvg = 0;
		foreach($facultyAverages as $field) {
			$facWeightedAvg += $field['weight'] * $field['average'];	
		}
		
		$studWeightedAvg = 0;
		foreach($studentAverages as $field) {
			$studWeightedAvg += $field['weight'] * $field['average'];	
		}

		$prs = (0.75 * $facWeightedAvg) + (0.25 * $studWeightedAvg);

		return $prs;
	}

	private function _presScore($prs)
	{
		switch(true) {
			case($prs >= 6.5):
				return (93 + (7 * ($prs - 6.5)));
				break;

			case(($prs < 6.5) && ($prs >= 5.5)):
				return (90 + (3 * ($prs - 5.5)));
				break;	

			case(($prs < 5.5) && ($prs >= 4.5)):
				return (87 + (3 * ($prs - 4.5)));
				break;		

			case(($prs < 4.5) && ($prs >= 3.5)):
				return (83 + (4 * ($prs - 3.5)));
				break;

			case(($prs < 3.5) && ($prs >= 2.5)):
				return (80 + (3 * ($prs - 2.5)));
				break;

			case(($prs < 2.5) && ($prs >= 1.5)):
				return (77 + (3 * ($prs - 1.5)));
				break;

			case(($prs < 1.5) && ($prs >= 0.5)):
				return (73 + (4 * ($prs - 0.5)));
				break;
		}
	}

	private function letterGrade($value) {
			/*
		Letter Grade Range	
			
		A	 93 - 100
		A-	 90 - 92.9
		B+	 87 - 89.9
		B 	 83 - 86.9
		B-	 80 - 82.9
		C+	 77 - 79.9
		C 	 73 - 76.9
		C-	 70 - 72.9
		D	 65 - 69.9
		E	 < 65
			*/	
		switch(true) {
			case ($value >  93):
				return 'A';
				break;
			case (($value < 92.9) && ($value > 90)):
				return 'A-';
				break;
			case (($value < 89.9) && ($value > 87)):
				return 'B+';
				break;
			case (($value < 86.9) && ($value > 83)):
				return 'B';
				break;

			case (($value < 82.9) && ($value > 80)):
				return 'B-';
				break;

			case (($value < 79.9) && ($value > 77)):
				return 'C+';
				break;

			case (($value < 76.9) && ($value > 73)):
				return 'C';
				break;

			case (($value < 72.9) && ($value > 70)):
				return 'C-';
				break;

			case (($value < 69.9) && ($value > 65)):
				return 'D';
				break;
			case ($value < 65):
				return 'E';
				break;
		}

	}

	
}

