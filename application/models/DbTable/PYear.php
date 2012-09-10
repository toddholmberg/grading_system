<?php

class Application_Model_DbTable_PYear extends Zend_Db_Table_Row_Abstract
{

    protected $_name = 'p_year';
	protected $_tableClass = 'Application_Model_DbTable_PYears';
	protected $_dependentTables = array(
			'Application_Model_DbTable_AcademicYearPYearSection'
			);

	protected $_id;
	protected $_p;

	public function setId($id)
	{
		$this->_id = $id;	
	}

	public function getId()
	{
		return $this->_id;
	}

	public function setP($p)
	{
		$this->_p = $p;	
	}

	public function getP()
	{
		return $this->_p;
	}	

}

