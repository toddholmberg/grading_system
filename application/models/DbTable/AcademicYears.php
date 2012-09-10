<?php

class Application_Model_DbTable_AcademicYears extends Zend_Db_Table_Abstract
{

	protected $_name = 'academic_year';
	protected $_rowClass = 'Application_Model_DbTable_AcademicYear';



	public function getAcademicYears() {
		$row = $this->fetchAll();
		if(!$row) {
			throw new Exception('No academic years');
		}
		return $row->toArray();
	}

	public function getCurrentAcademicYear() {
		$where = array('current = ?' => '1');
		$row = $this->fetchAll($where);
		if(!$row) {
			throw new Exception('No academic years');
		}
		return $row->current()->toArray();
	}	

}
