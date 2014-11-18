<?php

class Application_Model_SectionMapper extends Application_Model_MapperAbstract
{
	
	const DEFAULT_ATTENDANCE_COUNT = 12;

	public function save()
	{
	}

	public function buildSections($academicYearId)
	{

		$sectionTable = new Application_Model_DbTable_Sections();
		$sections = $sectionTable->fetchAll()->toArray();

		$pYearTable = new Application_Model_DbTable_PYears();
		$pYears = $pYearTable->fetchAll()->toArray();

		$apsData = array();
		foreach ($pYears as $item => $p) {
			$valueArray = array();
			if (in_array($p['p'], array(3,4))) {
				foreach($sections as $item => $section) {
					$valueArray['academic_year_id'] = $academicYearId;
					$valueArray['p_year_id'] = $p['id'];
					$valueArray['section_id'] = $section['id'];
					$apsData[] = $valueArray;

				}
			}
		}

		$apsTable = new Application_Model_DbTable_AcademicYearPYearSections();
		foreach($apsData as $aps) {
			$newRow = $apsTable->createRow();
			$newRow->setFromArray($aps);
			$newRow->save();
		}

		$sectionMap = $this->getSectionsByAcademicYearId($academicYearId, 'object');

		$this->saveSectionConfiguration(
			$sectionMap,
			array(
				1 => self::DEFAULT_ATTENDANCE_COUNT,
				2 => self::DEFAULT_ATTENDANCE_COUNT,
				3 => self::DEFAULT_ATTENDANCE_COUNT,
				4 => self::DEFAULT_ATTENDANCE_COUNT,
				5 => self::DEFAULT_ATTENDANCE_COUNT
				)
			);

		return $sectionMap;
	}

	public function saveSectionConfiguration($sectionMap, $attendanceData = null)
	{
		//error_log(print_r($sectionMap, true));	
		//error_log(print_r($attendanceData, true));

		// delete any existing config rows for the sections
		$this->_deleteSectionConfiguration($sectionMap);

		// insert the new/updated section configuration
		$sectionConfigTable = new Application_Model_DbTable_SectionConfig();
		foreach($sectionMap as $key => $section) {
			$sectionConfigData = array(
					'section_id' => $section->id,
					'attendance_count' => $attendanceData[$section->section_id]
				);
			$sectionConfigTable->insert($sectionConfigData);
		}

	}

	private function _deleteSectionConfiguration($sectionMap)
	{
		$sectionConfigTable = new Application_Model_DbTable_SectionConfig();
		foreach ($sectionMap as $key => $section) {
			$where = $sectionConfigTable->getAdapter()->quoteInto('section_id = ?', $section->id);
			$sectionConfigTable->delete($where);
		}
	}

	public function getSectionsByAcademicYearId($academicYearId, $type = 'array')
	{
		$apsTable = new Application_Model_DbTable_AcademicYearPYearSections();
		$where = array('academic_year_id = ?' => $academicYearId);
		switch ($type) {
			case 'array':
				return $apsTable->fetchAll($where)->toArray();
				break;

			case 'object':
				return $apsTable->fetchAll($where);
				break;
		}
	}

	public function findSectionIds($academicYearId, $pYearId, $sectionId, $roleId)
	{
		try {
			//get current academic year
			//$academicYears = new Application_Model_DbTable_AcademicYears();
			//$currentAcademicYear = $academicYears->getCurrentAcademicYear();
			$roleMapper = new Application_Model_RoleMapper();
			$role = new Application_Model_DbTable_Role();
			$roleMapper->find($roleId, $role);

			switch($role->getTitle()) {
				case('Faculty'):
					$apsTable = new Application_Model_DbTable_AcademicYearPYearSections();
					$where = array(
							'academic_year_id = ?' => $academicYearId,
							'section_id = ?' => $sectionId
							);
					$apsRows = $apsTable->fetchAll($where);
					return $apsRows->toArray();
				
					break;

				case('Student'):
					$apsTable = new Application_Model_DbTable_AcademicYearPYearSections();
					$where = array(
							'academic_year_id = ?' => $academicYearId,
							'p_year_id = ?' => $pYearId,
							'section_id = ?' => $sectionId
							);
					$apsRows = $apsTable->fetchAll($where);
					return $apsRows->toArray();

					break;

			}
		} catch(Exception $e) {
			throw new Exception("SectionMapper::findSectionIds: " . $e->getMessage());
		}
	}

	/**
	 * Grabs the basic section information for a single section.
	 * Does not retrieve user_section data. Sorry for the confusion, if any.
	 * 		
	 * @param  integer $number Section number 1 - 5.
	 * @return array
	 */
	public function findSectionDataByNumber($number) {
		$table = new Application_Model_DbTable_Sections();
		$where = array('number = ?' => $number);
		$row = $table->fetchRow($where);
		if(!empty($row)) {
			return $row->toArray();
		} else {
			return false;
		}
	}

