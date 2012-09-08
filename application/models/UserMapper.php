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
			$this->setDbTable('Application_Model_DbTable_Users');
		}
		return $this->_dbTable;
	}

	public function save($data)
	{
		
		$userdata = array(
				'id' => $data['id'],
				'unid'   => $data['unid'],
				'first_name' => $data['first_name'],
				'last_name' => $data['last_name'],
				'email'   => $data['email'],
				'archive'   => $data['archive'],
				'created_date' => date('Y-m-d H:i:s')
				);
		$user = new Application_Model_DbTable_Users();
		if (empty($userdata['id'])) {
			unset($userdata['id']);
			$id = $user->insert($userdata);
			$userRole = new Application_Model_DbTable_UserRole();
			$userRole->insert(array('user_id'=>$id, 'role_id'=>$data['role']));
			
			$newUser = $user->find($id);
			return $newUser;
		} else {
			$user->update($userdata, array('id = ?' => $userdata['id']));
			$userRole = new Application_Model_DbTable_UserRole();
			$select = $userRole->select();
			$select->where('user_id = ?', $userdata['id']);
			
			$roles = $userRole->fetchAll($select);
			if(!empty($roles[0])) {
				$currentRole = $roles[0];
				if($currentRole->role_id !== $data['role']) {
					$currentRole->role_id = $data['role'];
					$currentRole->save();
				}
			} else {
				$userRole->insert(array('user_id'=>$userdata['id'], 'role_id'=>$data['role']));
			}

			
			$updatedUser = $user->find($userdata['id']);
			return $updatedUser;
		}
	}

	public function find($id)
	{
		$result = $this->getDbTable()->find($id);
		if (0 == count($result)) {
			return;
		}
		$user = $result->current();
		$role = new Application_Model_DbTable_Roles();
		$userRole = new Application_Model_DbTable_UserRole();
		$roles = $user->findManyToManyRowset($role, $userRole);
		$userArray = $user->toArray();
		$userArray['roles'] = $roles->toArray();
		//Zend_Debug::dump($roles);exit;
		return $userArray;
		
	}	

	public function fetchAll()
	{
		$resultSet = $this->getDbTable()->fetchAll();
		$users   = array();
		foreach ($resultSet as $user) {
			$role = new Application_Model_DbTable_Roles();
			$userRole = new Application_Model_DbTable_UserRole();
			$roles = $user->findManyToManyRowset($role, $userRole);
			$users[] = $user; 
		}
		return $users;
	}
	
	public function fetchAllJson()
	{
		$resultSet = $this->getDbTable()->fetchAll();
		$users   = array();
		foreach ($resultSet as $user) {
			$role = new Application_Model_DbTable_Roles();
			$userRole = new Application_Model_DbTable_UserRole();
			$roles = $user->findManyToManyRowset($role, $userRole);
	
			$userArray = $user->toArray();
			$userArray['roles'] = $roles->toArray();
			$users[] = $userArray;
		}
		return json_encode($users);
	}
}

