<?php

class Application_Model_DbTable_UserSection extends Zend_Db_Table_Abstract
{

    protected $_name = 'user__section';
	protected $_primary = array('id');

	protected $_dependentTables = array('Application_Model_DbTable_Survey', 'Application_Model_DbTable_Seminar');

	protected $_referenceMap = array(
		'User' => array(
			'columns' => array('user_id'),
			'refTableClass' => 'Application_Model_DbTable_Users',
			'refColumns' => array('id')
		),
		'Section' => array(
			'columns' => array('section_id'),
			'refTableClass' => 'Application_Model_DbTable_Sections',
			'refColumns' => array('id')
		)
	);

	protected $_id;
	protected $_user_id;
	protected $_section_id;
	
	public function setId($id)
	{
		$this->_id = (int) $id;
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function setUserId($id)
	{
		$this->_user_id = $id;
		return $this;
	}

	public function getUserId()
	{
		return $this->_user_id;
	}

	public function setSectionId($id)
	{
		$this->_section_id = $id;
		return $this;
	}

	public function getSectionId()
	{
		return $this->_section_id;
	}
}

