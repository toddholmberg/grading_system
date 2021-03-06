<?php

class Application_Model_DbTable_User extends Zend_Db_Table_Row_Abstract
{

	protected $_name = 'user';
	protected $_tableClass = 'Application_Model_DbTable_Users';
	protected $_dependentTables = array('Application_Model_DbTable_UserRole');
	protected $_primary = array('id');

	protected $_id;
	protected $_unid;
	protected $_first_name;
	protected $_last_name;
	protected $_email;
	protected $_created_date;
	protected $_archive;

	public function setId($id)
	{
		$this->_id = (int) $id;
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function setUnid($unid)
	{
		$this->_unid = $unid;
		return $this;
	}

	public function getUnid()
	{
		return $this->_unid;
	}

	public function setFirstName($first_name)
	{
		$this->_first_name = $first_name;
		return $this;
	}

	public function getFirstName()
	{
		return $this->_first_name;
	}

	public function setLastName($last_name)
	{
		$this->_last_name = $last_name;
		return $this;
	}

	public function getLastName()
	{
		return $this->_last_name;
	}

	public function setEmail($email)
	{
		$this->_email = $email;
		return $this;
	}

	public function getEmail()
	{
		return $this->_email;
	}

	public function setCreatedDate($ts = null)
	{
		if(isset($ts)) {
			$this->_created_date = $ts;
		} else {
			$this->_created_date = time();

		}
		return $this;
	}

	public function getCreatedDate()
	{
		return $this->_created_date;
	}

	public function setArchive()
	{
		$this->_archive = ($archive);
		return $this;
	}

	public function getArchive()
	{
		return $this->_archive;
	}


}

