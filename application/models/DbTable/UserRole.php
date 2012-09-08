<?php

class Application_Model_DbTable_UserRole extends Zend_Db_Table_Abstract
{

    protected $_name = 'user__role';
	protected $_primary = array('id');

	protected $_id;
	protected $_user_id;
	protected $_role_id;
	
	protected $_referenceMap = array(
		'Application_Model_DbTable_User' => array(
			'columns' => array('user_id'),
			'refTableClass' => 'Application_Model_DbTable_Users',
			'refColumns' => array('id')
		),
		'Application_Model_DbTable_Role' => array(
			'columns' => array('role_id'),
			'refTableClass' => 'Application_Model_DbTable_Roles',
			'refColumns' => array('id')
		)
	);

	public function setId($id)
	{
		$this->_id = (int) $id;
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function setUserId($title)
	{
		$this->_user_id = $title;
		return $this;
	}

	public function getUserId()
	{
		return $this->_user_id;
	}

	public function setRoleId($title)
	{
		$this->_role_id = $title;
		return $this;
	}

	public function getRoleId()
	{
		return $this->_role_id;
	}
}

