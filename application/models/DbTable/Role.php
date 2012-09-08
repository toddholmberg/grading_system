<?php

class Application_Model_DbTable_Role extends Zend_Db_Table_Row_Abstract
{

    protected $_name = 'role';
	protected $_primary = array('id');

	protected $_tableClass = 'Application_Model_DbTable_Role';
	protected $_dependentTables = array('Application_Model_DbTable_UserRole');

	protected $_id;
	protected $_title;

	public function setId($id)
	{
		$this->_id = (int) $id;
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function setTitle($title)
	{
		$this->_title = $title;
		return $this;
	}

	public function getTitle()
	{
		return $this->_title;
	}

}

