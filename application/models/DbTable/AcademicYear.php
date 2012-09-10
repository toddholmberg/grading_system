<?php

class Application_Model_DbTable_AcademicYear extends Zend_Db_Table_Row_Abstract
{
    protected $_name = 'academic_year';
	protected $_tableClass = 'Application_Model_DbTable_AcademicYears';
	protected $_dependentTables = array(
		'Application_Model_DbTable_AcademicYearPYearSection'
	);

	protected $_id;
	protected $_year;
	protected $_current;

	public function setId($id)
	{
		$this->_id = $id;	
	}
	
	public function getId()
	{
		return $this->_id;
	}

	public function setYear($year)
	{
		$this->_year = $year;	
	}
	
	public function getYear()
	{
		return $this->_year;
	}	

	public function setCurrent($current)
	{
		$this->_current = $current;	
	}
	
	public function getCurrent()
	{
		return $this->_current;
	}
}

