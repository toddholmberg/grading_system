<?php

class Application_Model_SectionMapper extends Application_Model_MapperAbstract
{
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
		
		$sectionMap = $this->getSectionsByAcademicYearId($academicYearId);
		return $sectionMap;
	}

	public function getSectionsByAcademicYearId($academicYearId)
	{
		$apsTable = new Application_Model_DbTable_AcademicYearPYearSections();
		$where = array('academic_year_id = ?' => $academicYearId);
		$apsArray = $apsTable->fetchAll($where)->toArray();
		return $apsArray;

	} 

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

	public function saveUserSection($user_id, $section_id) 
	{
		try {
			$userSection = new Application_Model_DbTable_UserSection();
			$userSection->insert(array(
						'user_id'=>$user_id,
						'section_id'=>$section_id
						));
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

			/*
select aps.id from academic_year__p_year__section aps left join academic_year ay on ay.id = aps.academic_year_id left join user__section us on us.section_id = aps.id where aps.academic_year_id = 1 and us.user_id = 150
			*/
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

	public function updateUserSection($user_id, $academic_year_id, $newSectionId)
	{
		try {
			//echo "updateUserSection: params: $user_id, $academic_year_id, $newSectionId<br/>";

			$currentUserSectionMap = $this->getUserCurrentSectionMapId($user_id, $academic_year_id);

			$userSectionTable = new Application_Model_DbTable_UserSection();

			if(isset($currentUserSectionMap['id'])) {
				// return if no change in section
				if($currentUserSectionMap['id'] == $newSectionId) {
					return;
				}
				// update current section
				$userSectionRow = $userSectionTable->fetchRow(
						$userSectionTable->select()
						->where('user_id = ?', $user_id)
						->where('section_id = ?', $currentUserSectionMap['id'])
						);

				$userSectionRow->section_id = $newSectionId;
				$userSectionRow->save();
			} else {
				// add section for current academic year	
				$this->saveUserSection($user_id, $newSectionId);
			}
		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		}
	}	

}

