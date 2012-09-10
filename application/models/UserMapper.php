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
		print_r($data);
		$userData = array(
				'id' => $data['id'],
				'unid'   => $data['unid'],
				'first_name' => $data['first_name'],
				'last_name' => $data['last_name'],
				'email'   => $data['email'],
				'archive'   => $data['archive'],
				'created_date' => date('Y-m-d H:i:s')
				);
		$roleData = array(
			'role_id' => $data['role']
		);
		$sectionData = array(
			'p_year_id'=> $data['p_year'],
			'section_id' => $data['section']
		);
		if (empty($userData['id'])) {
			$newUser = $this->insert($userData, $roleData, $sectionData);
			return $newUser;
		} else {
			$updatedUser = $this->update($userData, $roleData, $sectionData);
			return $updatedUser;
		}
	}


	public function insert($userData, $roleData, $sectionData = null)
	{
		try {
			// drop 'id' field from userData array
			unset($userData['id']);

			// begin transaction
			$this->getDbTable()->getDefaultAdapter()->beginTransaction();	

			// insert new user
			$user = new Application_Model_DbTable_Users();
			$user_id = $user->insert($userData);

			// map user role
			$userRole = new Application_Model_DbTable_UserRole();
			$userRole->insert(array(
				'user_id'=>$user_id,
				'role_id'=>$roleData['role_id']
			));

			// map user to current section
			// get current academic year
			$academicYears = new Application_Model_DbTable_AcademicYears();
			$currentAcademicYear = $academicYears->getCurrentAcademicYear();
			
			$sectionMapper = new Application_Model_SectionMapper();
			$sectionToBeMapped = $sectionMapper->findAcademicYearPYearSectionId(array(
				'academic_year_id' => $currentAcademicYear['id'],
				'p_year_id' => $sectionData['p_year_id'],
				'section_id' => $sectionData['section_id']
			));			
			$sectionMapper->saveUserSection(array(
				'user_id' => $userData['id'],
				'section_id' => $sectionToBeMapped['id']
			));	

			// commit the transaction
			$this->getDbTable()->getDefaultAdapter()->commit();	

			// get saved user data
			$newUser = $user->find($userData['id']);

			return $newUser;		
		}
		catch(Exception $e) {
			echo $e->getMessage();
			$this->getDbTable()->getDefaultAdapter()->rollback();
		}
	}


	public function update($userdata)
	{
		$user = new Application_Model_DbTable_Users();
		$user->update($userdata, array('id = ?' => $userdata['id']));
		// update role
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

	public function find($id)
	{
		$result = $this->getDbTable()->find($id);
		if (0 == count($result)) {
			return;
		}
		$user = $result->current();

		// get role
		$role = new Application_Model_DbTable_Roles();
		$userRole = new Application_Model_DbTable_UserRole();
		$roles = $user->findManyToManyRowset($role, $userRole)->toArray();

		// add related data
		$user = $user->toArray();
		$user['role'] = $roles[0]['id'];

		// get current section
		
		return $user;
		
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

