<?php

class Application_Model_RoleMapper
{
	protected $_dbTable;

	public function setDbTable($dbTable)
	{
		if (is_string($dbTable)) {
			$dbTable = new $dbTable();
		}
		if (!$dbTable instanceof Zend_Db_Table_Abstract) {
			throw new Exception('Invalid table data gateway provided');
		}
		$this->_dbTable = $dbTable;
		return $this;
	}

	public function getDbTable()
	{
		if (null === $this->_dbTable) {
			$this->setDbTable('Application_Model_DbTable_Roles');
		}
		return $this->_dbTable;
	}

	public function find($id, Application_Model_DbTable_Role $role)
	{
		$result = $this->getDbTable()->find($id);
		if (0 == count($result)) {
			return;
		}
		$row = $result->current();
		$role->setId($row->id)
			->setTitle($row->title);
	}

	public function findByTitle($title)
	{
		try {
			$table = $this->getDbTable();
			$where = array('title = ?' => ucfirst($title));
			$row = $table->fetchRow($where);
			if(!empty($row)) {
				return $row->toArray();
			} else {
				return false;
			}
		} catch(Exception $e) {
			echo $e->getMessage();
		}
	
	}


	public function fetchAll()
	{
		$resultSet = $this->getDbTable()->fetchAll();
		$roles = array();
		foreach ($resultSet as $row) {
			$roles[$row->id] = $row->title;
		}
		return $roles;
	}

}

