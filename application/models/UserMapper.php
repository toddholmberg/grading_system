<?php

class Application_Model_UserMapper
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
			$this->setDbTable('Application_Model_DbTable_User');
		}
		Zend_Debug::Dump($this->_dbTable);
		return $this->_dbTable;
	}

	public function save(Application_Model_User $user)
	{
		$data = array(
				'unid'   => $user->getUnid(),
				'first_name' => $user->getFirstName(),
				'last_name' => $user->getLastName(),
				'email'   => $user->getEmail(),
				'created_date' => date('Y-m-d H:i:s'),
				);

		if (null === ($id = $user->getId())) {
			unset($data['id']);
			$this->getDbTable()->insert($data);
		} else {
			$this->getDbTable()->update($data, array('id = ?' => $id));
		}
	}

	public function find($id, Application_Model_User $user)
	{
		$result = $this->getDbTable()->find($id);
		if (0 == count($result)) {
			return;
		}
		$row = $result->current();
		$user->setId($row->id)
			->setFirstName($row->first_name)
			->setLastName($row->last_name)
			->setEmail($row->email)
			->setCreatedDate($row->created_date);
	}

	public function fetchAll()
	{
		$resultSet = $this->getDbTable()->fetchAll();
		Zend_Debug::dump($resultSet);
		$entries   = array();
		foreach ($resultSet as $row) {
			$user = new Application_Model_User();
			$user->setId($row->id)
				->setFirstName($row->first_name)
				->setLastName($row->last_name)
				->setEmail($row->email)
				->setCreatedDate($row->created_date);
			$users[] = $users;
		}
		Zend_Debug::dump($users);
		return $users;
	}

}

