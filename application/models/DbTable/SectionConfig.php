<?php

class Application_Model_DbTable_SectionConfig extends Zend_Db_Table_Abstract
{
    protected $_name = 'section_config';
	//protected $_tableClass = 'Application_Model_DbTable_SectionConfigs';

	protected $_id;
	protected $_section_id;
	protected $attendance_count;

	public function setId($id)
	{
		$this->_id = $id;	
	}
	
	public function getId()
	{
		return $this->_id;
	}

	public function setSectionId($section_id)
	{
		$this->_section_id = $section_id;	
	}
	
	public function getSectionId()
	{
		return $this->_section_id;
	}	

	public function setAttendanceCount($attendance_count)
	{
		$this->_attendance_count = $attendance_count;	
	}
	
	public function getAttendanceCount()
	{
		return $this->_attendance_count;
	}	
}

