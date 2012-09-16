<?php

class Application_Model_DbTable_Seminar extends Zend_Db_Table_Row_Abstract
{

    protected $_name = 'seminar';
	protected $_tableClass = 'Application_Model_DbTable_Seminars';
	protected $_dependentTables = array('Application_Model_DbTable_Survey', 'Application_Model_DbTable_Score');
	protected $_primary = array('id');

	protected $_referenceMap = array(
		'UserSection' => array(
			'columns' => array('presenter_user_section_id'),
			'refTableClass' => 'Application_Model_DbTable_UserSection',
			'refColumns' => array('id')
		)
	);

	protected $_id;
	protected $_date;
	protected $_presenter_user_section_id;

	public function setId($id)
	{
		$this->_id = (int) $id;
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}		

	public function setDate($date)
	{
		$this->_date = (int) $date;
		return $this;
	}

	public function getDate()
	{
		return $this->_date;
	}		

	public function setPresenterUserSectionId($presenter_user_section_id)
	{
		$this->_presenter_user_section_id = (int) $presenter_user_section_id;
		return $this;
	}

	public function getPresenterUserSectionId()
	{
		return $this->_presenter_user_section_id;
	}		


}

