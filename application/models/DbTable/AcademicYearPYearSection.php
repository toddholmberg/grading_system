<?php

class Application_Model_DbTable_AcademicYearPYearSection extends Zend_Db_Table_Row_Abstract
{
    protected $_name = 'academic_year__p_year__section';
	protected $_tableClass = 'Application_Model_DbTable_AcademicYearPYearSections';

	protected $_referenceMap = array(
			'AcademicYear' => array(
				'columns' => array('academic_year_id'),
				'refTableClass' => 'Application_Model_DbTable_AcademicYears',
				'refColumns' => array('id')
				),
			'PYear' => array(
				'columns' => array('p_year_id'),
				'refTableClass' => 'Application_Model_DbTable_PYears',
				'refColumns' => array('id')
				),
			'Section' => array(
				'columns' => array('section_id'),
				'refTableClass' => 'Application_Model_DbTable_Sections',
				'refColumns' => array('id')
				)
			);



	protected $_id;
	protected $_academic_year_id;
	protected $_p_year_id;
	protected $_section_id;

	public function setId($id)
	{
		$this->_id = $id;	
	}
	
	public function getId()
	{
		return $this->_id;
	}

	public function setAcademicYearId($academic_year_id)
	{
		$this->_academic_year_id = $academic_year_id;	
	}
	
	public function getAcademicYearId()
	{
		return $this->_academic_year_id;
	}

	public function setPYearId($p_year_id)
	{
		$this->_p_year_id = $p_year_id;	
	}
	
	public function getPYearId()
	{
		return $this->_p_year_id;
	}

	public function setSectionId($section_id)
	{
		$this->_section_id = $section_id;	
	}
	
	public function getSectionId()
	{
		return $this->_section_id;
	}
	
}