	// DEPRECATED
	public function findAcademicYearPYearSectionId($academic_year_id, $p_year_id, $section_id)
	{
		try {
			//get current academic year
			//$academicYears = new Application_Model_DbTable_AcademicYears();
			//$currentAcademicYear = $academicYears->getCurrentAcademicYear();

			$apsTable = new Application_Model_DbTable_AcademicYearPYearSections();
			$where = array(
					'academic_year_id = ?' => $academic_year_id,
					'p_year_id = ?' => $p_year_id,
					'section_id = ?' => $section_id
					);
			$apsRows = $apsTable->fetchAll($where);
			if($apsRows->current() !== NULL) {
				$apsRow = $apsRows->current()->toArray();
			} else {
				return false;
			}
			return $apsRow;

		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	public function saveUserSections($userId, $sectionArray, $isGrader) 
	{
		//error_log(print_r($sectionArray, true));
		try {
			$userSection = new Application_Model_DbTable_UserSection();
			foreach($sectionArray as $section) {
				$userSection->insert(array(
							'user_id'=>$userId,
							'section_id'=>$section['id'],
							'is_grader'=>$isGrader
							));
			}
		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		}

	}



	public function saveUserSection($user_id, $section_id, $isGrader, $roleId) 
	{
		try {
			$userSection = new Application_Model_DbTable_UserSection();
			$userSection->insert(array(
						'user_id'=>$user_id,
						'section_id'=>$section_id,
						'is_grader'=>$isGrader
						));
		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		}

	}

	public function getUserCurrentSections($user_id, $currentAcademicYearId = null)
	{
		try {
			if(empty($currentAcademicYearId)) {
				// get current academic year
				$academicYears = new Application_Model_DbTable_AcademicYears();
				$currentAcademicYear = $academicYears->getCurrentAcademicYear();
				$currentAcademicYearId = $currentAcademicYear['id'];
			}

			$db = Zend_Db_Table::getDefaultAdapter();
			$sql = 'select aps.id, aps.academic_year_id, aps.p_year_id, aps.section_id from academic_year__p_year__section aps left join academic_year ay on ay.id = aps.academic_year_id left join user__section us on us.section_id = aps.id where aps.academic_year_id = :academic_year_id and us.user_id = :user_id';
			$sth = $db->prepare($sql);
			$sth->bindParam(':user_id', $user_id);
			$sth->bindParam(':academic_year_id', $currentAcademicYearId);
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

	public function getUserCurrentSectionMapId($user_id, $currentAcademicYearId = null)
	{
		try {
			if(empty($currentAcademicYearId)) {
				// get current academic year
				$academicYears = new Application_Model_DbTable_AcademicYears();
				$currentAcademicYear = $academicYears->getCurrentAcademicYear();
				$currentAcademicYearId = $currentAcademicYear['id'];
			}

			$db = Zend_Db_Table::getDefaultAdapter();
			$sql = 'select aps.id, aps.academic_year_id, aps.p_year_id, aps.section_id from academic_year__p_year__section aps left join academic_year ay on ay.id = aps.academic_year_id left join user__section us on us.section_id = aps.id where aps.academic_year_id = :academic_year_id and us.user_id = :user_id';
			$sth = $db->prepare($sql);
			$sth->bindParam(':user_id', $user_id);
			$sth->bindParam(':academic_year_id', $currentAcademicYearId);
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


	public function compareUserSections($current, $new)
	{
		$currentIds = array();
		foreach($current as $section) {
			$currentIds[] = $section['id'];
		}
		$newIds = array();
		foreach($new as $section) {
			$newIds[] = $section['id'];
		}	

		return array_diff($newIds, $currentIds);
	}

	public function updateUserSection($userId, $sectionArray, $isGrader, $academicYearId)
	{
		try {
			//echo "updateUserSection: params: $userId, $academicYearId, $newSectionId<br/>";

			//$currentUserSectionMap = $this->getUserCurrentSectionMapId($userId, $academicYearId);
			$currentUserSections = $this->getUserCurrentSections($userId, $academicYearId);

			if(!empty($currentUserSections)) {
				$sectionChanges = $this->compareUserSections($currentUserSections, $sectionArray);
			}

			$userSectionTable = new Application_Model_DbTable_UserSection();

			switch(true) {

				case(empty($currentUserSections)):
					// Commented out grader validation. Allowing multiple graders.
					/*if($isGrader == 1) {
						$this->validateGrader($userId, $sectionArray);
					}*/	

					$this->saveUserSections($userId, $sectionArray, $isGrader);
					break;

				case(!empty($sectionChanges)):
					// Commented out grader validation. Allowing multiple graders.
					/*if($isGrader == 1) {
						$this->validateGrader($userId, $sectionArray);
					}*/

					// change new sections
					for($i = 0; $i < count($currentUserSections); $i++) {
						$userSectionRow = $userSectionTable->fetchRow(
								$userSectionTable->select()
								->where('user_id = ?', $userId)
								->where('section_id = ?', $currentUserSections[$i]['id'])
								);
		
						$userSectionRow->section_id = $sectionArray[$i]['id'];	

						if($userSectionRow->is_grader != $isGrader) {
							$userSectionRow->is_grader = $isGrader;

							// remove scores if no longer grader for section
							if(($userSectionRow->is_grader == 1) && ($isGrader == 0)) {
								$scoreTable = new Application_Model_DbTable_Scores();
								$scoreWhere = $scoreTable->getAdapter()->quoteInto('grader_user_id = ?', $userId);
								$scoreTable->delete($scoreWhere);
							}
						}	

						$userSectionRow->save();
				
					}
					break;

				default:
					if($isGrader == 1) {
						$this->validateGrader($userId, $currentUserSections);
					}

					foreach($currentUserSections as $section) {
						$userSectionRow = $userSectionTable->fetchRow(
								$userSectionTable->select()
								->where('user_id = ?', $userId)
								->where('section_id = ?', $section['id'])
								);

						if($userSectionRow->is_grader != $isGrader) {
							// remove scores if no longer grader for section
							if(($userSectionRow->is_grader == 1) && ($isGrader == 0)) {
								//error_log("kill scorer");
								$scoreTable = new Application_Model_DbTable_Scores();
								$scoreWhere = $scoreTable->getAdapter()->quoteInto('grader_user_id = ?', $userId);
								$scoreTable->delete($scoreWhere);
							}	

							$userSectionRow->is_grader = $isGrader;
							$userSectionRow->save();

							

						}

					}

					break;

			}

		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		}
	}	

	// TO-DO: change this when updating faculty to not require a year
	public function updateGraderStatus($isGrader, $userId)
	{
		try {
			$userSectionTable = new Application_Model_DbTable_UserSection();
			$data = array(
					'is_grader' => $isGrader
					);
			$where = $userSectionTable->getAdapter()->quoteInto('user_id = ?', $userId);
			$userSectionTable->update($data, $where);

			// If set to 0m then delete any scores from the database
			if($isGrader == 0) {
				$scoreTable = new Application_Model_DbTable_Scores();
				$scoreWhere = $scoreTable->getAdapter()->quoteInto('grader_user_id = ?', $userId);
				$scoreTable->delete($scoreWhere);
			}
		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		}	
	}

	/**
	 * DEPRECATED. Throw an exception if there is more than one grader. 
	 * @param  integer $userId
	 * @param  array $sectionArray
	 * @return void 
	 */
	public function validateGrader($userId, $sectionArray)
	{
		try {
			$userSectionTable = new Application_Model_DbTable_UserSection();
			$currentGraders = array();
			foreach($sectionArray as $section) {
				$rowset = $userSectionTable->fetchAll(
						$userSectionTable->select()
						->where('section_id = ?', $section['id'])
						->where('is_grader = ?', 1)
						);
				foreach($rowset as $row) {
					$currentGraders[] = $row->toArray();
				}
			}

			if(!empty($currentGraders)) {
				foreach($currentGraders as $grader) {
					if($grader['user_id'] != $userId) {
						$userTable = new Application_Model_DbTable_Users();
						$grader = $userTable->find($grader['user_id']);
						throw new Exception("There is already 1 faculty grader in that section: " . $grader[0]->first_name . " " . $grader[0]->last_name . "<br/>");
					}
				}
			} 
		} catch(Exception $e) {
			echo 'SectionMapper::validateGrader(): ' . $e->getMessage();
		}
	}

	public function getUserSectionData($userId)
	{
		$currentUserSectionMap = $this->getUserCurrentSectionMapId($userId);
		$table = new Application_Model_DbTable_UserSection();
		$select = $table->select()
			->where('user_id = ?', $userId)
			->where('section_id = ?', $currentUserSectionMap['id']);
		$row = $table->fetchRow($select);
		if(!empty($row)) {
			return $row;
		} else {
			return false;
		}
		
	}

	public function getUserSectionDataByUnid($unid)
	{
		$userMapper = new Application_Model_UserMapper();
		$user = $userMapper->findUserByUnid($unid);

		$currentUserSectionMap = $this->getUserCurrentSectionMapId($user['id']);
		$table = new Application_Model_DbTable_UserSection();
		$select = $table->select()
			->where('user_id = ?', $user['id'])
			->where('section_id = ?', $currentUserSectionMap['id']);
		$row = $table->fetchRow($select);
		if(!empty($row)) {
			return $row;
		} else {
			return false;
		}
		
	}

	public function getUserSectionGraders($userSectionId)
	{
		//error_log("USER SECTION ID: $userSectionId");
		$table = new Application_Model_DbTable_UserSection();
		$select = $table->select()
			->where('id = ?', $userSectionId);
		$userRow = $table->fetchRow($select);
		//error_log(print_r($userRow->toArray(), true));

		// now get grader rows
		$graderSelect = $table->select()
			->where('is_grader = ?', 1)
			->where('section_id = ?', $userRow->section_id);
		$graders = $table->fetchAll($graderSelect);

		if(!empty($graders)) {
			return $graders->toArray();
		} else {
			return false;
		}

	}

	public function getSectionConfig($sectionId)
	{
		$table = new Application_Model_DbTable_SectionConfig();
		$select = $table->select()->where('section_id = ?', $sectionId);
		$row = $table->fetchRow($select);
		if(!empty($row)) {
			return $row;
		} else {
			return false;
		}
	
	}	

}

