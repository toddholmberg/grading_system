<?php

class Application_Model_AcademicYearMapper extends Application_Model_MapperAbstract
{
	public function save()
	{
	}


	public function addAcademicYear($formData)
	{
		$table = new Application_Model_DbTable_AcademicYears();
		// unset curent academic year.
		$data = array('year' => $formData['newYear']);
		$table->insert($data);

	}	
	
	public function setCurrentAcademicYear($formData)
	{
		$table = new Application_Model_DbTable_AcademicYears();
		// unset curent academic year.
		$data = array('current' => 0);
		$where = $table->getAdapter()->quoteInto('current = ?', 1);
		$table->update($data, $where);

		// set new current academic year.
		$data = array('current' => 1);
		$where = $table->getAdapter()->quoteInto('id = ?', $formData['year']);
		$table->update($data, $where);

		
	}

	public function getCurrentAcademicYear() {
		$table = new Application_Model_DbTable_AcademicYears();
		$where = array('current = ?' => '1');
		$row = $table->fetchAll($where);
		if(!$row) {
			throw new Exception('No academic years');
		}
		$currentAcademicYear = $row->current();
		if(!empty($currentAcademicYear)) {
			return $row->current()->toArray();
		} else {
			return false;
		}
	}	

	public function findAcademicYear($year) {
		$table = new Application_Model_DbTable_AcademicYears();
		$where = array('year = ?' => $year);
		$row = $table->fetchRow($where);
		if(!empty($row)) {
			return $row->toArray();
		} else {
			return false;
		}
	}

}

