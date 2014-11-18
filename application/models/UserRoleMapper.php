<?php

class Application_Model_UserRoleMapper
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
			$this->setDbTable('Application_Model_DbTable_UserRole');
		}
		return $this->_dbTable;
	}

	public function save(Application_Model_DbTable_UserRole $userrole)
	{
		$data = array(
				'user_id'   => $userrole->getUserId(),
				'role_id'   => $userrole->getRoleId()
				);

		if (null === ($id = $userrole->getId())) {
			unset($data['id']);
			$this->getDbTable()->insert($data);
		} else {
			$this->getDbTable()->update($data, array('id = ?' => $id));
		}
	}

	public function find($id, Application_Model_DbTable_UserRole $userrole)
	{
		$result = $this->getDbTable()->find($id);
		if (0 == count($result)) {
			return;
		}
		$row = $result->current();
		$userrole->setId($row->id)
			->setUserId($row->user_id)
			->setRoleId($row->role_id);
	}

	public function fetchAll()
	{
		$resultSet = $this->getDbTable()->fetchAll();
		$userroles = array();
		foreach ($resultSet as $row) {
			$userrole = new Application_Model_DbTable_UserRole();
			$userrole->setId($row->id)
				->setUserId($row->user_id)
				->setRoleId($row->role_id);
			$userroles[] = $userrole;
		}
		return $userroles;
	}

	public function  getUserRoleFromUserId($userId)
	{
		try {
			$table = $this->getDbTable();
			$select = $table->select()
				->where('user_id = ?', $userId);
			$row = $table->fetchRow($select);
			if(!empty($row)) {
				return $row->toArray();
			} else {
				throw false;
			}
		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		}
	}	


}

