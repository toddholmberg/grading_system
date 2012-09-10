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

	public function findAcademicYearPYearSectionId(Array $sectionData)
	{
		try {
			//get current academic year
			//$academicYears = new Application_Model_DbTable_AcademicYears();
			//$currentAcademicYear = $academicYears->getCurrentAcademicYear();

			$apsTable = new Application_Model_DbTable_AcademicYearPYearSections();
			$where = array(
					'academic_year_id = ?' => $sectionData['academic_year_id'],
					'p_year_id = ?' => $sectionData['p_year_id'],
					'section_id = ?' => $sectionData['section_id']
					);
			$apsRows = $apsTable->fetchAll($where);
			$apsRow = $apsRows->current()->toArray();
			return $apsRow;

		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}

	public function saveUserSection(Array $userSectionData) {
		try {
			$userSection = new Application_Model_DbTable_UserSection();
			$userSection->insert(array(
						'user_id'=>$userSectionData['user_id'],
						'section_id'=>$userSectionData['section_id']
						));
		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		}

	}
}

