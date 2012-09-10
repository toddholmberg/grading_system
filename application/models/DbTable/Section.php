<?php

class Application_Model_DbTable_Section extends Zend_Db_Table_Row_Abstract
{
    protected $_name = 'section';
	protected $_tableClass = 'Application_Model_DbTable_Sections';
	protected $_dependentTables = array(
		'Application_Model_DbTable_AcademicYearPYearSection'
	);

	protected $_id;
	protected $_number;

	public function setId($id)
	{
		$this->_id = $id;	
	}
	
	public function getId()
	{
		return $this->_id;
	}

	public function setNumber($number)
	{
		$this->_number = $number;	
	}
	
	public function getNumber()
	{
		return $this->_number;
	}	
}

